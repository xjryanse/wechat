<?php
namespace xjryanse\wechat\model;

/**
 * 微信公众号账户表
 */
class WechatWePub extends Base
{
        //20230728 是否将数据缓存到文件
    public static $cacheToFile = true;

    public static $picFields = ['qrcode','logo'];
    
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