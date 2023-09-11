<?php

/**
 * Created by PhpStorm.
 * User: Jiawei
 * Date: 2017/7/29
 * Time: 19:06
 */
namespace xjryanse\wechat\WeApp;

use xjryanse\curl\Buffer;

class QRCode extends BaseApi {

    use \xjryanse\wechat\WeApp\traits\InstTrait;
    
    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html
     */
    public function getWxaCodeUnlimit($scene){
        $accessToken = Token::getInstance($this->uuid)->getAccessToken();
        
        $url            = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$accessToken;
        $data['scene']  = $scene;
        $data['page']   = 'pages/universal/index';
        $res = Buffer::post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return $res;
    }
    
    /********  20230619 ： 下方接口拟废弃  ********************************/
    
    public function getQRCodeA($path, $width = null, $auto_color = null, $line_color = null) {
        $url = ApiUrl::GET_APP_CODE_A;
        $param = array(
            'path' => $path,
            'width' => $width,
            'auto_color' => $auto_color,
            'line_color' => $line_color,
        );
        return $this->sendRequestWithToken($url, $param);
    }

    public function getQRCodeB($scene, $page, $width = null, $auto_color = null, $line_color = null) {
        $url = ApiUrl::GET_APP_CODE_B;
        $param = array(
            'scene' => $scene,
            'page' => $page,
            'width' => $width,
            'auto_color' => $auto_color,
            'line_color' => $line_color,
        );
        return $this->sendRequestWithToken($url, $param);
    }

    public function getQRCodeC($path, $width = null) {
        $url = ApiUrl::GET_QR_CODE_C;
        $param = array(
            'path' => $path,
            'width' => $width,
        );
        return $this->sendRequestWithToken($url, $param);
    }

}
