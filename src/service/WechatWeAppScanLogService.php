<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\logic\ScoreLogic;
use think\facade\Request;

/**
 * 微信小程序浏览日志
 */
class WechatWeAppScanLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWeAppScanLog';
    //直接执行后续触发动作
    protected static $directAfter = true;

    /**
     * 浏览日志记录
     * @param type $weAppId
     * @param type $openid
     * @return type
     */
    public static function scanLog($weAppId, $openid) {
        $data['we_app_id'] = $weAppId;
        $data['openid'] = $openid;
        $data['user_id'] = session(SESSION_USER_ID);
        $data['ip'] = Request::ip();
        return self::save($data);
    }

    /**
     * 额外输入信息
     */
    public static function extraAfterSave(&$data, $uuid) {
        //判定订单完成，给下单人赠送积分的触发动作
        ScoreLogic::score(session(SESSION_USER_ID));
        return $data;
    }

}
