<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\wechat\service\WechatWeAppService;
use xjryanse\wechat\service\WechatWePubService;
use xjryanse\logic\Arrays;
use xjryanse\logic\Debug;
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
        //查公众号表
        $con[] = ['appid','=',$appId];
        $wePubInfo = WechatWePubService::find($con);        
        Debug::debug('查公众号',$wePubInfo);
        //查小程序表
        $weAppInfo = WechatWeAppService::find($con);        
        Debug::debug('查小程序',$weAppInfo);
        $wxPayId = Arrays::value($wePubInfo, 'wx_pay_id') ? : (Arrays::value($weAppInfo, 'wx_pay_id') ? : '');    //先公众号，再小程序
        $info = self::getInstance($wxPayId)->get();
        if($info){
            $info['AppId'] = $appId;    //公众账号appid替换
        }
        return $info;
    }
}
