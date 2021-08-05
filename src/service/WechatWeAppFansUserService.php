<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\system\logic\FileLogic;
use xjryanse\user\service\UserService;
/**
 * 微信小程序粉丝用户绑定
 */
class WechatWeAppFansUserService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWeAppFansUser';

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
     * 获取绑定的用户id
     * @param type $openid      openid
     * @param type $scene       场景值
     * @param type $emptyCreate 无用户是否创建
     * @param type $extraData   用户创建时写入的额外信息
     * @return type
     */
    public static function getBindUserId( $openid, $scene="", $emptyCreate = false ,$extraData = [] )
    {
        //情况①：已经绑定直接返回
        $con    = [];
        $con[]  = ['openid','=',$openid];
        $con[]  = ['scene','=',$scene];
        $info   = self::find( $con ,0);   //无缓存查数据 
        if($info){
            return $info['user_id']; 
        }
        //情况②：没有用户信息的情况
        $bindWePubUserId = self::bindWePubUserId($openid);
        if($bindWePubUserId){
            return $bindWePubUserId;
        }
        //无用户时创建新用户
        if($emptyCreate){
            //情况③：没用户，创建新的，再绑定
            return self::bindNewGetUserId($openid, $scene, $extraData);
        } else {
            return '';
        }
    }
    /**
     * 新创建一个用户，并绑定
     * @param type $openid
     * @param type $scene
     * @param type $extraData
     */
    protected static function bindNewGetUserId( $openid, $scene="",$extraData = []  )
    {
        $weAppFans          = WechatWeAppFansService::findByOpenid($openid);
        //头像存本地
        $headImgInfo = FileLogic::saveUrlFile($weAppFans['avatar_url']);
        $userData   = Arrays::getByKeys($weAppFans->toArray(), ['nickname']);
        //头像
        $userData['headimg']    = $headImgInfo ? $headImgInfo['id'] : '';
        $userData['username']   = $openid;      //用openid作临时用户名
        $userDataMerge          = array_merge( $userData, $extraData );
        $userInfo               = UserService::save( $userDataMerge );                
        //创建后绑定
        $bindData = [];
        $bindData['acid']       = $weAppFans['acid'];
        $bindData['openid']     = $openid;
        $bindData['scene']      = $scene;
        $bindData['user_id']    = $userInfo['id'];
        $bindDataMerge = array_merge($extraData,$bindData);
        $resp = self::save($bindDataMerge);
        return $resp['user_id'];
    }
    /**
     * 绑定公众号的用户id
     * @param type $openid
     * @param type $scene
     */
    protected static function bindWePubUserId( $openid, $scene="" )
    {
        $weAppFans          = WechatWeAppFansService::findByOpenid($openid);
        $wePubFansOpenid    = WechatWePubFansService::unionidToOpenid( $weAppFans['unionid'] );
        $wePubBindUserId    = WechatWePubFansUserService::getUserIdByOpenid($wePubFansOpenid);
        if($wePubBindUserId){
            //将小程序和公众号绑定为同一个用户
            $bindData = [];
            $bindData['acid']       = $weAppFans['acid'];
            $bindData['openid']     = $openid;
            $bindData['scene']      = $scene;
            $bindData['user_id']    = $wePubBindUserId;
            $resp = self::save($bindData);
            return $resp['user_id'];
        }
        return '';
    }
    
}
