<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;

/**
 * 微信小程序粉丝
 */
class WechatWeAppFansService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWeAppFans';

    /**
     * unionid转openid
     * @param type $unionid
     * @return type
     */
    public static function unionidToOpenid($unionid) {
        $con[] = ['unionid', '=', $unionid];
        return self::mainModel()->where($con)->value('openid');
    }

    /**
     * 通过openid取单条数据
     */
    public static function findByOpenid($openid) {
        $con[] = ['openid', '=', $openid];
        // $info = self::mainModel()->where($con)->cache(86400)->find();
        $info = self::find($con);
        return $info;
    }

    /**
     * 额外详情
     * @param type $ids
     * @return type
     */
    public static function extraDetails($ids) {
        //数组返回多个，非数组返回一个
        $isMulti = is_array($ids);
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $con[] = ['id', 'in', $ids];
        $lists = self::selectX($con);
        //属性key数组
        $cond[] = ['openid', 'in', array_column($lists, 'openid')];
        $userCountArr = WechatWeAppFansUserService::mainModel()->where($cond)->group('openid')->column('count(1)', 'openid');
        foreach ($lists as &$v) {
            //attrKey数
            $v['userCounts'] = Arrays::value($userCountArr, $v['openid'], 0);
        }

        return $isMulti ? $lists : $lists[0];
        // return $isMulti ? Arrays2d::fieldSetKey($lists, 'id') : $lists[0];
    }

    /**
     * 20220910:手机号码转openid
     */
    public static function phoneToOpenid($phone) {
        $con[] = ['phone', '=', $phone];
        $info = self::find($con);
        return $info ? $info['openid'] : '';
    }

    /**
     * 20220910:手机号码转unionid
     * @param type $phone
     * @return type
     */
    public static function phoneToUnionid($phone) {
        $con[] = ['phone', '=', $phone];
        $info = self::find($con);
        return $info ? $info['unionid'] : '';
    }

}
