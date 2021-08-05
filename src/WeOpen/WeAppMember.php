<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Mini_Program_AdminManagement/Admin.html
 * 小程序成员管理接口
 */
class WeAppMember extends Base
{
    //开放平台实例复用类
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;

    /**
     * 绑定微信用户为体验者
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Mini_Program_AdminManagement/Admin.html
     */
    public function bindTester( $wechatId ){
        $authorizerAccessToken      = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/bind_tester?access_token='.$authorizerAccessToken;
        $data['wechatid'] = $wechatId;
        $res        = Query::posturl($url,$data);
        return $res;
    }
    /**
     * 获取体验者列表
     */
    public function memberAuth(){
        $authorizerAccessToken      = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/memberauth?access_token='.$authorizerAccessToken;
        $data['action'] = 'get_experiencer';
        $res        = Query::posturl($url,$data);
        return $res;
    }
}
