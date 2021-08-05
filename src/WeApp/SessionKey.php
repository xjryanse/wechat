<?php
namespace xjryanse\wechat\WeApp;

use xjryanse\system\service\SystemLogService;

class SessionKey extends BaseApi {
    public function get($code) {
        $url = ApiUrl::SESSION_KEY;
        $param = array(
            'appid' => $this->appid,
            'secret' => $this->secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        );
        $response = file_get_contents($url . '?' . http_build_query($param));
        //记录日志
        SystemLogService::outLog($url, "", $param, $response, $data = []);

        return $response;
    }
}
