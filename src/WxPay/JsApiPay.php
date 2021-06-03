<?php
namespace xjryanse\wechat\WxPay;

use xjryanse\wechat\WxPay\lib\WxPayUnifiedOrder;
use xjryanse\wechat\WxPay\base\WxPayConfigInterface;
use xjryanse\wechat\WxPay\lib\WxPayApi;
use xjryanse\wechat\WxPay\lib\WxPayRefund;
use xjryanse\wechat\WxPay\lib\WxPayMchOutPay;
/*
 * 微信JsApi支付配置信息
 */
class JsApiPay
{
    public function order( $param,WxPayConfigInterface $config)
    {
        $tools = new JsApiTool();
        $tools->setConfig($config);

        $openId = $param['openid'];

        $input = new WxPayUnifiedOrder();
        $input->SetBody($param['body']); //商品简单描述
        $input->SetAttach(isset($param['attach']) ? $param['attach'] : ''); //附加数据，在查询API和支付通知中原样返回
        $input->SetOut_trade_no($param['out_trade_no']); //商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|* 且在同一个商户号下唯一
        $input->SetTotal_fee((int)round($param['total_fee'])); //订单总金额，单位为分
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag(isset($param['goods_tag']) ? $param['goods_tag'] : '');
        $input->SetNotify_url(isset($param['notify_url']) ? $param['notify_url'] : $config->GetNotifyUrl()); //异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数
        $input->SetTrade_type('JSAPI');
        $input->SetProfit_sharing( isset($param['profit_sharing']) ? $param['profit_sharing'] : '' );
        $input->SetOpenid( $openId );

        $order = WxPayApi::unifiedOrder($config, $input);

        $jsApiParameters = $tools->GetJsApiParameters($order);

        // $editAddress = $tools->GetEditAddressParameters();

        return array(
            'jsApiParameters' => $jsApiParameters,
            // 'editAddress' => $editAddress,
        );
    }
    /**
     * 退款
     */
    public function refund( $param ,$config)
    {
        $outRefundNo    = $param["out_refund_no"];        
        $outTradeNo     = $param["out_trade_no"];
        $total_fee      = $param["total_fee"];
        $refund_fee     = $param["refund_fee"];
        $input = new WxPayRefund();
        $input->SetOut_refund_no($outRefundNo);
        $input->SetOut_trade_no($outTradeNo);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOp_user_id($config->GetMerchantId());
        return WxPayApi::refund($config, $input);
    }
    
    public function doOutcomePay($param,$config)
    {
        $partnerTradeNo     = $param["partner_trade_no"];        
        $openid             = $param["openid"];        
        $desc               = $param["desc"];        
        $spbillCreateIp     = $param["spbill_create_ip"];        
        $amount             = $param["amount"];        

        $input = new WxPayMchOutPay();
        $input->setPartnerTradeNo($partnerTradeNo);
        $input->setCheckName('NO_CHECK');
        $input->setOpenid( $openid );
        $input->setDesc($desc);
        $input->setAmount($amount);   //金额
        $input->setSpbillCreateIp( $spbillCreateIp );
        
        return WxPayApi::mchOutPay($config, $input);        
    }
}
