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
use xjryanse\wechat\WePub\wxurl\Card;
use xjryanse\system\logic\ConfigLogic;
use xjryanse\curl\Query;
use xjryanse\logic\Arrays;
use think\facade\Cache;
use xjryanse\logic\Debug;
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
    
    public function __construct($acid = 2,$openid='')
    {
        $this->acid     = $acid;
        $this->openid   = $openid;
        $app = WechatWePubService::getInstance($this->acid)->getCache();
        if(!$app){
            echo json_encode(['code' => '1',"msg"=>'公众号不存在[xjryanse\wechat\WePub]']); exit;
        }
        $this->appId        = $app['appid'];
        $this->appSecret    = $app['secret'];
        Debug::debug('$this->appId',$this->appId);
        Debug::debug('$this->appSecret',$this->appSecret);

        $this->wxUrl['CgiBin']      = new CgiBin( $this->appId, $this->appSecret,$this->accessToken );
        $this->wxUrl['Connect']     = new Connect( $this->appId, $this->appSecret,$this->accessToken );
        $this->wxUrl['Datacube']    = new Datacube( $this->appId, $this->appSecret,$this->accessToken );
        $this->wxUrl['Sns']         = new Sns( $this->appId, $this->appSecret,$this->accessToken );
        $this->wxUrl['Card']         = new Card( $this->appId, $this->appSecret,$this->accessToken );
        
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
        Debug::debug('$token',$token);
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
        Debug::debug('FansInfoOpenid',$this->openid);
        Debug::debug('DbFansInfo',$fansInfo);
        //20220204主动拉取接口不再返回昵称信息，增加昵称判断，如无昵称，由用户授权获取
        if(!$fansInfo || !$fansInfo['nickname']){
            //从微信服务器获取用户信息
            $userInfoUrl    = $this->wxUrl['Sns']->userInfo( $this->openid, $this->token );
            $res            = Query::geturl($userInfoUrl);
            //dump($res);exit;
            //方便调试
            $res['url']     = $userInfoUrl;
            $res['token']   = $this->token;
            $res['acid']    = $this->acid;
            //有openid，保存返回，没有，返回空
            if(!$fansInfo){
                //新增
                $fansInfo       = Arrays::value($res,'openid') ? WechatWePubFansService::save($res) : [];
            } else {
                WechatWePubFansService::getInstance($fansInfo['id'])->update($res);
                $fansInfo = array_merge($fansInfo,$res);
            }
        }
        return $fansInfo;
    }
    /**
     * 批量拉取用户信息
     */
    public function userGet( $nextOpenid = "")
    {
        $url = $this->wxUrl['CgiBin']->userGet( $nextOpenid);
        Debug::debug('Fans userGet() url',$url);
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
    public function cgiBinUserInfoBatchget( $data )
    {
        $userInfoUrl    = $this->wxUrl['CgiBin']->userInfoBatchget();
        $res            = Query::posturl($userInfoUrl,$data);
        Debug::debug('cgiBinUserInfoBatchget的$res',$res);
        if(!isset( $res['user_info_list'])){
            return $res;
        }
        //批量更新用户
        foreach( $res['user_info_list'] as &$v){
            //循环存入数据库
            //2021年12月27日之后，不再输出头像、昵称信息。
            $v = Arrays::unsetEmpty($v);
            // 20220911:增加同步时间戳
            $v['sync_timestamp'] = time();
            WechatWePubFansService::updateInfo( $v );
        }
        
        return $res;
    }
    /**
     * accessToken对象数组
     */
    public function getAccessTokenArr(){
        $wePubAccessTokenUrl = ConfigLogic::config('wePubAccessTokenUrl'); 
        Debug::debug('$wePubAccessTokenUrl',$wePubAccessTokenUrl);
        if($wePubAccessTokenUrl){
            $res            = Query::geturl( $wePubAccessTokenUrl);
            $accessToken    = $res['data'];
        } else {
            $cacheKey   = 'WUwechatAccessToken'.$this->acid;
            $accessToken = Cache::get( $cacheKey );
            Debug::debug('缓存中的$accessToken',$accessToken);
            // 无$accessToken，或10分钟后即将过期。
            if(!$accessToken || ! $accessToken['access_token'] || (strtotime($accessToken['expires_time']) - time() < 600)){
                //从微信服务器获取accessToken
                $accessTokenUrl = $this->wxUrl['CgiBin']->token();
                $res            = Query::geturl( $accessTokenUrl);
                Debug::debug('$accessTokenUrl',$accessTokenUrl);
                Debug::debug('$accessTokenUrl的$res',$res);
                if( isset($res['errmsg'])){
                    throw new Exception( $res['errmsg'],$res['errcode']);
                }
                $res['acid']    = $this->acid;
                $res['expires_time']    = date('Y-m-d H:i:s',time() + $res['expires_in']);
                // 2022-11-13:增加7000秒过期
                Cache::set( $cacheKey ,$res ,7000);
                $accessToken = $res;
            }
        }

        return $accessToken;
    }
    
    /**
     * 获取公众号accesstoken;
     * public 方法，用于对外部其他系统提供，确保accessToken唯一
     * @return type
     */
    public function getAccessToken()
    {
        // 20230308：逻辑拆分
        $accessToken = $this->getAccessTokenArr();

        $this->accessToken = $accessToken['access_token'];
        //全局设一下

        $this->wxUrl['CgiBin']  ->setAccessToken( $accessToken['access_token'] );
        $this->wxUrl['Connect'] ->setAccessToken( $accessToken['access_token'] );
        $this->wxUrl['Datacube']->setAccessToken( $accessToken['access_token'] );
        $this->wxUrl['Sns']     ->setAccessToken( $accessToken['access_token'] );
        $this->wxUrl['Card']     ->setAccessToken( $accessToken['access_token'] );

        return $this->accessToken;
    }
    /**
     * 获取公众号accesstoken
     * @return type
     */
    public function getJsapiTicket()
    {
        $key = 'JSAPI_TICKET_'.$this->acid;
        $jsapiTicket = Cache::get($key);
        // $jsapiTicket = $this->getJsapiTicketFromDb( $this->acid);
        if(!$jsapiTicket  || $jsapiTicket['expires_time'] < time()){
            //从微信服务器获取accessToken
            $jsapiTicketUrl = $this->wxUrl['CgiBin']->ticketGetticket( $this->accessToken );
            $res            = Query::geturl( $jsapiTicketUrl);
            $res['acid']    = $this->acid;
            $res['expires_time'] = time() + $res['expires_in'] - 60;
            
            Cache::set($key,$res);
            $jsapiTicket = $res;
            // $jsapiTicket    = WechatWePubJsapiTicketService::save($res);
        }
        return $jsapiTicket['ticket'];
    }
    /**
     * 获取微信分享jssdk参数
     */
    public function getWxJsSdkParams( $url )
    {
        $ticket = $this->getJsapiTicket();
        $timestamp  = time();
        $noncestr   = $this->getRandStr();
        $str = 'jsapi_ticket='. $ticket
                .'&noncestr='. $noncestr
                .'&timestamp='.$timestamp
                .'&url='.$url;
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
        return WechatWePubOauthAccessTokenService::tokenGet($openid, $acid);
//        return WechatWePubOauthAccessTokenService::mainModel()->where('acid',$acid)
//                ->where('openid',$openid)
//                ->order('id desc')
//                ->find();
    }
    
    private static function getUserInfoFromDb($openid,$acid)
    {
        return WechatWePubFansService::getFansInfoCache($openid);
    }
    /*
    private static function getAccessTokenFromDb( $acid )
    {
        return UwechatAccessTokenService::mainModel()->where('acid',$acid)->order('id desc')->find();
    }
     */
    private static function getJsapiTicketFromDb( $acid )
    {
        return WechatWePubJsapiTicketService::mainModel()->where('acid',$acid)->order('id desc')->cache(7000)->find();
    }


    /**
     * 批量查询卡券列表
     * @param type $data    
     * @return type
     */
    public function cardBatchget( $data  )
    {
        $userInfoUrl    = $this->wxUrl['Card']->batchget();
        Debug::debug('Fans cardBatchget() url',$userInfoUrl);
        $res            = Query::posturl($userInfoUrl,$data);
        Debug::debug('Fans cardBatchget() res',$res);        
        
        return $res;
    }
}
