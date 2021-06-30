<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\finance\service\FinanceAccountLogService;
use xjryanse\finance\service\FinanceAccountService;
use xjryanse\finance\service\FinanceStatementService;
use xjryanse\wechat\WxPay\sec\WxPaySecProfitSharing;
use xjryanse\wechat\model\WechatWxPaySec;
use xjryanse\logic\Arrays;
use think\Db;

/**
 * 
 */
class WechatWxPaySecService extends Base implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWxPaySec';

    /**
     * 分账由服务端发起请求，直接可得结果，不存在回调
     * 但是有第一次写入，第二次更新的间隔差（便于后续与其他接口的异步的逻辑并轨）
     * @param type $data
     * @param type $uuid
     */
    public static function extraAfterUpdate( &$data, $uuid ){
        $result     = Arrays::value($data, 'result_code');
        //状态S-交易成功；F-交易失败；A-等待授权；Z-交易未知；D-订单已撤销
        $info = self::getInstance( $uuid )->get();
        if( $result == 'SUCCESS' && !FinanceAccountLogService::statementHasLog( $info['out_order_no'] ) ){
            Db::startTrans();
            self::addFinanceAccountLog( $info );
            Db::commit();
        }
    }
    
    public static function secLog(WxPaySecProfitSharing $input){
        $data['transaction_id'] = $input->getTransactionId();
        $data['out_order_no']   = $input->getOutOrderNo();
        //保存分账账单
        $res            = self::save( $data );
        $receivers      = $input->getReceivers();
        $receiversArr   = json_decode($receivers,true);
        foreach( $receiversArr as &$v){
            $v['sec_id']            = $res['id'];
            $v['transaction_id']    = $input->getTransactionId();
            $v['out_order_no']      = $input->getOutOrderNo();
        }
        //保存账单接收方
        WechatWxPaySecReceiversService::saveAll( $receiversArr );
        return $res;
    }
    
    /*
     * 写入微信商户
     */
    public static function addFinanceAccountLog(WechatWxPaySec $log)
    {
        $statementId            = $log['out_order_no'];
        $statement              = FinanceStatementService::getInstance( $statementId )->get();

        $data['company_id']     = $log['company_id'];
        $data['user_id']        = Arrays::value($statement, 'user_id');
        $data['customer_id']    = Arrays::value($statement, 'customer_id');
        $data['money']          = Arrays::value($statement, 'need_pay_prize');
        $data['statement_id']   = $statementId;
        $data['reason']         = Arrays::value($statement, 'statement_name');
        $data['change_type']    = Arrays::value($statement, 'change_type');
        $data['account_id']     = FinanceAccountService::getIdByAccountType($log['company_id'], 'wxMch');      //微信商户号
        $data['from_table']     = self::mainModel()->getTable();
        $data['from_table_id']  = $log['id'];
        return FinanceAccountLogService::save($data);
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
     * 流水id
     */
    public function fTransactionId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 分账账单号
     */
    public function fOutOrderNo() {
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
