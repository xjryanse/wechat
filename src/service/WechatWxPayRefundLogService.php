<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\finance\service\FinanceAccountLogService;
use xjryanse\finance\service\FinanceAccountService;
use xjryanse\finance\service\FinanceStatementService;
use xjryanse\wechat\model\WechatWxPayRefundLog;
use xjryanse\logic\Arrays;
use think\Db;
/**
 * 微信支付退款
 */
class WechatWxPayRefundLogService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWxPayRefundLog';

    public static function extraAfterSave(&$data, $uuid) {
        $result     = Arrays::value($data, 'result_code');
        //状态S-交易成功；F-交易失败；A-等待授权；Z-交易未知；D-订单已撤销
        $info = self::getInstance( $uuid )->get();

        if( $result == 'SUCCESS' && !FinanceAccountLogService::statementHasLog( $info['out_refund_no'] ) ){
            Db::startTrans();
            self::addFinanceAccountLog( $info );
            Db::commit();
        }
    }
    /*
     * 写入微信商户
     */
    public static function addFinanceAccountLog(WechatWxPayRefundLog $log)
    {
        $statementId            = $log['out_refund_no'];
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
}
