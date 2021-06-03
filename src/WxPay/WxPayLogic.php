<?php
namespace xjryanse\wechat\WxPay;

use xjryanse\wechat\WxPay\WxPayConfigs;
use xjryanse\wechat\WxPay\JsApiPay;
use xjryanse\wechat\WxPay\lib\WxPayApi;
use xjryanse\wechat\WxPay\lib\WxPayMchOutPay;
use xjryanse\wechat\service\WechatWxPayRefundLogService;
use xjryanse\finance\logic\FinanceRefundLogic;
use xjryanse\wechat\service\WechatWxPayLogService;
use xjryanse\logic\Debug;
/**
 * 微信支付逻辑
 */
class WxPayLogic
{
    protected $wePubAppId;
    
    protected $openid;
    /**
     * @param type $wePubAppId  公众号Appid
     * @param type $openid      openid
     */
    public function __construct( $wePubAppId, $openid ) {
        $this->wePubAppId   = $wePubAppId;
        $this->openid       = $openid;
    }
    
    /*
     * 获取微信支付jsapi参数
     * @param type $outTradeNo      商户订单号
     * @param type $money           支付金额：元
     * @param type $orderDescribe   订单描述
     * @param array $attach         额外参数
     * @return type
     */
    public function getWxPayJsApiOrder( $outTradeNo, $money, $orderDescribe, $attach = '')
    {
        $appId                  = $this->wePubAppId;
        $param['profit_sharing']= 'Y';  //Y-是，需要分账;N-否，不分账
        $param['openid']        = $this->openid;
        $param['body']          = $orderDescribe;    //商品简单描述
        $param['attach']        = $attach; //附加数据，在查询API和支付通知中原样返回
        $param['out_trade_no']  = $outTradeNo; //商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|* 且在同一个商户号下唯一
        $param['total_fee']     = round( $money * 100, 2); //订单总金额，单位为分
        $config                 = WxPayConfigs::getInstance( $appId );
        $wxPayJsApiOrder        = (new JsApiPay())->order( $param, $config );
        return $wxPayJsApiOrder;
    }
    /**
     * 处理退款逻辑
     * @param type $param
     */
    public function doRefund( $param )
    {
        $appId          = $this->wePubAppId;
        $config         = WxPayConfigs::getInstance( $appId );
        Debug::debug('$config', WxPayConfigs::getInstance( $appId )->getInfo());
        $data     = (new JsApiPay())->refund( $param, $config );
        $data['val'] = json_encode($data,JSON_UNESCAPED_UNICODE);
        //退款日志记录
        return WechatWxPayRefundLogService::save($data);
    }
    /**
     * 付款至用户零钱
     */
    public function doOutcomePay($input)
    {
        $appId          = $this->wePubAppId;
        $config         = WxPayConfigs::getInstance( $appId );
        Debug::debug('$config', WxPayConfigs::getInstance( $appId )->getInfo());
        
        return (new JsApiPay())->doOutcomePay( $input, $config );
    }
    
    /*
     * 商户单号退款
     * @param type $paySn       商户单号
     * @param type $orderId     订单id
     * @param type $refundMoney 退款金额（元）
     * @return array
     */
    public static function paySnRefund( $paySn, $orderId , $refundMoney='')
    {
        //支付单号数组取支付记录列表
        $con[]      = ['out_trade_no','in',$paySn];
        $lists      = WechatWxPayLogService::lists( $con );
        //返回数组
        $resp = [];
        foreach( $lists as $wxPayLog ){
            //退款金额(元)
            $refundFee  = $refundMoney ? : $wxPayLog['total_fee'] * 0.01 ;
            //生成退款单
            $data['refund_from']    = FR_FINANCE_WECHAT; //从微信退
            $data['order_id']       = $orderId;
            $data['refund_prize']   = $refundFee;
            $data['refund_reason']  = '订单'.$orderId.'退款';
            $data['pay_sn']         = $wxPayLog['out_trade_no'];
            $logic  = FinanceRefundLogic::newRefund( $data );
            //执行退款操作
            FinanceRefundLogic::doRefund( $logic['id'] );
        }
        return $resp;
    }
}
