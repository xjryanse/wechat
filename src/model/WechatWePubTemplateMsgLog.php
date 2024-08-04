<?php
namespace xjryanse\wechat\model;

/**
 * 模板消息发送记录
 */
class WechatWePubTemplateMsgLog extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'from_table_id',
            // 去除prefix的表名
            'uni_name'  =>'finance_account_log',
            'uni_field' =>'id',
        ],
        [
            'field'     =>'from_table_id',
            // 去除prefix的表名
            'uni_name'  =>'order',
            'uni_field' =>'id',
        ],
        [
            'field'     =>'from_table_id',
            // 去除prefix的表名
            'uni_name'  =>'approval_thing_node',
            'uni_field' =>'id',
        ],
        [
            'field'     =>'from_table_id',
            // 去除prefix的表名
            'uni_name'  =>'finance_statement',
            'uni_field' =>'id',
        ],
        /*
         * 20231019：性能渣渣
        [
            'field'     =>'user_id',
            // 去除prefix的表名
            'uni_name'  =>'user',
            'uni_field' =>'id',
            'in_exist'  => true,
            'in_statics'  => false,
        ],
         */
    ];
}