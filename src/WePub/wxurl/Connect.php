<?php
namespace xjryanse\wechat\WePub\wxurl;

class Connect extends Base
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
    
    public function oauth2Authorize( $acid )
    {
        if($acid){
            $this->redirectUri = $this->redirectUri.'/acid/'.$acid;
        }
        //把当前会话的sessionid放到链接中，便于微信回调时识别
        $this->state = session_id();
        $class = (new \ReflectionClass( __CLASS__ ))->getShortName();
        return $this->getMyUrl($class, __FUNCTION__);
    }
}
