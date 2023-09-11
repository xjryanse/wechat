<?php
namespace xjryanse\wechat;

use xjryanse\curl\Query;
/**
 * 微信开放平台
 * 转发开放平台接口，用于自有站点对接
 */
class WeOpenApi {
    protected static $baseUrl = "https://tenancy.xiesemi.cn/";
    
    /**
     * 获取小程序登录sessionKey
     * @param type $appid
     * @param type $code
     */
    public static function getSessionKey($appid, $code){
        $url = 'wechat/we_open_api/getSessionKey';
        $data['appid'] = $appid;
        $data['code'] = $code;

        $res = Query::post(self::$baseUrl.$url, $data);
        return $res['code'] == 0 ? $res['data'] : [];
    }
    
}
