<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
/**
 * 微信公众号粉丝
 */
class WechatWePubFansService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWePubFans';

    public function extraPreDelete(){
        self::checkTransaction();
        $userInfo   = self::getInstance( $this->uuid )->get();
        $con[]      = ['openid','=',Arrays::value( $userInfo , 'openid')];
        $lists = WechatWePubFansUserService::lists( $con );
        foreach( $lists as $fansUser){
            WechatWePubFansUserService::getInstance( $fansUser['id'] )->delete();
        }
    }    
    
    public static function addOpenid( $openid ,$acid )
    {
        if(!$openid){
            return false;
        }
        if( self::isOpenidExists( $openid )){
            return false;
        }
        $data['acid']   = $acid;
        $data['openid'] = $openid;
        return self::save($data);
    }

    public static function isOpenidExists( $openid )
    {
        $con[] = ['openid','=',$openid];
        return self::ids( $con );
    }
    /**
     * 更新用户信息
     * @param type $data
     * @return boolean
     */
    public static function updateInfo( $data )
    {
        if(!isset( $data['openid']) || !$data['openid']){
            return false;
        }
        $openid = $data['openid'];
        if(isset($data['id'])){
            unset( $data['id']);
        }
        if(isset( $data['subscribe_time']) ){
            $data['subscribe_time2'] = date('Y-m-d H:i:s',$data['subscribe_time']);
        }
        if( self::isOpenidExists($openid) ){
            //更新
            $res = self::mainModel()->where('openid',$openid)->update( $data );
        } else {
            //新增
            $res = self::save( $data );
        }
        return $res;
    }
    /**
     * 根据openid 反查获取acid
     */
    public static function getAcidByOpenid($openid)
    {
        $con[] = ['openid','=',$openid];
        $info = self::mainModel()->where($con)->find();
        return $info ? $info['acid'] : 0 ;
    }    
    /**
     * 通过openid取单条数据
     */
    public static function findByOpenid( $openid )
    {
        $con[] = [ 'openid', '=', $openid ];
        $info = self::mainModel()->where($con)->find();
        return $info;
    }
}
