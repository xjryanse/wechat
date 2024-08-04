<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\wechat\service\WechatWxPayConfigService;
use xjryanse\wechat\WxPay\v2\WxPayApiXie;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\DbOperate;
use Exception;
/**
 * 微信支付对账单
 */
class WechatWxPayCompareService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWxPayCompare';
    //直接执行后续触发动作
    protected static $directAfter = true;

    use \xjryanse\wechat\service\wxPayCompare\DoTraits;
    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    return $lists;
                }, true);
    }
    
    
    /**
     * 20240722:按日拉取微信支付账单
     */
    public static function syncByDate($date) {
        $dateStr    = date('Ymd', strtotime($date));
        
        $companyId  = session(SESSION_COMPANY_ID);
        if(!$companyId){
            throw new Exception('未指定company');
        }
        // 【1】提取参数配置
        $config = WechatWxPayConfigService::getByCompanyId($companyId);
        // 【2】从微信服务器拉取账单
        $inst = new WxPayApiXie();
        $inst->setConf($config);
        // 0-账单明细；1-账单统计
        $res = $inst->downloadBill($dateStr);
        $arr = self::dataMatch($res[0]);
        // 20240722 
        self::saveAllRam($arr);

        DbOperate::dealGlobal();
        return true;
    }

    /**
     * 微信拉取的csv账单转换数组，键名是中文
     * @param type $arr
     */
    protected static function dataMatch($arr){
        $keys = [
            '交易时间'      =>'bill_time',
            '公众账号ID'    =>'appid',
            '商户号'        =>'mch_id',
            '特约商户号'    =>'mch_special',
            '设备号'        =>'equip_no',
            '微信订单号'    =>'transaction_id',
            '商户订单号'    =>'out_trade_no',
            '用户标识'      =>'openid',
            '交易类型'      =>'trade_type',
            '交易状态'      =>'trade_state',
            '付款银行'      =>'bank_type',
            '货币种类'      =>'fee_type',
            '应结订单金额'  =>'settle_prize',
            '代金券金额'    =>'coupon_prize',
            '微信退款单号'  =>'refund_transaction_id',
            '商户退款单号'  =>'out_refund_no',
            '退款金额'      =>'pay_refund_prize',
            '充值券退款金额' =>'coupon_refund_prize',
            '退款类型'      =>'refund_type',
            '退款状态'      =>'refund_state',
            '商品名称'      =>'goods_name',
            '商户数据包'    =>'attach',
            '手续费'        =>'charge_prize',
            '费率'          =>'charge_rate',
            '订单金额'      =>'order_prize',
            '申请退款金额'  =>'refund_apply_prize',
            '费率备注'      =>'charge_rate_comment',
        ];
        $resArr = Arrays2d::keyReplace($arr, $keys);
        return $resArr;
    }
}
