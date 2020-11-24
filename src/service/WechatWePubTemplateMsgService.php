<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 模板消息
 */
class WechatWePubTemplateMsgService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWePubTemplateMsg';
    /**
     * 根据模板key取信息
     * @param type $templateKey
     * @return type
     */
    public static function getByTemplateKey( $templateKey )
    {
        $con[] = ['template_key','=',$templateKey];
        return self::find( $con );
    }
}
