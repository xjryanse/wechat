<?php

/**
 * Created by xjryanse.
 * User: xjryanse
 * Date: 2020/10/13
 * Time: 10:13
 */

namespace xjryanse\wechat;

use xjryanse\wechat\WeApp\CustomMsg;
use xjryanse\wechat\WeApp\QRCode;
// use xjryanse\wechat\WeApp\SessionKey;
use xjryanse\wechat\WeApp\Statistic;
use xjryanse\wechat\WeApp\TemplateMsg;
use xjryanse\wechat\WeApp\SubscribeMsg;
use xjryanse\wechat\service\WechatWeAppService;
use xjryanse\wechat\service\WechatWeOpenAuthorizeService;
use xjryanse\wechat\WeOpen\WeAppLogin;
use xjryanse\wechat\WeOpenApi;
use xjryanse\xcache\Cache;
use xjryanse\logic\Debug;
// 20230616:开始优化重写
use xjryanse\wechat\WeApp\AppUser;


class WeApp {

    private $appid;
    private $secret;
    private $instance;

    public function __construct($appid, $secret, $token_cache_dir) {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->instance = [];
        Cache::init($token_cache_dir);
    }

    /**
     * @param $code
     * @return array sessionkey相关数组
     */
    public function getSessionKey($code) {
        if(!$this->secret){
            //20220301 尝试提取开放平台的授权信息
            /*
            $weOpenId = WechatWeOpenAuthorizeService::authorizerAppidGetWeOpenId($this->appid);
            WeAppLogin::getInstance($weOpenId)->setAuthAppId( $this->appid );
            $res = WeAppLogin::getInstance( $weOpenId )->getSessionKey($code);
             */
            // 20221020 ;
            $res = WeOpenApi::getSessionKey($this->appid, $code);
        } else {
            //原逻辑，使用密钥获取登录小程序
            // if (!isset($this->instance['sessionkey'])) {
            //    $this->instance['sessionkey'] = new SessionKey($this->appid, $this->secret);
            // }
            // $res = $this->instance['sessionkey']->get($code);

            // 20230616：优化重写，并测试成功
            $weAppId = WechatWeAppService::appIdToId($this->appid);
            $res = AppUser::getInstance($weAppId)->sessionKey($code);
        }

        return $res;
    }

    /**
     * @return TemplateMsg 模板消息对象
     */
    public function getTemplateMsg() {
        if (!isset($this->instance['template'])) {
            $this->instance['template'] = new TemplateMsg($this->appid, $this->secret);
        }
        return $this->instance['template'];
    }

    /**
     * @return QRCode 二维码对象
     */
    public function getQRCode() {
        if (!isset($this->instance['qrcode'])) {
            $this->instance['qrcode'] = new QRCode($this->appid, $this->secret);
        }
        return $this->instance['qrcode'];
    }

    /**
     * @return Statistic 数据统计对象
     */
    public function getStatistic() {
        if (!isset($this->instance['statistic'])) {
            $this->instance['statistic'] = new Statistic($this->appid, $this->secret);
        }
        return $this->instance['statistic'];
    }

    /**
     * @return CustomMsg 客户消息对象
     */
    public function getCustomMsg() {
        if (!isset($this->instance['custommsg'])) {
            $this->instance['custommsg'] = new CustomMsg($this->appid, $this->secret);
        }
        return $this->instance['custommsg'];
    }

    /**
     * @return CustomMsg 客户消息对象
     */
    public function getSubscribeMsg() {
        if (!isset($this->instance['subscribeMsg'])) {
            $this->instance['subscribeMsg'] = new SubscribeMsg($this->appid, $this->secret);
        }
        return $this->instance['subscribeMsg'];
    }
    
}
