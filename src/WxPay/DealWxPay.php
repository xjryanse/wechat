<?php
namespace xjryanse\wechat\WxPay;

use xjryanse\wechat\WxPay\JsApiPay;
use xjryanse\wechat\service\WechatWxPayRefundLogService;
/*
 * 处理支付和退款的操作逻辑
 */
class DealWxPay
{
    /**
     * 处理退款逻辑
     * @param type $param
     */
    public static function doRefund( $appId, $param )
    {
        $config         = WxPayConfigs::getInstance( $appId );
        $data           = (new JsApiPay())->refund( $param, $config );
        $data['val']    = json_encode($data,JSON_UNESCAPED_UNICODE);
        //退款日志记录
        return WechatWxPayRefundLogService::save($data);
    }

    
}
