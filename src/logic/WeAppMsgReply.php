<?php
namespace xjryanse\wechat\logic;

use xjryanse\wechat\logic\XmlMessage;
use xjryanse\wechat\logic\XmlRespTemplate;
use xjryanse\wechat\service\WechatWeMsgLogService;
use xjryanse\wechat\service\WechatWeMsgRespRuleService;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\logic\DbOperate;
use think\facade\Cache;

/**
 * 消息回复
 */
class WeAppMsgReply {
    /**
     * 消息回复
     * @param type $message
     * @return string
     */
    public static function index($acid, $message )
    {
        $xmlMessage = XmlMessage::getInstance();
        $xmlMessage->setMessage( $message );
        $dataArr = $xmlMessage->getMessage();
        WechatWeMsgLogService::saveRam($dataArr);
        DbOperate::dealGlobal();
        Cache::set('WeAppMsgReply$dataArr',$dataArr);
        
        //自动回复规则是否命中
        $msgType        = $dataArr['MsgType'];
        $event          = $dataArr['Event'];
        $sessionFrom    = $dataArr['SessionFrom'];
        $fromUserName   = $dataArr['FromUserName'];
        $toUserName     = $dataArr['ToUserName'];
        
        //$keyword = "";
        $response       = WechatWeMsgRespRuleService::getResponse($msgType, $event, $sessionFrom);
        Cache::set('WeAppMsgReplyResponse',$response);
        $respStr = '';
        if( $response){
            //$content        = $xmlMessage->Content();
            $content        = $response['resp_msg'];
            if($response['resp_msg_type'] == 'text'){
                //小程序的客服消息似乎只能异步发送
                self::custMsgSend($fromUserName, $content);
            } else {
                $respStr =  XmlRespTemplate::diyStr( $toUserName , $fromUserName,$response['resp_msg_type'], $content);
            }
        }
        
        if($respStr){
            //保存返回的消息
            $xmlMessage->setMessage( $respStr );
            WechatWeMsgLogService::saveRam($xmlMessage->getMessage());            
            DbOperate::dealGlobal();
        }

        return $respStr;
    }
    
    /**
     * 异步地发送客服消息
     */
    public static function custMsgSend($openid, $content){
        $app        = SystemCompanyService::getInstance( session(SESSION_COMPANY_ID) )->getWechatWeApp();;
        $custMsg    = $app->getCustomMsg();

        $data['content'] = $content;
        return $custMsg->send($openid, 'text', $data);
    }
}
