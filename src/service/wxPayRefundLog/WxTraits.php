<?php

namespace xjryanse\wechat\service\wxPayRefundLog;

use xjryanse\wechat\service\WechatWxPayConfigService;
use xjryanse\finance\service\FinanceStatementService;
use xjryanse\wechat\WxPay\v2\WxPayApiXie;
use xjryanse\logic\Arrays;
use Exception;
/**
 * 字段复用列表
 */
trait WxTraits {

    /**
     * 20240906：微信退款查单
     * @param type $statementId
     * @return bool
     * @throws Exception
     */
    public static function wxRefundQuery($statementId){
        $statement = FinanceStatementService::getInstance($statementId)->get();
        if (!$statement) {
            throw new Exception('查单失败:账单不存在'.$statementId);
        }
        // 20230903：本地不查
        if(Arrays::value($_SERVER, 'SERVER_NAME') == 'localhost'){
            return false;
        }
        // TODO:实例类对象如何缓存？？
        $con[] = ['company_id', '=', $statement['company_id']];
        $config = WechatWxPayConfigService::mainModel()->where($con)->find();
        if(!$config){
            return false;
        }

        $inst = new WxPayApiXie();
        $inst->setConf($config);

        $res = $inst->refundQueryByRefundStatement($statement);
        return $res;
    }
    
    public static function wxRefundQueryByPayStatement($payStatementId){
        $statement = FinanceStatementService::getInstance($payStatementId)->get();
        if (!$statement) {
            throw new Exception('查单失败:源账单不存在'.$payStatementId);
        }
        // 20230903：本地不查
        if(Arrays::value($_SERVER, 'SERVER_NAME') == 'localhost'){
            return false;
        }
        // TODO:实例类对象如何缓存？？
        $con[] = ['company_id', '=', $statement['company_id']];
        $config = WechatWxPayConfigService::mainModel()->where($con)->find();
        if(!$config){
            return false;
        }

        $inst = new WxPayApiXie();
        $inst->setConf($config);

        $res = $inst->refundQuery($statement);
        return $res;
    }

}
