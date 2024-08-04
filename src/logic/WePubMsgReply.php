<?php
namespace xjryanse\wechat\logic;

use xjryanse\wechat\logic\XmlMessage;
use xjryanse\wechat\logic\XmlRespTemplate;
use xjryanse\wechat\service\WechatWeMsgLogService;
use xjryanse\wechat\service\WechatWeMsgRespRuleService;
use xjryanse\wechat\service\WechatWePubFansService;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\system\service\SystemCompanyAbilityService;
use xjryanse\wechat\service\WechatWePubQrSceneService;
use app\system\wxTemplateMsg\WWePub;
use think\facade\Cache;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use xjryanse\logic\DbOperate;
/**
 * 公众号消息回复
 */
class WePubMsgReply {
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
        // 20221101:模板消息发送结果通知不做记录
        if($dataArr['Event'] == 'TEMPLATESENDJOBFINISH'){
            return '';
        }
        if($dataArr['Event'] == 'subscribe' || $dataArr['Event'] == 'SCAN'){
            
            $sceneId = Arrays::value($dataArr, 'EventKey');
            $dataArr['label'] = WechatWePubQrSceneService::getInstance($sceneId)->fLabel();
        }
        
        // 20240612
        // $dataArr['reamrk'] = $message;
        WechatWeMsgLogService::saveRam($dataArr);
        DbOperate::dealGlobal();
        Cache::set('WePubMsgReply$dataArr',$dataArr);
        
        //自动回复规则是否命中
        $msgType        = $dataArr['MsgType'];
        $event          = $dataArr['Event'];
        $sessionFrom    = $dataArr['SessionFrom'];
        $fromUserName   = $dataArr['FromUserName'];
        $toUserName     = $dataArr['ToUserName'];
        // 【步骤1】调用特殊代码逻辑
        $funcName = 'event'. ucfirst(strtolower($event));
        // 调用相应的事件处理逻辑
        if(method_exists(__CLASS__,$funcName)){
            call_user_func([ __CLASS__ , $funcName],$dataArr);
        }
        //【步骤2】调用通用代码逻辑
        //$keyword = "";
        $response       = WechatWeMsgRespRuleService::getResponse($msgType, $event, $sessionFrom, $dataArr);
        Cache::set('WePubMsgReplyResponse',$response);
        $respStr = '';
        if( $response){
            // $content        = $xmlMessage->Content();
            // $content        = $response['resp_msg'];
            $content        = Strings::dataReplace( $response['resp_msg'], $dataArr );
            // 20221013公众号同步发送
            $respStr        =  XmlRespTemplate::diyStr( $toUserName , $fromUserName,$response['resp_msg_type'], $content);
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
    /**
     * 20221012:关注事件
     */
    public static function eventSubscribe($dataArr){
        $openid = Arrays::value($dataArr, 'FromUserName');
        $con[]  = ['openid','=',$openid];
        $info = WechatWePubFansService::where($con)->find();
        if($info){
            $data['subscribe']          = 1;
            $data['subscribe_time']     = time();
            $data['subscribe_time2']    = date('Y-m-d');
            WechatWePubFansService::getInstance($info['id'])->update($data);
        } else {
            $acid = SystemCompanyService::getInstance(session(SESSION_COMPANY_ID))->fWePubId();
            WechatWePubFansService::addOpenid( $openid, $acid );
        }
        //增加公司校验
        //TODO：判断无绑定的才发
        if(SystemCompanyAbilityService::hasAbilityByKey('wePubUserBindNotice')){
            // 20221013：发送引导绑定的模板消息通知
            WWePub::wePubUserBindNotice($openid);
        }
    }
    /**
     * 20221012:取消关注事件
     * @param type $dataArr
     */
    public static function eventUnsubscribe($dataArr){
        $openid = Arrays::value($dataArr, 'FromUserName');
        $con[]  = ['openid','=',$openid];
        $info = WechatWePubFansService::where($con)->find();
        if($info){
            $data['subscribe']          = 0;
            $data['subscribe_time']     = null;
            $data['subscribe_time2']    = null;
            WechatWePubFansService::getInstance($info['id'])->update($data);
        }
    }
}
