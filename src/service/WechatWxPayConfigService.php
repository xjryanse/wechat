<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信支付配置
 */
class WechatWxPayConfigService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWxPayConfig';

    public static function getByAppId( $appId )
    {
        $con[] = ['AppId','=',$appId];
        return WechatWxPayConfigService::find( $con );
    }
}
