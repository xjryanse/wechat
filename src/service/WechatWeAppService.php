<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\logic\Cachex;
use xjryanse\logic\Arrays;
/**
 * 微信小程序账户
 */
class WechatWeAppService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWeApp';
    /**
     * appid取公司key
     * @param type $appId
     * @return type
     */
    public static function appidGetComKey($appId){
        return Cachex::funcGet( $appId.'_WeAppComKey', function() use ($appId){
            $con[]  = ['appid','=',$appId];
            $con[]  = ['status','=',1];
            $info   = self::mainModel()->where($con)->find();
            $companyInfo = SystemCompanyService::mainModel()->where('id',$info['company_id'])->find();
            return Arrays::value($companyInfo, 'key');
        });
    }

}
