<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\interfaces\ThirdFinanceLogInterface;

use xjryanse\finance\service\FinanceAccountLogService;
use xjryanse\finance\service\FinanceAccountService;
use xjryanse\finance\service\FinanceStatementService;
use xjryanse\finance\service\FinanceIncomePayService;
use xjryanse\system\service\SystemErrorLogService;
use xjryanse\wechat\model\WechatWxPayLog;
use xjryanse\wechat\WxPay\DealOrder;
use xjryanse\wechat\WxPay\v2\WxPayApiXie;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use Exception;
use think\Db;

/**
 * 微信支付记录
 */
class WechatWxPayLogService implements MainModelInterface, ThirdFinanceLogInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWxPayLog';
    //直接执行后续触发动作
    protected static $directAfter = true;

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    return $lists;
                }, true);
    }

    /**
     * 批处理
     */
    public static function dealBatch() {
        $con[] = ['appid', '=', ''];
        $list = self::mainModel()->master()->order('id desc')->where($con)->limit(3)->select();
        Debug::debug('dealOrder待处理列表', $list);
        foreach ($list as &$v) {
            //不开事务无法执行财务数据处理
            try {
                Db::startTrans();
                // self::getInstance($v['id'])->deal();
                // 2022-11-24:尝试跳过incomePay逻辑
                self::getInstance($v['id'])->tearValData();
                Db::commit();
            } catch (\Exception $e) {
                if (self::mainModel()->inTransaction()) {
                    Db::rollback();
                }
                if (Debug::isDebug()) {
                    throw $e;
                }
                SystemErrorLogService::exceptionLog($e);
                self::checkNoTransaction();
            }
        }
    }

    /**
     * 20220301把业务逻辑抽出来
     * 2022-11-24：似乎可以丢弃，包括 FinanceIncomePayService；
     * @return boolean
     * @throws Exception
     */
    public function deal() {
        self::checkTransaction();
        //拆解数据保存
        $this->tearValData();
        //处理订单财务数据
        $financeIncomePayId = DealOrder::dealOrderFinance($this->uuid);
        //处理流程节点数据
        $financeIncomePay = FinanceIncomePayService::getInstance($financeIncomePayId)->get();
        //获取收款单id
        $statementId = $financeIncomePay['income_id'];
        $statementInfo = FinanceStatementService::getInstance($statementId)->get();

        if (!$statementInfo) {
            throw new Exception('未找到' . $statementId . '对应订单号信息');
        }
        return true;
    }

    /**
     * 拆解val数据保存
     */
    public function tearValData() {
        $info = $this->get(MASTER_DATA);
        //val转数组
        $data = json_decode($info['val'], true);
        // 20230902:结构化数据异常，不处理
        if(!Arrays::value($data, 'out_trade_no')){
            $this->update(['appid'=>'false']);
            return false;
        }

        // Debug::debug('拆解数据attach', json_decode($data['attach'], true));
        $data['statement_id'] = Arrays::value(json_decode($data['attach'], true), 'statement_id');
        $data['company_id'] = FinanceStatementService::getInstance($data['statement_id'])->fCompanyId();
        session(SESSION_COMPANY_ID, $data['company_id']);
        // Debug::debug('拆解数据', $data);
        return $this->update($data);
    }

    public static function extraAfterUpdate(&$data, $uuid) {
        $result = Arrays::value($data, 'result_code');
        //状态S-交易成功；F-交易失败；A-等待授权；Z-交易未知；D-订单已撤销
        $info = self::getInstance($uuid)->get(0);
        if ($result == 'SUCCESS' && !FinanceAccountLogService::statementHasLog($info['statement_id'])) {
            Db::startTrans();
            self::addFinanceAccountLog($info);
            Db::commit();
        }
    }

    /**
     * 2022-11-24:同处理
     * @param type $data
     * @param type $uuid
     */
    public static function extraAfterSave(&$data, $uuid) {
        $result = Arrays::value($data, 'result_code');
        //状态S-交易成功；F-交易失败；A-等待授权；Z-交易未知；D-订单已撤销
        $info = self::getInstance($uuid)->get(0);
        if ($result == 'SUCCESS' && !FinanceAccountLogService::statementHasLog($info['statement_id'])) {
            Db::startTrans();
            self::addFinanceAccountLog($info);
            Db::commit();
        }
    }

    /*
     * 写入商户
     */

    public static function addFinanceAccountLog($log) {
        Debug::debug('addFinanceAccountLog::输入信息', $log);
        $statementId = $log['statement_id'];
        $statement = FinanceStatementService::getInstance($statementId)->get(0);
        Debug::debug('addFinanceAccountLog::$statementId信息', $statementId);
        Debug::debug('addFinanceAccountLog::$statement信息', $statement);
        if (!$statementId || !$statement) {
            throw new Exception('账单不存在' . $statementId);
        }

        $data['company_id'] = $log['company_id'];
        $data['user_id'] = Arrays::value($statement, 'user_id');
        $data['customer_id'] = Arrays::value($statement, 'customer_id');
        $data['money'] = Arrays::value($statement, 'need_pay_prize');
        $data['statement_id'] = $log['statement_id'];
        $data['reason'] = Arrays::value($statement, 'statement_name');
        $data['change_type'] = Arrays::value($statement, 'change_type');
        $data['account_id'] = FinanceAccountService::getIdByAccountType($log['company_id'], 'wxMch');      //微信商户号
        $data['from_table'] = self::mainModel()->getTable();
        $data['from_table_id'] = $log['id'];
        Debug::debug('addFinanceAccountLog::保存信息', $data);
        return FinanceAccountLogService::save($data);
    }

    /**
     * 根据商户单号获取支付信息
     */
    public static function getByOutTradeNo($paySn, $con = []) {
        $con[] = ['out_trade_no', '=', $paySn];
        $con[] = ['result_code', '=', 'SUCCESS'];
        $con[] = ['return_code', '=', 'SUCCESS'];

        return self::find($con);
    }

    /**
     * 使用账单号进行收付款查询
     * @param type $statementId
     */
    public static function payQuery($statementId) {
        // 查询本地是否有记录
        $con[] = ['statement_id', '=', $statementId];
        $info = self::mainModel()->where($con)->find();
        if ($info) {
            // 【情况1】本地有记录，直接返回
            return $info;
        }
        // 【情况2】本地无记录，查微信远程服务器数据
        return self::payQueryRemote($statementId);
    }

    /**
     * 查微信远程服务器数据
     */
    public static function payQueryRemote($statementId) {
        $statement = FinanceStatementService::getInstance($statementId)->get();
        if (!$statement) {
            throw new Exception('查单失败:账单不存在');
        }
        // 20230903：本地不查
        if(Arrays::value($_SERVER, 'SERVER_NAME') == 'localhost'){
            return false;
        }
        // TODO:实例类对象如何缓存？？
        $con[] = ['company_id', '=', $statement['company_id']];
        $config = WechatWxPayConfigService::mainModel()->where($con)->find();
        if(!$config){
            return false;
        }
        $inst = new WxPayApiXie();
        $inst->setConf($config);
        $res = $inst->orderQuery($statement);
        if ($res['return_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS') {
            if ($res['trade_state'] == 'NOTPAY') {
                return false;
            }
            $res['val'] = json_encode($res, JSON_UNESCAPED_UNICODE);
            $res['statement_id'] = $res['out_trade_no'];
            $res['company_id'] = $statement['company_id'];
            $resp = self::save($res);
            // 主动查单：处理订单数据；
            Db::startTrans();
            DealOrder::dealOrderFinance($resp['id']);
            Db::commit();
            return $resp;
        } else {
            // 查单失败，返回失败
            return false;
        }
    }

    /**
     * 查微信远程服务器数据
     */
    public static function refundQueryRemote($statementId) {
        $statement = FinanceStatementService::getInstance($statementId)->get();
        if (!$statement) {
            throw new Exception('退款查单失败:原账单不存在');
        }
        // TODO:实例类对象如何缓存？？
        $con[] = ['company_id', '=', $statement['company_id']];
        $config = WechatWxPayConfigService::mainModel()->where($con)->find();
        $inst = new WxPayApiXie();
        $inst->setConf($config);
        $res = $inst->refundQuery($statement);
        dump($res);
    }
    
    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCompanyId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 返回信息
     */
    public function fVal() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fAttach() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fBankType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fCashFee() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fFeeType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fIsSubscribe() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fMchId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fNonceStr() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fOpenid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fOutTradeNo() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fResultCode() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fReturnCode() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fSign() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTimeEnd() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTotalFee() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTradeType() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     *
     */
    public function fTransactionId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
