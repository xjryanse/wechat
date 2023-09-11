<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;

/**
 * 模板消息
 */
class WechatWePubTemplateMsgService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubTemplateMsg';

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $cond[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
                    $logArr = WechatWePubTemplateMsgLogService::groupBatchCount('template_key', array_column($lists, 'template_key'), $cond);
                    $userArr = WechatWePubTemplateMsgUserService::groupBatchCount('template_key', array_column($lists, 'template_key'), $cond);
                    foreach ($lists as &$v) {
                        $v['logCount'] = Arrays::value($logArr, $v['template_key'], 0);
                        $v['userCount'] = Arrays::value($userArr, $v['template_key'], 0);
                    }
                    return $lists;
                });
    }

    /**
     * 根据模板key取信息
     * @param type $templateKey
     * @return type
     */
    public static function getByTemplateKey($templateKey) {
        $con[] = ['template_key', '=', $templateKey];
        return self::find($con);
    }

}
