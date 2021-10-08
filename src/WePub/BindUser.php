<?php
namespace xjryanse\wechat\WePub;

use xjryanse\wechat\service\WechatWePubFansUserService;
use xjryanse\wechat\service\WechatWePubFansService;
use xjryanse\user\service\UserService;
use xjryanse\logic\Arrays;
use xjryanse\system\logic\FileLogic;
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
     * @param type $extraData   用户创建时写入的额外信息
     * @return type
     */
    public static function getBindUserId( $openid, $scene="", $emptyCreate = false ,$extraData = [] )
    {
//        $con    = [];
//        $con[]  = ['openid','=',$openid];
//        $con[]  = ['scene','=',$scene];
//        $info   = WechatWePubFansUserService::find( $con ,0);   //无缓存查数据 
        $userId   = WechatWePubFansUserService::getUserIdByOpenid($openid, $scene);
        if( !$userId && $emptyCreate){
            $cond = [];
            $cond[]      = ['username','=',$openid];
            $userInfo   = UserService::find( $cond );
            if( !$userInfo ){
                //创建空用户
                $fansInfo = WechatWePubFansService::findByOpenid($openid);
                //头像下载到本地服务器
                $headImgInfo = FileLogic::saveUrlFile($fansInfo['headimgurl']);
                //昵称头像存储
                $userData   = Arrays::getByKeys($fansInfo->toArray(), ['nickname']);
                //头像
                $userData['headimg']    = $headImgInfo ? $headImgInfo['id'] : '';
                $userData['username']   = $openid;      //用openid作临时用户名
                $userData1  = array_merge( $userData, $extraData );
                $userInfo = UserService::save( $userData1 );                
            }

            $data['openid']     = $openid;
            $data['scene']      = $scene;
            $data['user_id']    = $userInfo['id'];
            $info = WechatWePubFansUserService::save( $data );
            $userId = $userInfo['id'];
        }

        return $userId;
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
        $bindUserId = self::getBindUserId($openid, $scene);
        if(!$bindUserId || $bindUserId == $userId ){
            //相同用户不操作
            return false;
        }
        $userInfo = UserService::getInstance($bindUserId)->get();
        //如果为空,且强制删用户，则删
        //删空用户，或以openid为用户名的用户
        if( $bindUserId && $deleteIfEmpty && (!$userInfo['username'] || $userInfo['username'] == $openid)){
            //没用了，直接强删
            UserService::mainModel()->where(['id'=>$bindUserId])->delete();
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
            $data['user_id']    = $userId;
            return WechatWePubFansUserService::save( $data );
        }
    }
    
    /**
     * 粉丝设定手机号码，返回用户id
     * 适用于手机号码在用户表中唯一的场景
     */
    public static function fansSetPhoneGetUserId($openid, $userId, $phone ,$scene="" )
    {
        //微信环境下，根据手机号码获取用户信息
        $phoneUserInfo = UserService::getUserInfoByPhone( $phone );
        
        //若手机号码取到了用户，且用户id一致直接返回。
        if( $phoneUserInfo && $phoneUserInfo['id'] == $userId ){
            return $userId;
        }
        //若手机号码取到了用户，且用户id不一致，改绑。
        if( $phoneUserInfo && $phoneUserInfo['id'] != $userId ){
            //绑定用户变更
            self::changeBind( $openid , $phoneUserInfo['id'] ,$scene, true);
            return $phoneUserInfo['id'];
        }
        $userInfo = UserService::getInstance( $userId )->get();
        //若手机号码未取到用户信息，且当前用户手机号码为空，手机号码写入当前用户
        if( !$phoneUserInfo && !$userInfo['phone'] ){
            //更新用户名和手机号码
            UserService::getInstance( $userId )->update(['username'=>$phone,'phone'=>$phone]);
            return $userId;
        }
        //若手机号码未取到用户信息，且当前用户名存在，新增用户，并改绑
        if( !$phoneUserInfo && $userInfo['username'] ){
            $res = UserService::save(['username'=>$phone,'phone'=>$phone]);
            //绑定用户变更
            self::changeBind( $openid, $res['id'], $scene, true );
            return $res['id'];
        }
    }
}
