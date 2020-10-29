<?php
namespace xjryanse\wechat\WePub;

use xjryanse\wechat\service\WechatWePubFansUserService;
use xjryanse\user\service\UserService;
/*
 * 绑定用户逻辑类库
 */
class BindUser
{
    /**
     * 获取绑定的用户id
     */
    public static function getBindUserId( $openid, $scene="default", $emptyCreate = false  )
    {
        $con    = [];
        $con[]  = ['openid','=',$openid];
        $con[]  = ['scene','=',$scene];
        $info   = WechatWePubFansUserService::find( $con ); 
        if( !$info && $emptyCreate){
            //创建空用户
            $userInfo = UserService::save([]);
            $data['openid']     = $openid;
            $data['scene']      = $scene;
            $data['user_id']    = $userInfo['id'];
            $info = WechatWePubFansUserService::save( $data );
        }

        return $info ? $info['user_id'] : '';
    }
}
