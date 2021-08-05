<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信小程序粉丝
 */
class WechatWeAppFansService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWeAppFans';

    /**
     * unionid转openid
     * @param type $unionid
     * @return type
     */
    public static function unionidToOpenid($unionid)
    {
        $con[] = ['unionid','=',$unionid];
        return self::mainModel()->where($con )->value('openid');
    }
    
    /**
     * 通过openid取单条数据
     */
    public static function findByOpenid( $openid )
    {
        $con[] = [ 'openid', '=', $openid ];
        $info = self::mainModel()->where($con)->cache(86400)->find();
        return $info;
    }

}
