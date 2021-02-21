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
    /**
     * 根据userId取openid
     * @param type $openid
     * @param type $scene
     * @return type
     */
    public static function getUserIdByOpenid( $openid,$scene = '')
    {
        $con[] = ['openid','=',$openid ];
        if( $scene ){
            $con[] = ['scene','=',$scene ];
        }
        $info = self::find( $con );
        return $info ? $info['user_id'] : '';
    }
    /**
     * 公司id
     * @param type $openid
     * @param type $scene
     * @return type
     */
    public static function getCompanyIdByOpenid( $openid,$scene = '' )
    {
        $con[] = ['openid','=',$openid ];
        if( $scene ){
            $con[] = ['scene','=',$scene ];
        }
        $info = self::find( $con );
            
        return $info ? $info['company_id'] : '';        
    }
}
