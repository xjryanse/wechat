<?php

/**
 * 2021-06-02添加
 * 微信支付商户对外付款
 * */

namespace xjryanse\wechat\WxPay\lib;

use xjryanse\wechat\WxPay\base\WxPayDataBase;

class WxPayMchOutPay extends WxPayDataBase
{
    /**
     * 设置签名，详见签名生成算法
     * @param string $value 
     * */
    public function setSign($config) {
        $sign = $this->MakeSign($config,false);     //不需要补签，否则参数错误，校验不通过，且无法排查：https://developers.weixin.qq.com/community/develop/doc/0004c60a84c268a233691f1d651c00
        $this->values['sign'] = $sign;
        return $sign;
    }
    
    public function setNonceStr($value){
        $this->values['nonce_str'] = $value;
    }
    public function getNonceStr() {
        return $this->values['nonce_str'];
    }    
    
    public function setPartnerTradeNo($value){
        $this->values['partner_trade_no'] = $value;
    }
    public function getPartnerTradeNo() {
        return $this->values['partner_trade_no'];
    }
    public function setMchid($value){
        $this->values['mchid'] = $value;
    }
    public function getMchid() {
        return $this->values['mchid'];
    }
    public function setMchAppid($value){
        $this->values['mch_appid'] = $value;
    }
    public function getMchAppid() {
        return $this->values['mch_appid'];
    }
    public function setCheckName($value){
        $this->values['check_name'] = $value;
    }
    public function getCheckName() {
        return $this->values['check_name'];
    }
    public function setOpenid($value){
        $this->values['openid'] = $value;
    }
    public function getOpenid() {
        return $this->values['openid'];
    }
    public function setAmount($value){
        $this->values['amount'] = intval($value);
    }
    public function getAmount() {
        return $this->values['amount'];
    }    
    public function setDesc($value){
        $this->values['desc'] = $value;
    }
    public function getDesc() {
        return $this->values['desc'];
    }        
    public function setSpbillCreateIp($value){
        $this->values['spbill_create_ip'] = $value;
    }
    public function getSpbillCreateIp() {
        return $this->values['spbill_create_ip'];
    }
}