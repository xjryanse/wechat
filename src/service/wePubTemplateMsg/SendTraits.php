<?php
namespace xjryanse\wechat\service\wePubTemplateMsg;

use xjryanse\logic\Arrays;
use app\system\wxTemplateMsg\CommonSend;
use xjryanse\wechat\service\WechatWePubFansUserService;
/**
 * 发送逻辑
 */
trait SendTraits{
    
    /**
     * 传入数据和用户，匹配需要发送的消息
     * @param type $templateKey
     * @param type $info
     * @param type $userIds
     * @return bool
     */
    public static function doSendByDataAndUser($templateKey, $info, $userIds){
        // 20240127:
        $companyId = Arrays::value($info, 'company_id');
        if($companyId){
            session(SESSION_COMPANY_ID, $companyId);
        }
        // 20240127:匹配发送消息
        $cond       = [];
        $cond[]     = ['user_id','in',$userIds];
        $openids    = WechatWePubFansUserService::column('openid', $cond);

        if(!$openids){
            return false;
        }

        return CommonSend::newNoticeOnce($templateKey, $info, $openids);
    }
}
