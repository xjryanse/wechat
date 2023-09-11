<?php

/**
 * Created by PhpStorm.
 * User: Jiawei
 * Date: 2017/7/29
 * Time: 21:18
 */

namespace xjryanse\wechat\WeApp;
use think\facade\Cache;

class CustomMsg extends BaseApi {

    public function send($touser, $msgtype, $content_array) {
        $url = ApiUrl::CUSTOM_MSG_SEND;
        $param = array(
            'touser' => $touser,
            'msgtype' => $msgtype,
            $msgtype => $content_array,
        );
        Cache::set('CustomMsgSend$msgtype',$msgtype);
        Cache::set('CustomMsgSend$content_array',$content_array);
        Cache::set('CustomMsgSendUrl',$url);
        Cache::set('CustomMsgSendParam',$param);
        return $this->sendRequestWithToken($url, $param);
    }

}
