<?php
namespace xjryanse\wechat\WxPay;

use xjryanse\wechat\service\WechatWxPayLogService;
use xjryanse\finance\service\FinanceIncomePayService;
use xjryanse\finance\logic\FinanceIncomeLogic;
use xjryanse\finance\logic\FinanceIncomePayLogic;
use think\Db;
use Exception;
/*
 * 支付完成回调后，处理支付记录和订单信息
 */
class DealOrder
{

    /**
     * 拆解返回数据保存
     */
    public static function tearData()
    {
        $con[]  = ['appid','=',''];
        $list   = WechatWxPayLogService::lists( $con );
        foreach( $list as &$v){
            //拆解数据保存
            WechatWxPayLogService::getInstance( $v['id'] )->tearValData();
            //不开事务无法执行财务数据处理
            Db::startTrans();
            //处理订单财务数据
            self::dealOrderFinance( $v['id'] );
            Db::commit();
        }
    }
    /**
     * 处理订单财务数据
     * @param type $wxPayLogId
     */
    public static function dealOrderFinance( $wxPayLogId )
    {
        //无缓存拿数据
        $info       = WechatWxPayLogService::getInstance( $wxPayLogId )->get(0); 
        if(!$info){
            throw new Exception( '未找到微信支付记录信息'. $wxPayLogId );
        }
        $arrayData  = json_decode( $info['val'], true );
        //获取支付单id
        $financeIncomePay = FinanceIncomePayService::getBySn($arrayData['out_trade_no']);
        //支付单更新为已收款
        FinanceIncomePayLogic::afterPayDoIncome($financeIncomePay['id']);
        //收款单更新为已收款，且收款金额写入订单；
        FinanceIncomeLogic::afterPayDoIncome($financeIncomePay['id']);
        //收款金额写入订单
        return true;
    }
    
}
