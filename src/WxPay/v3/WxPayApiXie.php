<?php

namespace xjryanse\wechat\WxPay\v3;

use xjryanse\wechat\WxPay\v3\Header;
use xjryanse\logic\Strings;
use xjryanse\curl\Query;
/**
 * v3接口开发（官方首页只剩下V3版本了）
 * @createTime 2024-05-06
 */
class WxPayApiXie extends Base {
    
    protected static $baseUrl = 'https://api.mch.weixin.qq.com';

    /**
     * JSAPI下单
     * @param type $statement
     * @param type $timeOut
     * @return type
     */
    public function newWxPay() {

    }
    /**
     * 订单查询
     */
    public function payQuery(){

    }
    /**
     * 关闭订单
     */
    public function payClose(){

    }
    /**
     * 发起退款
     */
    public function newRefund(){
        
    }
    /**
     * 退款查询
     */
    public function refundQuery(){
        
    }
    /**
     * 申请资金账单
     * https://pay.weixin.qq.com/docs/merchant/apis/jsapi-payment/get-fund-bill.html
     */
    public function fundBill(){
        $url        = '/v3/bill/fundflowbill';
        $uFull      = self::$baseUrl.$url;
        // 构造签名
        
        $randStr    = Strings::rand(16);

        $auth       = Header::authorization('GET', $url, $randStr);

        dump($auth);
        dump($uFull);

        $header = [];
        $header['Accept']           = 'application/json';
        $header['Authorization']    = $auth;
        $res = Query::geturl($uFull,$header);
        dump($res);
    }
    /**
     * 申请交易账单
     */
    public function tradeBill(){
        
    }
}
