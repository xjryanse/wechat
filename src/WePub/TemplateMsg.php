<?php
namespace xjryanse\wechat\WePub;

use xjryanse\curl\Query;
use xjryanse\wechat\service\WechatWePubTemplateMsgLogService;

class TemplateMsg
{
    use \xjryanse\traits\WePubAuthTrait;
    
    /**
     * 模板消息发送
     * @param type $acid    公众号acid
     * @param type $message 符合模板消息发送格式的数据
     * @return type
     */
    public function send( $acid ,$message ,$log = [])
    {
        $data       = is_string($message) ? json_decode(stripslashes($message),true) : $message ;

        //微信公众号账户信息初始化
        $this->initWePubAccount( $acid );
        $url    = $this->wxUrl['CgiBin']->messageTemplateSend();        
        $res = Query::posturl($url, $data);
        //保存数据
        $log['message']     = is_string($message) ?  : json_encode( $message, JSON_UNESCAPED_UNICODE );
        $log['send_resp']   = json_encode( $res ,JSON_UNESCAPED_UNICODE);
        WechatWePubTemplateMsgLogService::save($log);
        return $res;
    }
}
