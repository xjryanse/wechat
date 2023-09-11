<?php
namespace xjryanse\wechat\WePub\wxurl;

use xjryanse\logic\Url;
use think\facade\Request;
use xjryanse\logic\Debug;
use xjryanse\system\logic\ConfigLogic;

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
        $params['sessionid'] = session_id();
        if(Debug::isDebug()){
            $params['debug'] = 'xjryanse';
            dump('Request::domain()');
            dump(Request::host());
        }
        
        $this->redirectUri = urlencode(Url::addParam($this->redirectUri, $params));
        //本地真香调试【20210609】TODO
        // 配置的域名数组
        // if(in_array(Request::ip(),['127.0.0.1','::1']) || in_array(Request::host(),['jksh.xiesemi.cn','xywxtest.xiesemi.cn']) ){
        // 20230723:微信授权是否走代理
        if(in_array(Request::ip(),['127.0.0.1','::1']) || ConfigLogic::config('isWxAuthProxy')){
            $wxRedirectBaseUrl = $this->getWxRedirectBaseUrl();
            $this->redirectUri = $wxRedirectBaseUrl.'/wechat/we_pub/local?backUrl='.urlencode($this->redirectUri); 
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
