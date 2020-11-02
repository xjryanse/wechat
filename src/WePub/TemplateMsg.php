<?php
namespace xjryanse\wechat\WePub;

use xjryanse\curl\Query;
use xjryanse\wechat\service\WechatWePubTemplateMsgLogService;
use xjryanse\wechat\service\WechatWePubTemplateMsgService;

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

    /**
     * 拼接模板消息
     */
    public static function matchAll( $key,$openids, $url, $data, $replaceRule = [] )
    {
        $con[] = ['company_id', '=' , session(SESSION_COMPANY_ID)];
        $con[] = ['template_key', '=' , $key ];
        $info  = WechatWePubTemplateMsgService::find( $con );
        //外部替换规则优先
        $rule  = $replaceRule ? : json_decode( $info['match'],true);
        $messages = [];
        foreach($openids as $openid){
            if(!$openid){
                continue;
            }

            $sendData       = self::matchOne( $info['template_id'], $openid, $url, $data, $rule  );
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
//        dump($data);
        $sendData['touser']         = $openid;
        $sendData['template_id']    = $templateId;
        $sendData['url']            = $url;
        foreach( $replaceRule as $k=>$v) {
            //商品描述
            $sendData['data'][ $k ]['value']  = $data[$v['value']];
            $sendData['data'][ $k ]['color']  = $v['color'];
        }

        return $sendData;
    }
}
