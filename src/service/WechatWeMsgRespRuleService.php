<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 消息回复规则
 */
class WechatWeMsgRespRuleService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWeMsgRespRule';

    /**
     * 获取回复消息规则
     * @param type $msgType     消息类型
     * @param type $event       事件key
     * @param type $sessionFrom 小程序sessionFrom参数
     * @param type $fromMsg     关键词
     */
    public static function getResponse($msgType, $event, $sessionFrom, $fromMsg = '') {
        $con[] = ['MsgType', '=', $msgType];
        if ($event) {
            $con[] = ['Event', '=', $event];
        }
        if ($sessionFrom) {
            $con[] = ['SessionFrom', '=', $sessionFrom];
        }
        // 20230721
        $con[] = ['status','=',1];
        $list = self::staticConList($con, '', 'sort');
        foreach ($list as &$v) {
            if ($fromMsg && $v['keyword'] && strstr($fromMsg, $v['keyword'])) {
                return $v;
            }
        }
        return $list ? $list[0] : [];
    }

}
