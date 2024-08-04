<?php
namespace xjryanse\wechat\model;

/**
 * 粉丝用户绑定表
 */
class WechatWePubFansUser extends Base
{
    use \xjryanse\traits\ModelUniTrait;
    // 20230516:数据表关联字段
    public static $uniFields = [
        [
            'field'     =>'user_id',
            // 去除prefix的表名
            'uni_name'  =>'user',
            'uni_field' =>'id',
            'del_check' => false,
            'del_msg'   => '该用户有绑定公众号信息'
        ]
    ];
}