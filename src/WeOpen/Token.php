<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\wechat\service\WechatWeOpenService;
use xjryanse\logic\Debug;
use think\facade\Cache;
/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Before_Develop/Authorization_Process_Technical_Description.html
 * 令牌管理
 */
class Token extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    /**
     * 获取令牌
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/component_access_token.html
     * 参数	类型	必填	说明
        component_appid	string	是	第三方平台 appid
        component_appsecret	string	是	第三方平台 appsecret
        component_verify_ticket	string	是	微信后台推送的 ticket
     */
    public function getApiComponentToken()
    {
        $key = 'WeOpen_ApiComponentToken'.$this->uuid;
        Debug::debug('getApiComponentToken 的 缓存key',$key);
        $apiComponentToken = Cache::get($key);
        Debug::debug('getApiComponentToken 的 缓存',$apiComponentToken);
        if(!$apiComponentToken || $apiComponentToken['expires_time'] < time()){
            //从微信服务器获取accessToken
            $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
            $ticket     = WechatWeOpenService::mainModel()->where('id',$this->uuid)->value('ComponentVerifyTicket');
            //post数据
            $data['component_appid']            =   $this->appId;
            $data['component_appsecret']        =   $this->appSecret;
            $data['component_verify_ticket']    =   $ticket;
            $res = Query::posturl($url, $data);
            $res['acid']            = $this->uuid;
            $res['expires_time']    = time() + $res['expires_in'] - 60;
            Debug::debug('getApiComponentToken 的 远程$url',$url);
            Debug::debug('getApiComponentToken 的 远程$data',$data);
            Debug::debug('getApiComponentToken 的 远程',$res);
            if($res['component_access_token']){
                Cache::set($key,$res);
            }
            $apiComponentToken = $res;
        }
        return $apiComponentToken['component_access_token'];
    }

    /**
     * 启动ticket推送服务
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/component_verify_ticket_service.html
     * 参数	类型	必填	说明
        component_appid	string	是	平台型第三方平台的appid
        component_secret	string	是	平台型第三方平台的APPSECRET
     */
    public function apiStartPushTicket()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_start_push_ticket";
        $data['component_appid']    =   $this->appId;
        $data['component_secret']   =   $this->appSecret;
        $res = Query::posturl($url, $data);
        return $res;
    }

}
