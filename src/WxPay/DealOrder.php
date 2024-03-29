<?php
namespace xjryanse\wechat\WxPay;

use xjryanse\finance\service\FinanceIncomePayService;
use xjryanse\finance\logic\FinanceIncomeLogic;
use xjryanse\finance\logic\FinanceIncomePayLogic;
use xjryanse\wechat\service\WechatWxPayLogService;
use xjryanse\wechat\service\WechatWxPayRefundLogService;
use xjryanse\finance\logic\UserPayLogic;
use xjryanse\logic\Debug;
use think\Db;
use Exception;
/*
 * 支付完成回调后，处理支付记录和订单信息
 */
class DealOrder
{

    /**
     * 【适用于一般场景】拆解返回数据保存
     */
    public static function tearData()
    {
        $con[]  = ['appid','=',''];
        $list   = WechatWxPayLogService::lists( $con );
        foreach( $list as &$v){
            //不开事务无法执行财务数据处理
            Db::startTrans();
                //拆解数据保存
                WechatWxPayLogService::getInstance( $v['id'] )->tearValData();
                //处理订单财务数据
                self::dealOrderFinance( $v['id'] );
            Db::commit();
        }
        return $list;
    }
    /**
     * 处理订单财务数据
     * @param type $wxPayLogId
     */
    public static function dealOrderFinance( $wxPayLogId )
    {
        WechatWxPayLogService::checkTransaction();
        //无缓存拿数据
        $info       = WechatWxPayLogService::getInstance( $wxPayLogId )->get(0); 
        if(!$info){
            throw new Exception( '未找到微信支付记录信息'. $wxPayLogId );
        }
        $arrayData  = json_decode( $info['val'], true );
        //获取支付单id
        $con[] = ['income_id','=',$info['statement_id']];
        $financeIncomePay = FinanceIncomePayService::mainModel()->where($con)->order('id desc')->find();
        Debug::debug('$financeIncomePay', $financeIncomePay);
        if(!$financeIncomePay){
            //20220717:增加$wxPayLogId
            throw new Exception( '未找到'. $arrayData['out_trade_no'] .'对应支付单信息，$wxPayLogId'.$wxPayLogId );
        }
        return UserPayLogic::afterPay($financeIncomePay['id']);

    }
    /**
     * 处理退款的财务入账逻辑
     */
    public static function dealRefundFinance( $wxPayRefundLogId )
    {
        $info = WechatWxPayRefundLogService::getInstance( $wxPayRefundLogId )->get( 0 );
        if(!$info){
            throw new Exception( '未找到微信退款记录信息'. $wxPayRefundLogId );
        }
        if($info['result_code'] != 'SUCCESS'){
            throw new Exception( '退款未成功'. $wxPayRefundLogId );
        }
        //
        $arrayData  = json_decode( $info['val'], true );
        //获取支付单id
        $financeIncomePay = FinanceIncomePayService::getBySn($arrayData['out_trade_no']);
        if(!$financeIncomePay){
            throw new Exception( '未找到'. $arrayData['out_trade_no'] .'对应支付单信息' );
        }        
        //支付单更新为已退款
        FinanceIncomePayLogic::afterPayDoIncome($financeIncomePay['id']);
        //收款单更新为已收款，且收款金额写入订单；
        FinanceIncomeLogic::afterPayDoIncome($financeIncomePay['id']);
        
        
    }
    
    
}
