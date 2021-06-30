<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信公众号账户
 */
class WechatWePubService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWePub';

    
    public function fQrcode() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fLogo() {
        return $this->getFFieldValue(__FUNCTION__);
    }
}
