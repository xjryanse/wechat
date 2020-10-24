<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

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
     * token保存
     * @param type $data
     * @return type
     */
    public static function tokenSave($data)
    {
        if(!isset($data['openid']) || !$data['openid']){
            return false;
        }
        $times = time() + $data['expires_in'];
        $data['expires_time'] = date('Y-m-d H:i:s',$times);
        //已有记录更新，没有记录新增
        $con[]  = ['openid','=',$data['openid']];
        $res = self::find( $con );
        if($res){
            $data['id'] = $res['id'];
            return self::update($data);
        } else {
            return self::save($data);
        }
    }    
}
