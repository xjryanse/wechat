<?php

namespace xjryanse\wechat\WxPay\lib;

use xjryanse\wechat\WxPay\base\WxPayException;
use xjryanse\wechat\WxPay\base\WxPayConfigInterface;
use xjryanse\wechat\WxPay\base\WxPayApiBase;
use xjryanse\wechat\WxPay\base\WxPayResults;

/**
 * 
 * 接口访问类，包含所有微信支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交被扫支付为10s，上报超时时间为1s外，其他均为6s）
 * @author widyhu
 *
 */
class WxPayApi extends WxPayApiBase {

    /**
     * 
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function unifiedOrder($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet()) {
            throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
        } else if (!$inputObj->IsBodySet()) {
            throw new WxPayException("缺少统一支付接口必填参数body！");
        } else if (!$inputObj->IsTotal_feeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数total_fee！");
        } else if (!$inputObj->IsTrade_typeSet()) {
            throw new WxPayException("缺少统一支付接口必填参数trade_type！");
        }

        //关联参数
        if ($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if ($inputObj->GetTrade_type() == "NATIVE" && !$inputObj->IsProduct_idSet()) {
            throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }

        //异步通知url未设置，则使用配置文件中的url
        if (!$inputObj->IsNotify_urlSet() && $config->GetNotifyUrl() != "") {
            $inputObj->SetNotify_url($config->GetNotifyUrl()); //异步通知url
        }

        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']); //终端ip	   	    
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串
        //签名
        $inputObj->SetSign($config);
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
//        dump("url");
//        var_dump($url);
//        dump("请求xml");
//        dump( $xml );
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);

        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayOrderQuery $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function orderQuery($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 关闭订单，WxPayCloseOrder中out_trade_no必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayCloseOrder $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function closeOrder($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet()) {
            throw new WxPayException("订单查询接口中，out_trade_no必填！");
        }
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个且
     * out_refund_no、total_fee、refund_fee、op_user_id为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayRefund $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function refund($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new WxPayException("退款申请接口中，out_trade_no、transaction_id至少填一个！");
        } else if (!$inputObj->IsOut_refund_noSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数out_refund_no！");
        } else if (!$inputObj->IsTotal_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数total_fee！");
        } else if (!$inputObj->IsRefund_feeSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数refund_fee！");
        } else if (!$inputObj->IsOp_user_idSet()) {
            throw new WxPayException("退款申请接口中，缺少必填参数op_user_id！");
        }
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();
        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, true, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 查询退款
     * 提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，
     * 用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。
     * WxPayRefundQuery中out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayRefundQuery $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function refundQuery($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //检测必填参数
        if (!$inputObj->IsOut_refund_noSet() &&
                !$inputObj->IsOut_trade_noSet() &&
                !$inputObj->IsTransaction_idSet() &&
                !$inputObj->IsRefund_idSet()) {
            throw new WxPayException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！");
        }
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 下载对账单，WxPayDownloadBill中bill_date为必填参数
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayDownloadBill $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function downloadBill($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        //检测必填参数
        if (!$inputObj->IsBill_dateSet()) {
            throw new WxPayException("对账单接口中，缺少必填参数bill_date！");
        }
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        if (substr($response, 0, 5) == "<xml>") {
            return "";
        }
        return $response;
    }

    /**
     * 提交被扫支付API
     * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
     * 由商户收银台或者商户后台调用该接口发起支付。
     * WxPayWxPayMicroPay中body、out_trade_no、total_fee、auth_code参数必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayWxPayMicroPay $inputObj
     * @param int $timeOut
     */
    public static function micropay($config, $inputObj, $timeOut = 10) {
        $url = "https://api.mch.weixin.qq.com/pay/micropay";
        //检测必填参数
        if (!$inputObj->IsBodySet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数body！");
        } else if (!$inputObj->IsOut_trade_noSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数out_trade_no！");
        } else if (!$inputObj->IsTotal_feeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数total_fee！");
        } else if (!$inputObj->IsAuth_codeSet()) {
            throw new WxPayException("提交被扫支付API接口中，缺少必填参数auth_code！");
        }

        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']); //终端ip
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 撤销订单API接口，WxPayReverse中参数out_trade_no和transaction_id必须填写一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayReverse $inputObj
     * @param int $timeOut
     * @throws WxPayException
     */
    public static function reverse($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";
        //检测必填参数
        if (!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
            throw new WxPayException("撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！");
        }

        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, true, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayReport $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function report($config, $inputObj, $timeOut = 1) {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if (!$inputObj->IsInterface_urlSet()) {
            throw new WxPayException("接口URL，缺少必填参数interface_url！");
        } if (!$inputObj->IsReturn_codeSet()) {
            throw new WxPayException("返回状态码，缺少必填参数return_code！");
        } if (!$inputObj->IsResult_codeSet()) {
            throw new WxPayException("业务结果，缺少必填参数result_code！");
        } if (!$inputObj->IsUser_ipSet()) {
            throw new WxPayException("访问接口IP，缺少必填参数user_ip！");
        } if (!$inputObj->IsExecute_time_Set()) {
            throw new WxPayException("接口耗时，缺少必填参数execute_time_！");
        }
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetUser_ip($_SERVER['REMOTE_ADDR']); //终端ip
        $inputObj->SetTime(date("YmdHis")); //商户上报时间	 
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        return $response;
    }

    /**
     * 
     * 生成二维码规则,模式一生成支付二维码
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayBizPayUrl $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function bizpayurl($config, $inputObj, $timeOut = 6) {
        if (!$inputObj->IsProduct_idSet()) {
            throw new WxPayException("生成二维码，缺少必填参数product_id！");
        }

        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetTime_stamp(time()); //时间戳	 
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名

        return $inputObj->GetValues();
    }

    /**
     * 
     * 转换短链接
     * 该接口主要用于扫码原生支付模式一中的二维码链接转成短链接(weixin://wxpay/s/XXXXXX)，
     * 减小二维码数据量，提升扫描速度和精确度。
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayShortUrl $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function shorturl($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/tools/shorturl";
        //检测必填参数
        if (!$inputObj->IsLong_urlSet()) {
            throw new WxPayException("需要转换的URL，签名用原串，传输需URL encode！");
        }
        $inputObj->SetAppid($config->GetAppId()); //公众账号ID
        $inputObj->SetMch_id($config->GetMerchantId()); //商户号
        $inputObj->SetNonce_str(self::getNonceStr()); //随机字符串

        $inputObj->SetSign($config); //签名
        $xml = $inputObj->ToXml();

        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, false, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 支付结果通用通知
     * @param function $callback
     * 直接回调函数使用方法: notify(you_function);
     * 回调类成员函数方法:notify(array($this, you_function));
     * $callback  原型为：function function_name($data){}
     */
    public static function notify($config, $callback, &$msg) {
        //获取通知的数据
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($xml)) {
            # 如果没有数据，直接返回失败
            return false;
        }

        //如果返回成功则验证签名
        try {
            $result = WxPayNotifyResults::Init($config, $xml);
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }

        return call_user_func($callback, $result);
    }
    
    /**
     * 
     * @param WxPayConfigInterface $config  配置对象
     * @param WxPayMchOutPay $inputObj
     * @param int $timeOut
     * @return type
     */
    public static function mchOutPay($config, $inputObj, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        
        $inputObj->setMchAppid($config->GetAppId()); //公众账号ID        
        $inputObj->setMchid($config->GetMerchantId()); //商户号
        $inputObj->setNonceStr(self::getNonceStr()); //随机字符串
        $inputObj->setSign($config); //签名
        
        dump($inputObj);
        $xml = $inputObj->ToXml();
        dump($xml);
        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, true, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

}
