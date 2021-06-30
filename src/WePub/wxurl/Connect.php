<?php
namespace xjryanse\wechat\WePub\wxurl;
use xjryanse\logic\Url;
use think\facade\Request;

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
    
    public function oauth2Authorize( $acid ,$scope="")
    {
        if($acid){
            $this->redirectUri = Url::addParam($this->redirectUri, ['acid'=>$acid]);
        }
        $respp = explode('?',$this->redirectUri);
        //有参数，取参数，无参数，放空
        $params = isset($respp[1]) ? equalsToKeyValue($respp[1]):[];
        $this->redirectUri = urlencode(Url::addParam($this->redirectUri, array_merge($params,['sessionid'=> session_id()])));
        //本地真香调试【20210609】
        if(in_array(Request::ip(),['127.0.0.1','::1'])){
            $this->redirectUri = 'http://tenancy.xiesemi.cn/wechat/we_pub/local?backUrl='.urlencode($this->redirectUri);    
        }
        
        //把当前会话的sessionid放到链接中，便于微信回调时识别
        $this->state = session_id();
        $class = (new \ReflectionClass( __CLASS__ ))->getShortName();
        
        $name   = lcfirst( $class ). ucfirst( __FUNCTION__ );
        $url    = BaseUrlTpl::$urlTpl[$name];
        if($scope){
            $url = str_replace( "=SCOPE",        '='.$scope,       $url);
        }
        
        return $this->replace( $url );
    }
}
