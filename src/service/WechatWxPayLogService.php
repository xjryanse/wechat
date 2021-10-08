<?php

namespace xjryanse\wechat\service;

use xjryanse\finance\service\FinanceAccountLogService;
use xjryanse\finance\service\FinanceAccountService;
use xjryanse\finance\service\FinanceStatementService;
use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\wechat\model\WechatWxPayLog;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use Exception;
use think\Db;
/**
 * 微信支付记录
 */
class WechatWxPayLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWxPayLog';
    //直接执行后续触发动作
    protected static $directAfter = true;
    
    /**
     * 拆解val数据保存
     */
    public function tearValData() {
        $info = $this->get( MASTER_DATA );
        //val转数组
        $data = json_decode($info['val'], true);
        Debug::debug('拆解数据attach',json_decode($data['attach'], true));
        $data['statement_id']   = Arrays::value(json_decode($data['attach'], true),'statement_id');
        $data['company_id']     = FinanceStatementService::getInstance( $data['statement_id'] )->fCompanyId();
        session( SESSION_COMPANY_ID, $data['company_id']);
        Debug::debug('拆解数据',$data);
        return $this->update($data);
    }

    public static function extraAfterUpdate(&$data, $uuid) {
        $result     = Arrays::value($data, 'result_code');
        //状态S-交易成功；F-交易失败；A-等待授权；Z-交易未知；D-订单已撤销
        $info = self::getInstance( $uuid )->get(0);
        if( $result == 'SUCCESS' && !FinanceAccountLogService::statementHasLog( $info['statement_id'] ) ){
            Db::startTrans();
            self::addFinanceAccountLog( $info );
            Db::commit();
        }
    }
    
    /*
     * 写入商户
     */
    public static function addFinanceAccountLog(WechatWxPayLog $log)
    {
        Debug::debug('addFinanceAccountLog::输入信息',$log);
        $statementId            = $log['statement_id'];
        $statement              = FinanceStatementService::getInstance( $statementId )->get();
        Debug::debug('addFinanceAccountLog::$statementId信息',$statementId);
        Debug::debug('addFinanceAccountLog::$statement信息',$statement);
        if(!$statementId || !$statement){
            throw new Exception('账单不存在');
        }

        $data['company_id']     = $log['company_id'];
        $data['user_id']        = Arrays::value($statement, 'user_id');
        $data['customer_id']    = Arrays::value($statement, 'customer_id');
        $data['money']          = Arrays::value($statement, 'need_pay_prize');
        $data['statement_id']   = $log['statement_id'];
        $data['reason']         = Arrays::value($statement, 'statement_name');
        $data['change_type']    = Arrays::value($statement, 'change_type');
        $data['account_id']     = FinanceAccountService::getIdByAccountType($log['company_id'], 'wxMch');      //微信商户号
        $data['from_table']     = self::mainModel()->getTable();
        $data['from_table_id']  = $log['id'];
        Debug::debug('addFinanceAccountLog::保存信息',$data);
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
