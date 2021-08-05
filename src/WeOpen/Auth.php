<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\wechat\service\WechatWeOpenService;
use xjryanse\wechat\service\WechatWeOpenAuthorizeService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use think\facade\Cache;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Before_Develop/Authorization_Process_Technical_Description.html
 * 授权流程
 */
class Auth extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;

    /**
     * 调用接口获取预授权码
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/pre_auth_code.html
     * 参数	类型	必填	说明
component_access_token	string	是	第三方平台component_access_token，不是authorizer_access_token
component_appid	string	是	第三方平台 appid
     */
    public function getPreAuthCode()
    {
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$componentAccessToken;
        $data['component_appid']    = $this->appId;
        $res        = Query::posturl($url, $data);
        return $res['pre_auth_code'];
    }
    /**
     * 使用授权码获取授权信息
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/authorization_info.html
     * 参数             类型	必填	说明
component_access_token	string	是	第三方平台component_access_token，不是authorizer_access_token
component_appid	string	是	第三方平台 appid
authorization_code	string	是	授权码, 会在授权成功时返回给第三方平台，详见第三方平台授权流程说明
     */
    public function getApiQueryAuth( $authorizationCode )
    {
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();
        $url ='https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$componentAccessToken;
        $data['component_access_token']     = $componentAccessToken;
        $data['component_appid']            = $this->appId;
        $data['authorization_code']         = $authorizationCode;
        $res        = Query::posturl($url, $data);
        return $res;
    }
    
    /**
     * 构建PC端授权链接
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Before_Develop/Authorization_Process_Technical_Description.html
     * 参数	必填	
component_appid	是	第三方平台方 appid
pre_auth_code	是	预授权码
redirect_uri	是	回调 URI
auth_type	是	要授权的帐号类型：1 则商户点击链接后，手机端仅展示公众号、2 表示仅展示小程序，3 表示公众号和小程序都展示。如果为未指定，则默认小程序和公众号都展示。第三方平台开发者可以使用本字段来控制授权的帐号类型。
biz_appid	否	指定授权唯一的小程序或公众号
     */
    public function pcAuthUrl($authType = 3)
    {
        $preAuthCode    = $this->getPreAuthCode();
        $appId          = $this->appId;
        $redirectUrl    = WechatWeOpenService::mainModel()->where('id',$this->uuid)->value('authCallBackUrl');

        $url = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid='.$appId.'&pre_auth_code='.$preAuthCode.'&redirect_uri='.$redirectUrl.'&auth_type='.$authType;
        return $url;
    }
    
    public function webAuthUrl($authType = 3)
    {
        $preAuthCode    = $this->getPreAuthCode();
        $appId          = $this->appId;
        $redirectUrl    = WechatWeOpenService::mainModel()->where('id',$this->uuid)->value('authCallBackUrl');
        
        $url = 'https://mp.weixin.qq.com/safe/bindcomponent?action=bindcomponent&no_scan=1&component_appid='.$appId.'&pre_auth_code='.$preAuthCode.'&redirect_uri='.$redirectUrl.'&auth_type='.$authType.'#wechat_redirect';
        return $url;
    }
    /**
     * 获取/刷新接口调用令牌
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/api_authorizer_token.html
     * @param type $authorizerAppid     授权方appid
     *      */
    public function apiAuthorizerToken($authorizerAppid){
        $key = 'WeOpen_ApiComponentToken'.$this->uuid.$authorizerAppid;
        $apiAuthorizerToken = Cache::get( $key );
        Debug::debug('apiAuthorizerToken 的 缓存',$apiAuthorizerToken );
        if(!$apiAuthorizerToken || $apiAuthorizerToken['expires_time'] < time()){
            $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$componentAccessToken;
            $data['component_appid']            = $this->appId;
            $data['authorizer_appid']           = $authorizerAppid;
            $authorizeData  = WechatWeOpenAuthorizeService::getByAuthorizerAppid($authorizerAppid);
            $data['authorizer_refresh_token']   =  Arrays::value($authorizeData, 'authorizer_refresh_token');
            $res        = Query::posturl($url, $data);
            $res['acid']            = $this->uuid;
            $res['expires_time']    = time() + $res['expires_in'] - 60;
            Debug::debug('apiAuthorizerToken 的 远程',$res);
            if($res['authorizer_access_token']){
                Cache::set($key,$res);
            }
            $apiAuthorizerToken = $res;
        }
        return $apiAuthorizerToken['authorizer_access_token'];
    }
    
    /**
     * 获取授权账号信息
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/api_get_authorizer_info.html
     */
    public function apiGetAuthorizerInfo($authorizerAppid){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();
        $url ='https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$componentAccessToken;
        $data['component_access_token']     = $componentAccessToken;
        $data['component_appid']            = $this->appId;
        $data['authorizer_appid']           = $authorizerAppid; //授权方appid
        $res        = Query::posturl($url, $data);
        return $res;
    }
    
}
