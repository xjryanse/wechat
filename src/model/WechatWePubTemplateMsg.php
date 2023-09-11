<?php
namespace xjryanse\wechat\model;

/**
 * 模板消息
 */
class WechatWePubTemplateMsg extends Base
{
    //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;

    public function getReplaceRuleAttr($value)
    {
        return $value ? json_decode($value,true) : [];
    }

    /**
     * 描述图标
     * @param type $value
     * @return type
     */
    public function getDescribeAttr($value) {
        return self::getImgVal($value);
    }

    /**
     * 描述图标
     * @param type $value
     * @throws \Exception
     */
    public function setDescribeAttr($value) {
        return self::setImgVal($value);
    }
}