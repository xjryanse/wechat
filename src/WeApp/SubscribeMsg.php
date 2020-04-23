<?php

namespace xjryanse\wechat\WeApp;

/**
 * 订阅消息
 */
class SubscribeMsg extends BaseApi {

    /**
     * 
     * @param type $touser
     * @param type $template_id
     * @param type $data
     * @param type $page
     * @param type $color
     * @param type $emphasis_keyword
     * @param type $miniprogram_state   跳转小程序类型：developer为开发版；trial为体验版；formal为正式版；默认为正式版
     * @return type
     */
    public function send($touser, $template_id, $data, $page = null, $miniprogram_state='formal',$lang = "zh_CN") {
        $url = ApiUrl::MESSAGE_SUBSCRIBE_SEND;
        $param = array(
            'touser'        => $touser,
            'template_id'   => $template_id,
            'page'          => $page,
            "lang"          =>$lang,
            'miniprogram_state' => $miniprogram_state,
            'data'          => $data,
        );
        return $this->sendRequestWithToken($url, $param);
    }

}
