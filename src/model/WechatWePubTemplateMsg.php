<?php
namespace xjryanse\wechat\model;

/**
 * 模板消息
 */
class WechatWePubTemplateMsg extends Base
{

    public function getReplaceRuleAttr($value)
    {
        return $value ? json_decode($value,true) : [];
    }

}