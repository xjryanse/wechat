<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use think\facade\Cache;

/**
 * 
 */
class WechatWePubOauthAccessTokenService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubOauthAccessToken';

    /**
     * token保存：20210923改缓存；20220429改session
     * @param array $data
     * @return boolean
     */
    public static function tokenSave($data) {
        $acid = Arrays::value($data, 'acid');
        $openid = Arrays::value($data, 'openid');
        if (!isset($data['openid']) || !$data['openid']) {
            return false;
        }
        $times = time() + $data['expires_in'];
        $data['expires_time'] = date('Y-m-d H:i:s', $times);
        Debug::debug(__CLASS__ . __FUNCTION__, $data);
        return session(self::tokenKey($openid, $acid), $data);
    }

    /**
     * 缓存key
     * @param type $openid
     * @param type $acid
     * @return type
     */
    protected static function tokenKey($openid, $acid) {
        $tokenKey = 'WechatWePubOauthAccessTokenService_' . $acid . "_" . $openid;
        Debug::debug(__CLASS__ . __FUNCTION__, $tokenKey);
        return $tokenKey;
    }

    public static function tokenGet($openid, $acid) {
        return session(self::tokenKey($openid, $acid)) ?: [];
    }

}
