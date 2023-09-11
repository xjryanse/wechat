<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\curl\Buffer;
use xjryanse\logic\Debug;
use xjryanse\system\logic\FileLogic;

/**
 * https://developers.weixin.qq.com/doc/oplatform/openApi/OpenApiDoc/thirdparty-management/domain-mgnt/modifyThirdpartyServerDomain.html
 * 第三方平台域名管理
 */
class WeOpenDomain extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    /**
     * 获取第三方平台域名校验文件
     */
    public function getDomainConfirmfile() {
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/cgi-bin/component/get_domain_confirmfile?access_token='.$componentAccessToken;
        $res        = Query::posturl($url);
        return $res;
    }
    /**
     * 获取第三方平台业务域名
     */
    public function modifyWxaJumpDomainGet(){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/cgi-bin/component/modify_wxa_jump_domain?access_token='.$componentAccessToken;
        $data['action'] = 'get';
        $res        = Query::posturl($url,$data);
        return $res;
    }
    /**
     * 设置第三方平台业务域名
     * @return type
     */
    public function modifyWxaJumpDomainSet($data){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/cgi-bin/component/modify_wxa_jump_domain?access_token='.$componentAccessToken;
        $data['action'] = 'set';
        $res        = Query::posturl($url,$data);
        return $res;
    }
    /**
     * 设置第三方平台服务器域名
     */
    public function modifyWxaServerDomainSet($data){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/cgi-bin/component/modify_wxa_server_domain?access_token='.$componentAccessToken;
        $data['action'] = 'set';
        $res        = Query::posturl($url,$data);
        return $res;
    }
    /**
     * 获取第三方平台服务器域名
     * @return type
     */
    public function modifyWxaServerDomainGet(){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/cgi-bin/component/modify_wxa_server_domain?access_token='.$componentAccessToken;
        $data['action'] = 'get';
        $res        = Query::posturl($url,$data);
        return $res;
    }

    
}
