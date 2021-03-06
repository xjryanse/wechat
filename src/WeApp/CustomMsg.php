<?php
/**
 * Created by PhpStorm.
 * User: Jiawei
 * Date: 2017/7/29
 * Time: 21:18
 */

namespace xjryanse\wechat\WeApp;


class CustomMsg extends BaseApi
{
	public function send($touser,$msgtype,$content_array){
		$url = ApiUrl::CUSTOM_MSG_SEND;
		$param = array(
			'touser'=>$touser,
			'msgtype'=>$msgtype,
			$msgtype=>$content_array,
		);
		return $this->sendRequestWithToken($url,$param);
	}

}