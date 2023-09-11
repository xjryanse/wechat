<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 消息记录
 */
class WechatWeMsgLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWeMsgLog';

    public function fOpenid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fNickname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fSubscribe() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
