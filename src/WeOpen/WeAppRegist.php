<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\logic\Debug;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Register_Mini_Programs/Fast_Registration_Interface_document.html
 * 小程序注册
 */
class WeAppRegist extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;
    
    /**
     * 快速注册企业小程序（无需300认证费）
     * @param type $name    企业名称
     * @param type $code    统一社会信用代码
     * @param type $legalPersonalWechat 法人微信
     * @param type $legalPersonaName    法人姓名
     * @param type $phone       第三方平台手机
     * @return type
     */
    public function fastRegisterWeApp($name, $code,$legalPersonalWechat,$legalPersonaName,$phone){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/fastregisterweapp?action=create&component_access_token='.$componentAccessToken;
        // 企业名
        $data['name']                   = $name;
        // 企业代码
        $data['code']                   = $code;
        // 企业代码
        $data['code']                   = $code;
        // 企业代码类型（1：统一社会信用代码， 2：组织机构代码，3：营业执照注册号）
        $data['code_type']              = 1;
        // 法人微信
        $data['legal_persona_wechat']   = $legalPersonalWechat;
        // 法人姓名
        $data['legal_persona_name']     = $legalPersonaName;
        // 第三方联系电话
        $data['component_phone']        = $phone;
        
        Debug::debug('$url', $url);
        Debug::debug('$data', $data);
        $res = Query::posturl($url,$data);
        return $res;
    }
    
}
