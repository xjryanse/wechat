<?php
namespace xjryanse\wechat\model;

/**
 * 微信支付记录
 */
class WechatWxPayLog extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'statement_id',
            'uni_name'  =>'finance_statement',
            'uni_field' =>'id',
            'in_list'   => false,
            'in_statics'=> false,
            'in_exist'  => true,
            'del_check' => true,
        ],
    ];

    public static $uniRevFields = [
        [
            'table'     =>'finance_account_log',
            'field'     =>'from_table_id',
            'uni_field' =>'id',
            'exist_field'   =>'isFinanceAccountLogExist',
            'condition'     =>[
                // 关联表，即本表
                'from_table'=>'{$uniTable}'
            ]
        ],
    ];
}