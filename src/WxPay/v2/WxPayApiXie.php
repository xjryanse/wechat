<?php

namespace xjryanse\wechat\WxPay\v2;

use xjryanse\finance\model\FinanceStatement;
use xjryanse\logic\Strings;
use xjryanse\logic\Arrays2d;
/**
 * 20221118:封装重写
 * https://pay.weixin.qq.com/wiki/doc/api/index.html
 */
class WxPayApiXie extends Base {
    /**
     * 
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * @param FinanceStatement $statement
     * @param type $timeOut
     * @return type
     */
    public function orderQuery($statement, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        $this->values['appid']          = $this->payConf['AppId'];
        $this->values['mch_id']         = $this->payConf['MerchantId'];
        $this->values['out_trade_no']   = $statement['id'];
        $this->values['nonce_str']      = self::getNonceStr();
        $this->values['sign']           = self::makeSign();
        $xml = $this->toXml();
        // TODO接上报开启
        // $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response       = $this->postXmlCurl( $xml, $url, false, $timeOut);
        $result         = $this->resultInit($response);
        // TODO需要再接入
        // $this->reportCostTime($url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }

    /**
     * 
     * 查询退款，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * @param FinanceStatement $statement
     * @param type $timeOut
     * @return type
     */
    public function refundQuery($statement, $timeOut = 6) {
        $url = "https://api.mch.weixin.qq.com/pay/refundquery";
        //检测必填参数
        $this->values['appid']          = $this->payConf['AppId'];
        $this->values['mch_id']         = $this->payConf['MerchantId'];
        $this->values['out_trade_no']   = $statement['id'];
        $this->values['nonce_str']      = self::getNonceStr();
        $this->values['sign']           = self::makeSign();
        $xml = $this->toXml();
        // TODO接上报开启
        // $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response       = $this->postXmlCurl( $xml, $url, false, $timeOut);
        $result         = $this->resultInit($response);
        // TODO需要再接入
        // $this->reportCostTime($url, $startTimeStamp, $result); //上报请求花费时间

        return $result;
    }
    /**
     * 下载交易账单
     */
    public function downloadBill($date){
        $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        //检测必填参数
        $this->values['appid']          = $this->payConf['AppId'];
        $this->values['mch_id']         = $this->payConf['MerchantId'];
        $this->values['bill_date']      = date('Ymd',strtotime($date));
        $this->values['nonce_str']      = self::getNonceStr();
        $this->values['bill_type']      = 'ALL';
        $this->values['sign']           = self::makeSign();
        $xml = $this->toXml();
        // TODO接上报开启
        // $startTimeStamp = self::getMillisecond(); //请求开始时间
        $response       = $this->postXmlCurl( $xml, $url, false);
        // 20240722:去除BOM头
        $response = ltrim($response,"\XEF\XBB\XBF");

        return Strings::csvToArray($response);
        // TODO需要再接入
        // $this->reportCostTime($url, $startTimeStamp, $result); //上报请求花费时间
    }
}
