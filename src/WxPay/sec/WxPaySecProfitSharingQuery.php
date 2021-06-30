<?php

/**
 * 2015-06-29 修复签名问题
 * */

namespace xjryanse\wechat\WxPay\sec;
use xjryanse\wechat\WxPay\base\WxPayDataBase;

/**
 * 微信支付单次分账
 * @author widyhu
 */
class WxPaySecProfitSharingQuery extends WxPayDataBase {
    public function setMchId($value){
        $this->values['mch_id'] = $value;
    }
    public function getMchId() {
        return $this->values['mch_id'];
    }
    public function setAppid($value){
        $this->values['appid'] = $value;
    }
    public function getAppid() {
        return $this->values['appid'];
    }
    public function setNonceStr($value){
        $this->values['nonce_str'] = $value;
    }
    public function getNonceStr() {
        return $this->values['nonce_str'];
    }
    /**
     * 微信订单号
     * @param type $value
     */
    public function setTransactionId($value){
        $this->values['transaction_id'] = $value;
    }
    /**
     * 微信订单号
     * @return type
     */
    public function getTransactionId() {
        return $this->values['transaction_id'];
    }    
    /**
     * 商户分账单号
     * @param type $value
     */
    public function setOutOrderNo($value){
        $this->values['out_order_no'] = $value;
    }
    /**
     * 商户分账单号
     * @return type
     */
    public function getOutOrderNo() {
        return $this->values['out_order_no'];
    }    

}
