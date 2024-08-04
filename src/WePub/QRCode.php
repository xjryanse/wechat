<?php

/**
 * 20240613：场景值二维码
 */

namespace xjryanse\wechat\WePub;

use xjryanse\curl\Buffer;
use xjryanse\curl\Query;
// 20240613:todo:粉丝
use xjryanse\wechat\WePub\Fans;

/**
 * https://developers.weixin.qq.com/doc/offiaccount/Account_Management/Generating_a_Parametric_QR_Code.html
 */
// class QRCode extends BaseApi {
class QRCode {

    use \xjryanse\wechat\WePub\traits\InstTrait;

    /*     * ** 创建二维码ticket ************* */

    /*     * *** 临时二维码申请 ***************** */

    /**
     * 创建二维码ticket
     * @param type $actionName  二维码类型
     * QR_SCENE为临时的整型参数值，
     * QR_STR_SCENE为临时的字符串参数值，
     * QR_LIMIT_SCENE为永久的整型参数值，
     * QR_LIMIT_STR_SCENE为永久的字符串参数值
     * @return type
     */
    public function qrcodeCreate($actionName, $sceneStr) {
        $acid = $this->uuid;
        $inst = new Fans($acid);
        $accessToken = $inst->getAccessToken();

        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accessToken;

        $data['action_name']    = $actionName;
        $scene                  = ['scene_str'=>$sceneStr];
        $data['action_info']['scene']          = $scene;

        $res                    = Query::posturl($url, $data);
        // 这个ticket,配合showQrcode得到二维码图片
        // ["ticket"] => string(96) "gQHE8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyZzFUNVZjcDhlUGoxMDAwME0wN0gAAgRumWpmAwQAAAAA"
        // 或者用这个url自行生成
        // ["url"] => string(45) "http://weixin.qq.com/q/02g1T5Vcp8ePj10000M07H"
        return $res;
    }
    /**
     * 20240613:上一步拿到的ticket
     * 调用此链接获得图片
     * @param type $ticket
     */
    public function showQrcode($ticket){
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        return $url;
    }

}
