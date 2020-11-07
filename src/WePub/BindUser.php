<?php
namespace xjryanse\wechat\WePub;

use xjryanse\wechat\service\WechatWePubFansUserService;
use xjryanse\wechat\service\WechatWePubFansService;
use xjryanse\user\service\UserService;
use xjryanse\logic\Arrays;
/*
 * 绑定用户逻辑类库
 */
class BindUser
{
    /**
     * 获取绑定的用户id
     * @param type $openid      openid
     * @param type $scene       场景值
     * @param type $emptyCreate 无用户是否创建
     * @param type $data        用户创建时写入的额外信息
     * @return type
     */
    public static function getBindUserId( $openid, $scene="", $emptyCreate = false ,$extraData = [] )
    {
        $con    = [];
        $con[]  = ['openid','=',$openid];
        $con[]  = ['scene','=',$scene];
        $info   = WechatWePubFansUserService::find( $con ); 
        if( !$info && $emptyCreate){
            //创建空用户
            $fansInfo = WechatWePubFansService::findByOpenid($openid);
            //昵称头像存储
            $userData   = Arrays::getByKeys($fansInfo->toArray(), ['nickname','headimgurl']);
            $userData1  = array_merge( $userData, $extraData );
            $userInfo = UserService::save( $userData1 );
            $data['openid']     = $openid;
            $data['scene']      = $scene;
            $data['user_id']    = $userInfo['id'];
            $info = WechatWePubFansUserService::save( $data );
        }

        return $info ? $info['user_id'] : '';
    }
    /**
     * 用户改绑
     * @param type $openid
     * @param type $userId
     * @param type $scene
     * @param type $deleteIfEmpty   若先前的用户为空，是否删除？默认不删
     */
    public static function changeBind( $openid, $userId, $scene= "", $deleteIfEmpty = false)
    {
        $info = self::getBindUserId($openid, $scene);
        if(!$info || ( $info && $info['user_id'] == $userId ) ){
            //相同用户不操作
            return false;
        }
        $userInfo = UserService::getInstance($info['user_id'])->get();
        //如果为空,且强制删用户，则删
        if($info && $deleteIfEmpty && !$userInfo['username']){
            UserService::getInstance($info['user_id'])->delete();
        }
        //绑定信息
        $con    = [];
        $con[]  = ['openid','=',$openid];
        $con[]  = ['scene','=',$scene];
        $bind = WechatWePubFansUserService::find( $con );
        if( $bind ){
            //更新绑定记录
            return WechatWePubFansUserService::getInstance($bind['id'])->update(['user_id'=>$userId]);
        } else {
            //新增绑定记录
            $data['openid']     = $openid;
            $data['scene']      = $scene;
            $data['user_id']    = $userInfo['id'];
            $info = WechatWePubFansUserService::save( $data );
        }
    }
}
