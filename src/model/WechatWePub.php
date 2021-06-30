<?php
namespace xjryanse\wechat\model;

/**
 * 微信公众号账户表
 */
class WechatWePub extends Base
{
    public function getQrcodeAttr($value) {
        return self::getImgVal($value);
    }
    public function setQrcodeAttr($value) {
        return self::setImgVal($value);
    }
    
    public function getLogoAttr($value) {
        return self::getImgVal($value);
    }
    public function setLogoAttr($value) {
        return self::setImgVal($value);
    }
}