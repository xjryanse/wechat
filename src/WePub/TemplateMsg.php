<?php
namespace xjryanse\wechat\WePub;

use xjryanse\curl\Query;
use xjryanse\logic\Arrays;
use xjryanse\logic\Strings;
use xjryanse\logic\Debug;
use xjryanse\wechat\service\WechatWePubTemplateMsgService;

class TemplateMsg
{
    use \xjryanse\traits\WePubAuthTrait;
    /**
     * 【批量】执行发送操作
     * @param type $acid        公众号id
     * @param type $messages    已经打包好的模板消息串
     */
    public static function doSend( $acid,$messages)
    {
        $TemplateMsg    = new self();
        //循环发送消息
        if(is_array($messages)){
            foreach( $messages as $message ){
                $TemplateMsg->send( $acid, $message );
            }
        }
        return $messages;
    }
    
    /**
     * 【单条】模板消息发送
     * @param type $acid    公众号acid
     * @param type $message 符合模板消息发送格式的数据
     * @return type
     */
    public function send( $acid ,$message )
    {
        $data       = is_string($message) ? json_decode(stripslashes($message),true) : $message ;

        //微信公众号账户信息初始化
        $this->initWePubAccount( $acid );
        $url    = $this->wxUrl['CgiBin']->messageTemplateSend();        
        $res = Query::posturl($url, $data);
        //保存数据[使用异步]
//        $log['message']     = is_string($message) ?  : json_encode( $message, JSON_UNESCAPED_UNICODE );
//        $log['send_resp']   = json_encode( $res ,JSON_UNESCAPED_UNICODE);
//        WechatWePubTemplateMsgLogService::save($log);
        return $res;
    }

    /**
     * 拼接模板消息
     */
    public static function matchAll( $key,$openids, $data, $replaceRule = [] )
    {
        $con[] = ['company_id', '=' , isset($data['company_id']) ? $data['company_id'] : session(SESSION_COMPANY_ID)];
        $con[] = ['template_key', '=' , $key ];
        $info  = WechatWePubTemplateMsgService::find( $con );
        if(!$info['template_id']){
            return false;
        }
        Debug::debug('$info',$info);
        //后台配置的目标表
        $targetUrl = Strings::dataReplace(Arrays::value($info, 'target_url'),$data);
        Debug::debug('$data',$data);
        Debug::debug('$targetUrl',$targetUrl);
        //外部替换规则优先
        $rule  = $replaceRule ? : $info['replace_rule'];
        $messages = [];
        foreach($openids as $openid){
            if(!$openid){
                continue;
            }

            $sendData       = self::matchOne( $info['template_id'], $openid, $targetUrl, $data, $rule  );
//            dump($sendData);
            $messages[]     = $sendData;
        }
        return $messages;
    }
    
    /**
     * 模板消息匹配【单条】
     * @param type $templateId
     * @param type $openid
     * @param type $url
     * @param type $data
     * @param type $replaceRule         替换规则
     * @return type
     */
    public static function matchOne( $templateId, $openid, $url, $data, $replaceRule  )
    {
        $sendData['touser']         = $openid;
        $sendData['template_id']    = $templateId;
        $sendData['url']            = $url;
        if(is_array($replaceRule)){
            foreach( $replaceRule as $k=>$v) {
                //字段存在，则取字段，否则，取原样
                $sendData['data'][ $k ]['value']  = isset($data[$v['value']]) ? $data[$v['value']] : $v['value'] ;
                $sendData['data'][ $k ]['color']  = $v['color'];
            }
        }
        return $sendData;
    }
}
