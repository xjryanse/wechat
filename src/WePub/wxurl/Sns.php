<?php
namespace xjryanse\wechat\WePub\wxurl;

class Sns extends Base
{
    /**
     * 空方法，偷懒
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public function __call($name,$arguments) { 
        $class = (new \ReflectionClass( __CLASS__ ))->getShortName();
        return $this->getMyUrl($class, $name );
    }
    /**
     * 用户信息
     * @param type $openid
     * @param type $access_token
     * @return type
     */
    public function userInfo($openid,$access_token)
    {
        $this->openid       = $openid;
        $this->accessToken  = $access_token;
        $class = (new \ReflectionClass( __CLASS__ ))->getShortName();
        return $this->getMyUrl($class, __FUNCTION__);
    }
    
    /**
     * code换用户access_token
     * @param type $openid
     * @param type $access_token
     * @return type
     */
    public function oauth2AccessToken( $code )
    {
        $this->code = $code;
        $class = (new \ReflectionClass( __CLASS__ ))->getShortName();
        return $this->getMyUrl($class, __FUNCTION__);
    }    
}
