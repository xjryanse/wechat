<?php
namespace xjryanse\wechat\WxPay\sec;

use xjryanse\wechat\WxPay\base\WxPayApiBase;
use xjryanse\wechat\WxPay\base\WxPayResults;
use xjryanse\wechat\WxPay\base\WxPayConfigInterface;
use xjryanse\logic\Debug;

class WxPaySecApi extends WxPayApiBase  {
    /**
     * 
     * @param type $url
     * @param WxPayConfigInterface $config
     * @param mix $inputObj
     * @param type $timeOut
     * @return type
     */
    private static function doQuery($url, $config, $inputObj, $timeOut)
    {
        $inputObj->setAppid($config->GetAppId());       //公众账号ID        
        $inputObj->setMchId($config->GetMerchantId());  //商户号
        $inputObj->setNonceStr(self::getNonceStr());    //随机字符串
        $inputObj->SetSign($config); //签名
        
        $xml = $inputObj->ToXml();
        Debug::debug('$inputObj',$inputObj);
        Debug::debug('$inputObj->ToXml()',$inputObj->ToXml());
        $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response = self::postXmlCurl($config, $xml, $url, true, $timeOut);
        $result = WxPayResults::Init($config, $response);
        self::reportCostTime($config, $url, $startTimeStamp, $result); //上报请求花费时间
        return $result;
    }
    /**
     * 微信分账API：请求单次分账
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_1&index=1
     * @param WxPayConfigInterface $config
     * @param WxPaySecProfitSharing $inputObj
     * @param type $timeOut
     * @return type
     */
    public static function profitSharing($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharing";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    /**
     * 请求多次分账
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_6&index=2
     * @param WxPayConfigInterface $config
     * @param WxPaySecMultiProfitSharing $inputObj
     * @param type $timeOut
     */
    public static function multiProfitSharing($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/multiprofitsharing";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    
     /**
     * 查询分账结果
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_2&index=3
     * @param WxPayConfigInterface $config
     * @param WxPaySecProfitSharingQuery $inputObj
     * @param type $timeOut
     */
    public static function profitSharingQuery($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingquery";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    
    /**
     * 添加分账接收方
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_3&index=4
     * @param WxPayConfigInterface $config
     * @param type $inputObj
     * @param type $timeOut
     */
    public static function profitSharingAddReceiver($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingaddreceiver";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    
    /**
     * 删除分账接收方
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_3&index=4
     * @param WxPayConfigInterface $config
     * @param type $inputObj
     * @param type $timeOut
     */
    public static function profitSharingRemoveReceiver($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingremovereceiver";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    
    /**
     * 完结分账
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_5&index=6
     * @param WxPayConfigInterface $config
     * @param type $inputObj
     * @param type $timeOut
     */
    public static function profitSharingFinish($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharingfinish";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    
    /**
     * 查询订单待分账金额
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_10&index=7
     * @param WxPayConfigInterface $config
     * @param type $inputObj
     * @param type $timeOut
     */
    public static function profitSharingOrderAmountQuery($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingorderamountquery";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    /**
     * 分账回退
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_7&index=8
     * @param WxPayConfigInterface $config
     * @param type $inputObj
     * @param type $timeOut
     */
    public static function profitSharingReturn($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/profitsharingreturn";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }
    /**
     * 分账回退查询
     * https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_7&index=8
     * @param WxPayConfigInterface $config
     * @param type $inputObj
     * @param type $timeOut
     */
    public static function profitSharingReturnQuery($config, $inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/profitsharingreturnquery";
        //执行查询
        return self::doQuery($url, $config, $inputObj, $timeOut);
    }    
    
    
}
