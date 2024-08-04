<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Cachex;

/**
 * 微信公众号场景值二维码
 */
class WechatWePubQrcodeService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubQrcode';
    //直接执行后续触发动作
    protected static $directAfter = true;

//    public function fOpenid() {
//        return $this->getFFieldValue(__FUNCTION__);
//    }
//
//    public function fNickname() {
//        return $this->getFFieldValue(__FUNCTION__);
//    }
//
//    public function fSubscribe() {
//        return $this->getFFieldValue(__FUNCTION__);
//    }

}
