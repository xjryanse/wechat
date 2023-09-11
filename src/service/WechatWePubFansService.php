<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Cachex;

/**
 * 微信公众号粉丝
 */
class WechatWePubFansService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubFans';
    //直接执行后续触发动作
    protected static $directAfter = true;

    public function extraPreDelete() {
        self::checkTransaction();
        $userInfo = self::getInstance($this->uuid)->get();
        $con[] = ['openid', '=', Arrays::value($userInfo, 'openid')];
        $lists = WechatWePubFansUserService::lists($con);
        foreach ($lists as $fansUser) {
            WechatWePubFansUserService::getInstance($fansUser['id'])->delete();
        }
    }

    public static function extraAfterSave(&$data, $uuid) {
        //20220921
        $openid = Arrays::value($data, 'openid');
        self::setFansInfoCache($openid);
    }

    public static function extraAfterUpdate(&$data, $uuid) {
        $info = self::getInstance($uuid)->get();
        $openid = Arrays::value($info, 'openid');
        //20220617
        self::setFansInfoCache($openid);
    }

    /**
     * 额外详情
     * @param type $ids
     * @return type
     */
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $openids = array_column($lists, 'openid');
                    $cond[] = ['openid', 'in', array_column($lists, 'openid')];

                    $userArrObj = WechatWePubFansUserService::mainModel()->where($cond)->select();
                    $userArr = $userArrObj ? $userArrObj->toArray() : [];

                    $msgCountsArr = WechatWePubTemplateMsgLogService::groupBatchCount('openid', $openids);

                    foreach ($lists as &$v) {
                        //attrKey数
                        $cone = [];
                        $cone[] = ['openid', 'in', $v['openid']];
                        $v['userCounts'] = count(Arrays2d::listFilter($userArr, $cone));
                        //模板消息数
                        $v['tplMsgCounts'] = Arrays::value($msgCountsArr, $v['openid'], 0);
                        // 用于关联显示
                        $v['userIds'] = implode(',', array_column(Arrays2d::listFilter($userArr, $cone), 'user_id'));
                    }
                    return $lists;
                });
    }

    public static function addOpenid($openid, $acid) {
        if (!$openid) {
            return false;
        }
        if (self::isOpenidExists($openid)) {
            return false;
        }
        $data['acid'] = $acid;
        $data['openid'] = $openid;
        return self::save($data);
    }

    public static function isOpenidExists($openid) {
        $allOpenIds = self::getAllOpenidArr();
        return in_array($openid, $allOpenIds);
    }

    /**
     * 获取全部openid列表，
     */
    public static function getAllOpenidArr($cache = 60) {
        return Cachex::funcGet(__METHOD__, function() {
                    $con[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
                    $allOpenids = self::mainModel()->where($con)->column('openid');
                    return $allOpenids;
                }, true, $cache);
    }

    /*     * **设定用户的粉丝信息(缓存)** */

    protected static function setFansInfoCache($openid) {
        $cacheKey = 'WechatWePubFansServiceFansInfo' . $openid;
        return Cachex::set($cacheKey, function() use ($openid) {
                    return self::fansInfoFromDb($openid);
                });
    }

    /**
     * 获取锁定时间段
     * @param type $openid     
     * @return type
     */
    public static function getFansInfoCache($openid) {
        $cacheKey = 'WechatWePubFansServiceFansInfo' . $openid;
        return Cachex::funcGet($cacheKey, function() use ($openid) {
                    return self::fansInfoFromDb($openid);
                });
    }

    /**
     * 20220617;从数据库获取
     * @return type
     */
    protected static function fansInfoFromDb($openid) {
        $con[] = ['openid', '=', $openid];
        $res = self::where($con)->order('id desc')->find();
        return $res ? $res->toArray() : [];
    }

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
     * 更新用户信息
     * @param type $data
     * @return boolean
     */
    public static function updateInfo($data) {
        if (!isset($data['openid']) || !$data['openid']) {
            return false;
        }
        $openid = $data['openid'];
        if (isset($data['id'])) {
            unset($data['id']);
        }
        if (isset($data['subscribe_time'])) {
            $data['subscribe_time2'] = date('Y-m-d H:i:s', $data['subscribe_time']);
        }
        if (self::isOpenidExists($openid)) {
            //更新
            $res = self::mainModel()->where('openid', $openid)->update($data);
        } else {
            //新增
            $res = self::save($data);
        }
        return $res;
    }

    /**
     * 根据openid 反查获取acid
     */
    public static function getAcidByOpenid($openid) {
        $con[] = ['openid', '=', $openid];
        $info = self::mainModel()->where($con)->find();
        return $info ? $info['acid'] : 0;
    }

    /**
     * 通过openid取单条数据
     */
    public static function findByOpenid($openid) {
        $con[] = ['openid', '=', $openid];
        $info = self::mainModel()->where($con)->cache(86400)->find();
        return $info;
    }

    /**
     * 设置手机号码
     * @param type $openid
     * @param type $phone
     * @return boolean
     */
    public static function setPhone($openid, $phone) {
        if (!$phone || !$openid) {
            return false;
        }
        self::mainModel()->where('openid', $openid)->update(['phone' => $phone]);
    }

    /**
     * 20220531 openid缓存key
     */
    public static function openidCacheKey($preSessionId) {
        return 'myOpenid_' . session(SESSION_COMPANY_KEY) . '_' . $preSessionId;
    }

    public function fOpenid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fNickname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fSubscribe() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
