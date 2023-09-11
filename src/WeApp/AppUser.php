<?php

namespace xjryanse\wechat\WeApp;

use xjryanse\curl\Query;
use xjryanse\logic\Url;
use xjryanse\system\service\SystemLogService;
/**
 * 20230616：重写与用户相关的逻辑
 */
class AppUser extends Base {

    use \xjryanse\wechat\WeApp\traits\InstTrait;

    /**
     * 
     * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-login/code2Session.html
     * @param type $jsCode  登录时获取的 code，可通过wx.login获取
     * @return type
     */
    public function sessionKey($jsCode){
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $param['grant_type']    = 'authorization_code';
        $param['appid']         = $this->appId;
        $param['secret']        = $this->appSecret;
        $param['js_code']       = $jsCode;
        
        $finalUrl   = Url::addParam($url, $param);
        $response   = Query::get($finalUrl);
        SystemLogService::outLog($finalUrl, "", $param, $response);
        return $response;
    }

    
}
