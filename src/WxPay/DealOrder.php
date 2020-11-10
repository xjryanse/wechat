<?php
namespace xjryanse\wechat\WxPay;

use xjryanse\wechat\service\WechatWxPayLogService;
use xjryanse\finance\service\FinanceIncomeService;
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
            //处理订单财务数据
            self::dealOrderFinance( $v['id'] );
        }
    }
    /**
     * 处理订单财务数据
     * @param type $wxPayLogId
     */
    public static function dealOrderFinance( $wxPayLogId )
    {
        //无缓存拿数据
        $info       = self::getInstance( $wxPayLogId )->get(0); 
        if(!$info){
            throw new Exception( '未找到微信支付记录信息'. $wxPayLogId );
        }
        $arrayData  = json_decode( $info['val'], true );
        //收款单更新为已收款；
        
        //收款单号取订单号
        //订单循环入账
                
        
    }
    
}
