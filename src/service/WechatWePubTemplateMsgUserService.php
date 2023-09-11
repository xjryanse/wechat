<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\wechat\service\WechatWePubFansUserService;

/**
 * 模板消息
 */
class WechatWePubTemplateMsgUserService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubTemplateMsgUser';

    public static function keyUserIds($key) {
        $con[] = ['template_key', '=', $key];
        $con[] = ['status', '=', 1];
        $userIds = self::staticConColumn('user_id', $con);
        return $userIds;
    }

    public static function keyOpenids($key) {
        $userIds = self::keyUserIds($key);
        $cond[] = ['user_id', 'in', $userIds];
        // $cond[] = ['user_id','=',$info['seller_user_id']];
        $openids = WechatWePubFansUserService::column('openid', $cond);
        return $openids;
    }

}
