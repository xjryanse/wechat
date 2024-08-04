<?php
namespace xjryanse\wechat\model;

/**
 * 微信支付对账单
 */
class WechatWxPayCompare extends Base
{

    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'out_trade_no',
            'uni_name'  =>'finance_statement',
            'uni_field' =>'id',
            'in_list'   => false,
            'in_statics'=> false,
            'in_exist'  => true,
            'del_check' => true,
        ],
        [
            'field'     =>'out_refund_no',
            'uni_name'  =>'finance_statement',
            'uni_field' =>'id',
            'in_list'   => false,
            'in_statics'=> false,
            'in_exist'  => true,
            'del_check' => true,
        ],
    ];

    
}