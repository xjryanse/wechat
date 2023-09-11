<?php
namespace xjryanse\wechat\model;

/**
 * 微信支付退款
 */
class WechatWxPayRefundLog extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'out_refund_no',
            // 去除prefix的表名
            'uni_name'  =>'finance_statement',
            'uni_field' =>'id',
            'in_exist'  => true,
            'del_check' => true,
            'del_msg'   => '该账单已退款，不可删'
        ],
        [
            'field'     =>'out_trade_no',
            // 去除prefix的表名
            'uni_name'  =>'finance_statement',
            'uni_field' =>'id',
            'in_exist'  => true,
            'del_check' => true,
            'del_msg'   => '该账单已退款，不可删'
        ],
        
        [
            'field'     =>'out_trade_no',
            // 去除prefix的表名
            'uni_name'  =>'wechat_wx_pay_log',
            'uni_field' =>'statement_id',
            'in_list'   => false,            
            'in_statics'=> true,
            'in_exist'  => false,
            'del_check' => false,
            'del_msg'   => ''
        ],
    ];

}