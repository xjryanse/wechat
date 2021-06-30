<?php

/**
 * 2015-06-29 修复签名问题
 * */

namespace xjryanse\wechat\WxPay\sec;
use xjryanse\wechat\WxPay\base\WxPayDataBase;

/**
 * 微信支付多次分账
 * @author widyhu
 */
class WxPaySecMultiProfitSharing extends WxPayDataBase {
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
    /**
     * 添加分账接收方
     * @param type $type    类型
     * @param type $account 账号
     * @param type $amount  金额
     * @param type $description 描述
     * @param type $name    名称
     */
    public function addReceivers($type,$account,$amount,$description,$name){
        if(!$this->values['receivers']){
            $this->values['receivers'] = [];
        } else {
            $this->values['receivers'] = json_decode($this->values['receivers']);
        }
        $tempArr = [];
        $tempArr['type']        = $type;            //MERCHANT_ID：商户号（mch_id或者sub_mch_id） ;PERSONAL_OPENID：个人openid
        $tempArr['account']     = $account;         //类型是MERCHANT_ID时，是商户号（mch_id或者sub_mch_id） 类型是PERSONAL_OPENID时，是个人openid 
        $tempArr['amount']      = $amount;          //分账金额，单位为分，只能为整数，不能超过原订单支付金额及最大分账比例金额 
        $tempArr['description'] = $description;     //分账的原因描述，分账账单中需要体现
        $tempArr['name']        = $name;            //可选项，在接收方类型为个人的时可选填，若有值，会检查与 name 是否实名匹配，不匹配会拒绝分账请求 1、分账接收方类型是PERSONAL_OPENID时，是个人姓名（选传，传则校验） 
        //封包后写入
        $this->values['receivers'][]    = $tempArr;
        $this->values['receivers']      = json_encode($this->values['receivers'],JSON_UNESCAPED_UNICODE);
    }
    /**
     * 获取分账接收方
     * @return type
     */
    public function getReceivers() {
        return $this->values['receivers'];
    }    

}
