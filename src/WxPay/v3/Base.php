<?php
namespace xjryanse\wechat\WxPay\v3;

use xjryanse\wechat\model\WechatWxPayConfig;
/**
 * 
 * 微信支付API基本类
 * @author widyhu
 *
 */
abstract class Base {
    /**
     * 支付配置
     * @var type 
     */
    protected $payConf;
    
    protected $values = [];
    /**
     * 设置支付参数
     * @param WechatWxPayConfig $config
     */
    public function setConf(WechatWxPayConfig $config){
        $this->payConf = $config;
    }

}
