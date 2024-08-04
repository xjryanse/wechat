<?php

namespace xjryanse\wechat\service\weMsgLog;

use xjryanse\wechat\service\WechatWePubQrSceneScanLogService;
/**
 * 分页复用列表
 */
trait TriggerTraits{

    public static function extraPreUpdate(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }

    public static function extraPreSave(&$data, $uuid) {
        self::stopUse(__METHOD__);
    }

    public function extraPreDelete() {
        self::stopUse(__METHOD__);
    }

    public static function ramPreSave(&$data, $uuid) {
        WechatWePubQrSceneScanLogService::logFromMsg($uuid);
    }

    public static function ramPreUpdate(&$data, $uuid) {

    }
    /**
     * 20220810：增加判断
     * @throws Exception
     */
    public function ramPreDelete() {

    }

}
