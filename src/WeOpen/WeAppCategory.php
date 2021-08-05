<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\curl\Buffer;
use xjryanse\logic\Debug;
use xjryanse\system\logic\FileLogic;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Mini_Program_AdminManagement/Admin.html
 * 小程序类目管理
 */
class WeAppCategory extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    //第三方授权实例复用类
    use \xjryanse\wechat\WeOpen\traits\AuthAppidInstTrait;
    
    /**
     * 获取审核时可填写的类目信息
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/category/get_category.html
     */
    public function wxaGetCategory(){
        $authorizerAccessToken     = $this->getApiAuthorizerToken();
        $url = 'https://api.weixin.qq.com/wxa/get_category?access_token='.$authorizerAccessToken;
        $res = Query::geturl($url);
        return $res;
    }

}
