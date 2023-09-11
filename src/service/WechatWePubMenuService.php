<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信公众号菜单
 */
class WechatWePubMenuService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\TreeTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubMenu';

    /**
     * 根据acid获取公众号菜单
     */
    public static function getMenuByAcid($acid) {
        $con[] = ['acid', '=', $acid];
        $con[] = ['status', '=', 1];
        $lists = self::mainModel()->where($con)->cache(2)->order('sort')->select();
        $tree = self::makeTree($lists ? $lists->toArray() : [], 0, 'pid', 'sub_button');
        return $tree;
    }

}
