<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\curl\Buffer;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Business/qrcode.generate.html
 * 小程序二维码
 */
class WeAppQRCode extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;
    
    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html
     */
    public function getWxaCodeUnlimit($scene){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$authorizerAccessToken;
        $data['scene'] = $scene;
        $data['page'] = 'pages/universal/index';
        $res = Buffer::post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return $res;
    }

}
