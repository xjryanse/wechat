<?php
namespace xjryanse\wechat\WePub\wxurl;

use xjryanse\logic\Debug;
use xjryanse\system\logic\ConfigLogic;
use think\facade\Request;

abstract class Base
{
    protected $appId          = '';
    protected $appSecret      = '';
    protected $accessToken    = '';
    protected $scope          = 'snsapi_userinfo';
    protected $code           = '';
    protected $state          = '';
    protected $openid         = '';
    protected $redirectUri    = ''; //'https://wuser.xiesemi.cn/uwechat/wx/authorize';//code回调

    public function __construct($appid, $secret, $token = null,$redirectUri='')
    {
        if ($appid && $secret) {
            $this->appId     = $appid;
            $this->appSecret = $secret;

            if (!empty($token)) {
                $this->accessToken = $token;
            }
        } else {
            throw new \Exception('appid和secret参数错误！');
        }
        //回调地址
        $this->redirectUri = $redirectUri ? : Request::domain( ).'/'.session(SESSION_COMPANY_KEY).'/wechat/we_pub/authorize';
    }
    /**
     * 设定AccessToken
     */
    public function setAccessToken( $accessToken )
    {
        $this->accessToken = $accessToken;
    }    
    /**
     * 参数替换
     * @param type $url
     * @return type
     */
    protected function replace($url='')
    {
        $url = str_replace( "=APPID",        '='.$this->appId,       $url);
        $url = str_replace( "=SECRET",       '='.$this->appSecret,   $url);
        $url = str_replace( "=ACCESS_TOKEN", '='.$this->accessToken, $url);
        $url = str_replace( "=REDIRECT_URI", '='.$this->redirectUri, $url);
        $url = str_replace( "=SCOPE",        '='.$this->scope,       $url);
        $url = str_replace( "=CODE",         '='.$this->code,        $url);       
        $url = str_replace( "=OPENID",       '='.$this->openid,      $url);        
        $url = str_replace( "=STATE",        '='.$this->state,       $url);
        
        return $url;
    }
    /**
     * 取链接
     * @param type $class
     * @param type $method
     */
    protected function getMyUrl( $class, $method )
    {
        $name   = lcfirst( $class ). ucfirst( $method );
        Debug::debug('Base getMyUrl() $name',$name);
        Debug::debug('Base getMyUrl() $urlTpl',BaseUrlTpl::$urlTpl);
        $url    = BaseUrlTpl::$urlTpl[$name];
        $realUrl = $this->replace( $url );
        //本地真香调试【20210609】
        if(in_array(Request::ip(),['127.0.0.1','::1'])){
            $wxRedirectBaseUrl = $this->getWxRedirectBaseUrl();
            $realUrl = $wxRedirectBaseUrl.'/wechat/we_pub/query?url='. urlencode($realUrl);    
        }
        return $realUrl;
    }
    /**
     * 获取微信授权跳转基本URL，用于本地真香调试
     */
    protected function getWxRedirectBaseUrl(){
        return ConfigLogic::config('wxRedirectBaseUrl') ? : 'http://axsl.xiesemi.cn/';        
    }
}
