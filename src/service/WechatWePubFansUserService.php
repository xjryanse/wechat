<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信公众号粉丝用户绑定
 */
class WechatWePubFansUserService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWePubFansUser';

    /*
     * 根据user_id,取绑定的openid列表
     */
    public static function columnOpenidByUserId( $userId )
    {
        $con[] = [ 'user_id','=',$userId ];
        return self::column( 'openid', $con );
    }
}
