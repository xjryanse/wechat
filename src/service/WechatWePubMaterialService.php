<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信公众号素材
 */
class WechatWePubMaterialService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubMaterial';

    /*
     * 素材是否存在
     */

    public static function isMaterialExists($mediaId) {
        $con[] = ['media_id', '=', $mediaId];
        return self::mainModel()->where($con)->value('media_id');
    }

}
