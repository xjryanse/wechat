<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use think\facade\Cache;
/**
 * 
 */
class WechatWePubOauthAccessTokenService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWePubOauthAccessToken';

    /**
     * token保存：20210923改缓存
     * @param array $data
     * @return boolean
     */
    public static function tokenSave($data){
        $acid   = Arrays::value($data, 'acid');
        $openid = Arrays::value($data, 'openid');
        if(!isset($data['openid']) || !$data['openid']){
            return false;
        }
        $times = time() + $data['expires_in'];
        $data['expires_time'] = date('Y-m-d H:i:s',$times);
        return Cache::set(self::tokenKey($openid, $acid), $data);
    }
    /**
     * 缓存key
     * @param type $openid
     * @param type $acid
     * @return type
     */
    protected static function tokenKey($openid,$acid){
        return 'WechatWePubOauthAccessTokenService_'.$acid."_".$openid;
    }
    
    public static function tokenGet($openid,$acid){
        return Cache::get(self::tokenKey($openid, $acid)) ? : [];
    }
}
