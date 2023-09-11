<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\wechat\service\WechatWeOpenService;
use xjryanse\wechat\service\WechatWeOpenAuthorizeService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
use think\facade\Cache;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/privacy_config/set_privacy_setting.html
 * 小程序用户隐私保护指引
 */
class Privacy extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;

    /**
     * 配置小程序用户隐私保护指引
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/privacy_config/set_privacy_setting.html
     */
    public function setPrivacySetting()
    {
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/setprivacysetting?access_token='.$authorizerAccessToken;
        $data = [];
        $data['owner_setting']['contact_email']    = 'xjryanse@sina.com';
        $data['owner_setting']['notice_method']    = '通过弹窗，公告，线下告知';
        $data['setting_list'][]   = [
            'privacy_key'=>"UserInfo",
            'privacy_text'=>"登录创建账号",
        ];
        $data['setting_list'][]   = [
            'privacy_key'=>"PhoneNumber",
            'privacy_text'=>"执班司机能通过手机号码及时联系您乘车",
        ];
        $data['setting_list'][]   = [
            'privacy_key'=>"Location",
            'privacy_text'=>"显示车辆位置",
        ];

        $res        = Query::posturl($url, $data);
        return $res;
    }
    /**
     * 查询小程序用户隐私保护指引
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/privacy_config/get_privacy_setting.html
     * @return type
     */
    public function getPrivacySetting()
    {
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/getprivacysetting?access_token='.$authorizerAccessToken;
        $data = [];
        $data['privacy_ver'] = 2;
        $res        = Query::posturl($url, $data);
        return $res;
    }
}
