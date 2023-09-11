<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\curl\Buffer;
use xjryanse\logic\Debug;
use xjryanse\system\logic\FileLogic;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Mini_Program_AdminManagement/Admin.html
 * 小程序代码管理
 */
class WeAppCode extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;
    
    /**
     * 上传代码
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/commit.html
     */
    public function commit( $templateId,$extJson,$userVersion,$userDesc){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/commit?access_token='.$authorizerAccessToken;
        $data['template_id']    = $templateId;
        $data['ext_json']       = $extJson;
        $data['user_version']   = $userVersion;
        $data['user_desc']      = $userDesc;
        Debug::debug('接口url',$url);
        Debug::debug('接口url的data',$data);
        $res = Query::posturl($url, $data);
        
        return $res;
    }
    
    /**
     * 获取已上传的代码的页面列表
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/get_page.html
     */
    public function getPage(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/get_page?access_token='.$authorizerAccessToken;
        $res = Query::geturl($url);
        return $res;
    }
    /**
     * 获取体验版二维码
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/get_qrcode.html
     */
    public function getQrcode( $path='' ){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url    ='https://api.weixin.qq.com/wxa/get_qrcode?access_token='.$authorizerAccessToken.'&path='.$path;
        //，因为有ip限制，故需保存图片
        $resp = FileLogic::saveUrlFile($url);
        return $resp;
    }
    /*
     * 提交审核
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/submit_audit.html
     */
    public function submitAudit(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/submit_audit?access_token='.$authorizerAccessToken;
        Debug::debug('submitAudit的$url',$url);
        // 2023-02-13 审核项列表
        $data['item_list'] = [];
        // 代码中含有ext.json未配置隐私接口getLocation(暂无权限)，请配置并申请权限或者承诺不使用这些接口（设置参数privacy_api_not_use为true）后再提交审核
        $data['privacy_api_not_use'] = true;
        $res = Query::posturl($url,$data);
        Debug::debug('submitAudit的$res',$res);
        return $res;
    }
    /**
     * 查询最新一次提交的审核状态
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/get_latest_auditstatus.html
     * @return type
     */
    public function getLatestAutidstatus(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token='.$authorizerAccessToken;
        $res = Query::geturl($url);
        return $res;
    }
    /*
     * 撤回审核
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/undocodeaudit.html
     */
    public function undoCodeAudit(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/undocodeaudit?access_token='.$authorizerAccessToken;
        $res = Query::geturl($url);
        return $res;
    }
    /**
     * 发布已通过审核的小程序
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/release.html
     * @return type
     */
    public function release(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/release?access_token='.$authorizerAccessToken;
        //注：post的data为空，不等于不需要传data，否则会报错【errcode: 44002 "errmsg": "empty post data"】
        $res = Query::post($url,'{}');
        return $res;
    }
    /**
     * 查询服务商的当月提审限额（quota）和加急次数
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/query_quota.html
     * @return type
     */
    public function queryQuota(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/queryquota?access_token='.$authorizerAccessToken;
        //注：post的data为空，不等于不需要传data，否则会报错【errcode: 44002 "errmsg": "empty post data"】
        $res = Query::geturl($url);
        return $res;
    }
    /**
     * 加急审核
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/code/speedup_audit.html
     */
    public function speedUpAudit($auditId){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/speedupaudit?access_token='.$authorizerAccessToken;
        //注：post的data为空，不等于不需要传data，否则会报错【errcode: 44002 "errmsg": "empty post data"】
        $data['auditid'] = $auditId;
        $res = Query::posturl($url,$data);
        return $res;
    }
}
