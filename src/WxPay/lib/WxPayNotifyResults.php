<?php

/**
 * 2015-06-29 修复签名问题
 * */

namespace xjryanse\wechat\WxPay\lib;
use xjryanse\wechat\WxPay\base\WxPayException;
use xjryanse\wechat\WxPay\base\WxPayConfigInterface;
use xjryanse\wechat\WxPay\base\WxPayResults;

/**
 *
 * 回调回包数据基类
 *
 * */
class WxPayNotifyResults extends WxPayResults {

    /**
     * 将xml转为array
     * @param WxPayConfigInterface $config
     * @param string $xml
     * @return WxPayNotifyResults
     * @throws WxPayException
     */
    public static function Init($config, $xml) {
        $obj = new self();
        $obj->FromXml($xml);
        //失败则直接返回失败
        $obj->CheckSign($config);
        return $obj;
    }

}
