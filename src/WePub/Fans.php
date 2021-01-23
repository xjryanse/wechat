<?php
namespace xjryanse\wechat\WePub;

use xjryanse\wechat\service\WechatWePubService;
use xjryanse\wechat\service\WechatWePubFansService;
use xjryanse\wechat\service\WechatWePubJsapiTicketService;
use xjryanse\wechat\service\WechatWePubOauthAccessTokenService;
use xjryanse\wechat\WePub\wxurl\CgiBin;
use xjryanse\wechat\WePub\wxurl\Connect;
use xjryanse\wechat\WePub\wxurl\Datacube;
use xjryanse\wechat\WePub\wxurl\Sns;
use xjryanse\curl\Query;
use think\facade\Request;
use think\facade\Cache;
use Exception;

class Fans
{
    protected $acid;
    protected $appId;
    public    $token;       //用户授权的accesstoken
    public    $accessToken; //公众号的accessToken
    protected $appSecret;
    protected $openid;
    public    $wxUrl;
    
    public function __construct(int $acid = 2,string $openid='')
    {
        $this->acid     = $acid;
        $this->openid   = $openid;
        $app = WechatWePubService::getInstance($this->acid)->get();
        if(!$app){
            echo json_encode(['code' => '1',"msg"=>'公众号不存在[xjryanse\wechat\WePub]']); exit;
        }
        $this->appId        = $app->appid;
        $this->appSecret    = $app->secret;
        
        $this->wxUrl['CgiBin']      = new CgiBin( $this->appId, $this->appSecret,$this->accessToken );
        $this->wxUrl['Connect']     = new Connect( $this->appId, $this->appSecret,$this->accessToken );
        $this->wxUrl['Datacube']    = new Datacube( $this->appId, $this->appSecret,$this->accessToken );
        $this->wxUrl['Sns']         = new Sns( $this->appId, $this->appSecret,$this->accessToken );
        
        $this->getOauthAccessToken();
        $this->getAccessToken();
    }

    /**
     * 获取用户授权AccessToken
     * @param type $openid  openid
     * @param type $acid    公众号id
     * @param type $url     回调
     */
    private function getOauthAccessToken()
    {
        //从本地服务器数据库获取用户授权AccessToken
        $token = $this->getOauthAccessTokenFromDb( $this->openid, $this->acid );

        if(!$token || strtotime($token['expires_time']) < time()){
            //没有记录或者accesstoken过期
            return false;
        }
        $this->token    = $token['access_token'];
        return $this->token;
    }
    /**
     * 获取公众号accesstoken
     * @return type
     */
    public function getUserInfo()
    {
        $fansInfo = $this->getUserInfoFromDb( $this->openid, $this->acid);
        if(!$fansInfo){
            //从微信服务器获取用户信息
            $userInfoUrl    = $this->wxUrl['Sns']->userInfo( $this->openid, $this->token );
            $res            = Query::geturl($userInfoUrl);
            //方便调试
            $res['url']     = $userInfoUrl;
            $res['token']   = $this->token;
            $res['acid']    = $this->acid;
            $fansInfo       = WechatWePubFansService::save($res);
        }
        return $fansInfo;
    }
    /**
     * 批量拉取用户信息
     */
    public function userGet( $nextOpenid = "")
    {
        $url = $this->wxUrl['CgiBin']->userGet( $nextOpenid = "" );
        dump($url);
        $data = Query::geturl( $url );
        //一般是出错的情况
        if(!isset($data['data']['openid'])){
            return $data;
        }
        //正常情况
        foreach( $data['data']['openid'] as $v){
            WechatWePubFansService::addOpenid( $v,$this->acid );
        }
        return $data;
    }
    
    /**
     * TODO修改开发者获取用户信息
     */
    public function cgiBinUserInfo( $userOpenid )
    {
        $userInfoUrl    = $this->wxUrl['CgiBin']->userInfo( $userOpenid, $this->getOauthAccessToken() );
        $res            = Query::geturl($userInfoUrl);
        return $res;
    }
    /**
     * 开发者批量拉取用户信息
     * @param type $data    微信规定的数据格式  https://developers.weixin.qq.com/doc/offiaccount/User_Management/Get_users_basic_information_UnionID.html#UinonId
     * @return type
     */
    public function cgiBinUserInfoBatchget( $data  )
    {
        $userInfoUrl    = $this->wxUrl['CgiBin']->userInfoBatchget();
        $res            = Query::posturl($userInfoUrl,$data);
        if(isset( $res['user_info_list'])){
            foreach( $res['user_info_list'] as &$v){
                //循环存入数据库
                WechatWePubFansService::updateInfo( $v );
            }
        }
        
        return $res;
    }
    
    /**
     * 获取公众号accesstoken
     * @return type
     */
    private function getAccessToken()
    {
//        $accessToken = $this->getAccessTokenFromDb( $this->acid);
        $cacheKey   = 'WUwechatAccessToken'.$this->acid;
        $accessToken = Cache::get( $cacheKey );
        if(!$accessToken || ! $accessToken['access_token'] || strtotime($accessToken['expires_time']) < time()){
            //从微信服务器获取accessToken
            $accessTokenUrl = $this->wxUrl['CgiBin']->token();
            $res            = Query::geturl( $accessTokenUrl);
            if( isset($res['errmsg'])){
                throw new Exception( $res['errmsg'],$res['errcode']);
            }
            $res['acid']    = $this->acid;
            $res['expires_time']    = date('Y-m-d H:i:s',time() + $res['expires_in']);

            Cache::set( $cacheKey ,$res );
            $accessToken = $res;
        }
        
        $this->accessToken = $accessToken['access_token'];
        //全局设一下
        $this->wxUrl['CgiBin']  ->setAccessToken( $accessToken['access_token'] );
        $this->wxUrl['Connect'] ->setAccessToken( $accessToken['access_token'] );
        $this->wxUrl['Datacube']->setAccessToken( $accessToken['access_token'] );
        $this->wxUrl['Sns']     ->setAccessToken( $accessToken['access_token'] );
    }
    /**
     * 获取公众号accesstoken
     * @return type
     */
    public function getJsapiTicket()
    {
        $jsapiTicket = $this->getJsapiTicketFromDb( $this->acid);
        if(!$jsapiTicket  || strtotime($jsapiTicket['expires_time']) < time()){
            //从微信服务器获取accessToken
            $jsapiTicketUrl = $this->wxUrl['CgiBin']->ticketGetticket( $this->accessToken );
            $res            = Query::geturl( $jsapiTicketUrl);
            $res['acid']    = $this->acid;
            $jsapiTicket    = WechatWePubJsapiTicketService::save($res);
        }
        return $jsapiTicket['ticket'];
    }
    /**
     * 获取微信分享jssdk参数
     */
    public function getWxJsSdkParams()
    {
        $ticket = $this->getJsapiTicket();
        $timestamp  = time();
        $noncestr   = $this->getRandStr();
        $str = 'jsapi_ticket='. $ticket
                .'&noncestr='. $noncestr
                .'&timestamp='.$timestamp
                .'&url='.Request::url(true);
        $signature  = sha1($str);
        
        $data['wxAppId']        = $this->appId;
        $data['wxTimestamp']    = &$timestamp;
        $data['wxNonceStr']     = &$noncestr;
        $data['wxSignature']    = &$signature;
        
        return $data;
    }
    
    private function getRandStr($len = 20){
        $str = "1234567890asdfghjklqwertyuiopzxcvbnmASDFGHJKLZXCVBNMPOIUYTREWQ";
        return substr(str_shuffle($str),0,$len);
    }    
    /**
     * 从本地服务器数据库获取用户授权AccessToken
     * @param type $openid
     * @param type $acid
     */
    private static function getOauthAccessTokenFromDb($openid,$acid)
    {
        return WechatWePubOauthAccessTokenService::mainModel()->where('acid',$acid)
                ->where('openid',$openid)
                ->order('id desc')
                ->find();
    }
    
    private static function getUserInfoFromDb($openid,$acid)
    {
        return WechatWePubFansService::mainModel()->where('acid',$acid)
                ->where('openid',$openid)
                ->order('id desc')                
                ->find();
    }
    /*
    private static function getAccessTokenFromDb( $acid )
    {
        return UwechatAccessTokenService::mainModel()->where('acid',$acid)->order('id desc')->find();
    }
     */
    private static function getJsapiTicketFromDb( $acid )
    {
        return WechatWePubJsapiTicketService::mainModel()->where('acid',$acid)->order('id desc')->find();
    }
}
