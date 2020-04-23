<?php

/**
 * Created by PhpStorm.
 * User: Jiawei
 * Date: 2017/7/29
 * Time: 10:04
 */

namespace xjryanse\wechat;

use xjryanse\wechat\WeApp\CustomMsg;
use xjryanse\wechat\WeApp\QRCode;
use xjryanse\wechat\WeApp\SessionKey;
use xjryanse\wechat\WeApp\Statistic;
use xjryanse\wechat\WeApp\TemplateMsg;
use xjryanse\wechat\WeApp\SubscribeMsg;
use xjryanse\xcache\Cache;

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
        if (!isset($this->instance['sessionkey'])) {
            $this->instance['sessionkey'] = new SessionKey($this->appid, $this->secret);
        }
        return $this->instance['sessionkey']->get($code);
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
