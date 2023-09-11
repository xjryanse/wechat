<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\logic\Url;
use xjryanse\curl\Query;
/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/others/WeChat_login.html
 * 小程序登录
 */
class WeAppLogin extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;
    /**
     * 登录接口
     * @param type $code
     * @return type
     */
    public function getSessionKey( $code ){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();
        //$authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/sns/component/jscode2session';
        $data['appid']      = $this->authAppId;
        $data['js_code']    = $code;
        $data['grant_type'] = 'authorization_code';
        $data['component_appid']            = $this->appId;
        $data['component_access_token']     = $componentAccessToken;

        $urlNew = Url::addParam($url, $data);

        $res        = Query::geturl($urlNew);
        //dump($res);
        return $res;
    }

}
