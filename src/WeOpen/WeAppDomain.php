<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\curl\Buffer;
use xjryanse\logic\Debug;
use xjryanse\system\logic\FileLogic;

/**
 * https://developers.weixin.qq.com/doc/oplatform/openApi/OpenApiDoc/miniprogram-management/domain-management/modifyServerDomain.html
 * 小程序域名管理
 */
class WeAppDomain extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;
    /**
     * 业务域名校验文件
     * @return type
     */
    public function getWebviewDomainConfirmfile(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/get_webviewdomain_confirmfile?access_token='.$authorizerAccessToken;
        $res = Query::post($url,'{}');
        return $res;
    }
    /**
     * 获取服务器域名列表
     */
    public function wxaModifyDomainGet(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/modify_domain?access_token='.$authorizerAccessToken;
        $data['action'] = 'get';
        $res = Query::posturl($url,$data);
        return $res;
    }
    
    public function wxaModifyDomainDelete($data){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/modify_domain?access_token='.$authorizerAccessToken;
        $data['action'] = 'delete';
        $res = Query::posturl($url,$data);
        return $res;
    }
    /**
     * 设置服务器域名列表
     * @param array $data
     * @return type
     */
    public function wxaModifyDomainSet ($data = []) {
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/modify_domain?access_token='.$authorizerAccessToken;
        $data['action'] = 'set';
        $res = Query::posturl($url,$data);
        return $res;
    }
    public function wxaModifyDomainAdd ($data = []) {
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/modify_domain?access_token='.$authorizerAccessToken;
        $data['action'] = 'add';
        $res = Query::posturl($url,$data);
        return $res;
    }
    /*
     * 获取授权小程序的业务域名
     */
    public function wxaWebviewDomainGet(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain?access_token='.$authorizerAccessToken;
        $data['action'] = 'get';
        $res = Query::posturl($url,$data);
        return $res;
    }
    /**
     * 设置授权小程序的业务域名
     * @param array $data
     * @return type
     */
    public function wxaWebviewDomainSet ($data = []) {
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain?access_token='.$authorizerAccessToken;
        $data['action'] = 'set';
        $res = Query::posturl($url,$data);
        return $res;
    }
    
    public function wxaWebviewDomainAdd ($data = []) {
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain?access_token='.$authorizerAccessToken;
        $data['action'] = 'add';
        $res = Query::posturl($url,$data);
        return $res;
    }
    
    public function wxaWebviewDomainDelete ($data = []) {
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain?access_token='.$authorizerAccessToken;
        $data['action'] = 'delete';
        $res = Query::posturl($url,$data);
        return $res;
    }
    /*
     * 快速……
     */
    public function wxaWebviewDomainDirectlyGet(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain_directly?access_token='.$authorizerAccessToken;
        $data['action'] = 'get';
        $res = Query::posturl($url,$data);
        return $res;
    }
    
    public function wxaWebviewDomainDirectlyDelete($data=[]){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain_directly?access_token='.$authorizerAccessToken;
        $data['action'] = 'delete';
        $res = Query::posturl($url,$data);
        return $res;
    }
    /**
     * 快速设置业务域名
     * @return type
     */
    public function wxaWebviewDomainDirectlySet($data=[]){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain_directly?access_token='.$authorizerAccessToken;
        $data['action'] = 'set';
        $res = Query::posturl($url,$data);
        return $res;
    }
    
    public function wxaWebviewDomainDirectlyAdd($data=[]){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/setwebviewdomain_directly?access_token='.$authorizerAccessToken;
        $data['action'] = 'add';
        $res = Query::posturl($url,$data);
        return $res;
    }
    
    /**
     * 获取发布后生效服务器域名列表
     * @return type
     */
    public function getEffectiveDomain(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/get_effective_domain?access_token='.$authorizerAccessToken;
        $res = Query::post($url,'{}');
        return $res;
    }
    /**
     * 获取发布后生效业务域名列表
     * @return type
     */
    public function getEffectiveWebviewDomain(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/get_effective_webviewdomain?access_token='.$authorizerAccessToken;
        $res = Query::post($url,'{}');
        return $res;
    }
}
