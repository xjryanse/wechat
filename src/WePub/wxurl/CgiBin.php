<?php
namespace xjryanse\wechat\WePub\wxurl;

class CgiBin extends Base
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

    public function jsapiTicket( $access_token )
    {
        $this->accessToken  = $access_token;
        $class = (new \ReflectionClass( __CLASS__ ))->getShortName();
        return $this->getMyUrl($class, __FUNCTION__);
    }
}
