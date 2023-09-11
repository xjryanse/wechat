<?php

namespace xjryanse\wechat\WeApp;

use xjryanse\logic\Cachex;
use xjryanse\curl\Query;
use xjryanse\logic\Url;

/**
 * 20230616：重写优化逻辑
 */
class Token extends Base {

    use \xjryanse\wechat\WeApp\traits\InstTrait;

    /**
     * 20230616：重写
     * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/mp-access-token/getAccessToken.html
     * @return string
     */
    public function getAccessToken() {
        $key = __METHOD__ . $this->uuid;
        $res = Cachex::funcGet($key, function() {
                    $url = 'https://api.weixin.qq.com/cgi-bin/token';
                    $param['grant_type'] = 'client_credential';
                    $param['appid'] = $this->appId;
                    $param['secret'] = $this->appSecret;
                    $finalUrl = Url::addParam($url, $param);
                    return Query::get($finalUrl);
                }, false, 7000);
        /*
         * res数据格式如下：
          array(2) {
          ["access_token"] => string(136) "69_2WeoVlwJZivWcYOORGbTR74lBwVHoV4XV_CDSavB7eRd1aRrqrZP5CkGLX8q73X-jiei6pKWIhKmsxTZ4txJWAFEiYHgtfPSSjZtwDGOTwU38cmjJHAw7Z6-q4sMLDiAEAGFX"
          ["expires_in"] => int(7200)
          }
         */

        return $res ? $res['access_token'] : '';
    }

}
