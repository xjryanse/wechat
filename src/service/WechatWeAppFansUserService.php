<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\system\logic\FileLogic;
use xjryanse\user\service\UserService;
use xjryanse\order\service\OrderService;
use Exception;
/**
 * 微信小程序粉丝用户绑定
 */
class WechatWeAppFansUserService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWeAppFansUser';
    //直接执行后续触发动作
    protected static $directAfter = true;

    //2023-01-08：
    public static function extraAfterSave(&$data, $uuid) {
        UserService::clearCommExtraDetailsCache($data['user_id']);
    }

    //2023-01-08：
    public static function extraPreUpdate(&$data, $uuid) {
        $info = self::getInstance($uuid)->get();
        if ($info['user_id']) {
            UserService::clearCommExtraDetailsCache($info['user_id']);
        }
    }

    //2023-01-08：
    public static function extraAfterUpdate(&$data, $uuid) {
        if ($data['user_id']) {
            UserService::clearCommExtraDetailsCache($data['user_id']);
        }
    }

    public function extraAfterDelete($data) {
        // 20230316清除粉丝的手机号码
        $con[] = ['openid', '=', $data['openid']];
        WechatWeAppFansService::where($con)->update(['phone' => '']);
        //2023-01-08：
        UserService::clearCommExtraDetailsCache($data['user_id']);
    }

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $orderCount = OrderService::groupBatchCount('user_id', array_column($lists, 'user_id'));
                    foreach ($lists as &$v) {
                        $v['orderCount'] = Arrays::value($orderCount, $v['user_id'], 0);
                    }
                    return $lists;
                }, true);
    }

    /**
     * 改绑
     */
    public static function changeBind($openid, $userId) {
        if (!$openid || !$userId) {
            return false;
        }
        $con[] = ['openid', '=', $openid];
        $info = self::find($con);
        if ($info['id']) {
            $data['user_id'] = $userId;
            $res = self::mainModel()->where('id', $info['id'])->update($data);
        } else {
            $data['openid'] = $openid;
            $data['user_id'] = $userId;
            $res = self::save($data);
        }
    }

    public static function getUserIdByOpenid($openid, $scene = '') {
        $con[] = ['openid', '=', $openid];
        if ($scene) {
            $con[] = ['scene', '=', $scene];
        }
        $info = self::find($con);
        return $info ? $info['user_id'] : '';
    }

    /**
     * 获取绑定的用户id
     * @param type $openid      openid
     * @param type $scene       场景值
     * @param type $emptyCreate 无用户是否创建
     * @param type $extraData   用户创建时写入的额外信息
     * @return type
     */
    public static function getBindUserId($openid, $scene = "", $emptyCreate = false, $extraData = []) {
        //情况①：已经绑定直接返回
        $con = [];
        $con[] = ['openid', '=', $openid];
        $con[] = ['scene', '=', $scene];
        $info = self::find($con, 0);   //无缓存查数据 
        if ($info) {
            return $info['user_id'];
        }
        //情况②：没有用户信息的情况
        $bindWeAppUserId = self::bindWeAppUserId($openid);
        if ($bindWeAppUserId) {
            return $bindWeAppUserId;
        }
        //无用户时创建新用户
        if ($emptyCreate) {
            //情况③：没用户，创建新的，再绑定
            return self::bindNewGetUserId($openid, $scene, $extraData);
        } else {
            return '';
        }
    }

    /**
     * 新创建一个用户，并绑定
     * @param type $openid
     * @param type $scene
     * @param type $extraData
     */
    protected static function bindNewGetUserId($openid, $scene = "", $extraData = []) {
        $weAppFans = WechatWeAppFansService::findByOpenid($openid);
        //头像存本地
        $headImgInfo = FileLogic::saveUrlFile($weAppFans['avatar_url']);
        $userData = Arrays::getByKeys($weAppFans->toArray(), ['nickname']);
        //头像
        $userData['headimg'] = $headImgInfo ? $headImgInfo['id'] : '';
        $userData['username'] = $openid;      //用openid作临时用户名
        $userDataMerge = array_merge($userData, $extraData);
        //$userInfo               = UserService::save( $userDataMerge );          
        $userId = UserService::openidGetId($openid, $userDataMerge);
        //throw new \Exception('测试$userId'.$userId." openid".$openid);
        //创建后绑定
        $bindData = [];
        $bindData['acid'] = $weAppFans['acid'];
        $bindData['openid'] = $openid;
        $bindData['scene'] = $scene;
        $bindData['user_id'] = $userId;
        $bindDataMerge = array_merge($extraData, $bindData);
        $resp = self::save($bindDataMerge);
        return $resp['user_id'];
    }

    /**
     * 绑定公众号的用户id
     * @param type $openid
     * @param type $scene
     */
    protected static function bindWeAppUserId($openid, $scene = "") {
        $weAppFans = WechatWeAppFansService::findByOpenid($openid);
        $wePubFansOpenid = WechatWePubFansService::unionidToOpenid($weAppFans['unionid']);
        $wePubBindUserId = WechatWePubFansUserService::getUserIdByOpenid($wePubFansOpenid);
        if ($wePubFansOpenid && $wePubBindUserId) {
            //将小程序和公众号绑定为同一个用户
            $bindData = [];
            $bindData['acid'] = $weAppFans['acid'];
            $bindData['openid'] = $openid;
            $bindData['scene'] = $scene;
            $bindData['user_id'] = $wePubBindUserId;
            $resp = self::save($bindData);
            return $resp['user_id'];
        }
        return '';
    }

    /**
     * 反写用户id
     * 20230609:todo:解决历史遗留bug
     */
    public static function doUserBack($id) {
        $info = self::getInstance($id)->get();
        $hasUser = UserService::getInstance($info['user_id'])->get();
        if ($hasUser) {
            throw new Exception('用户' . $info['user_id'] . '已存在');
        }
        $fansInfo = WechatWeAppFansService::findByOpenid($info['openid']);
        if (!$fansInfo) {
            throw new Exception('小程序用户' . $info['openid'] . '不存在');
        }
        if($fansInfo['phone']){
            $data['id']         = $info['user_id'];
            $data['phone']      = $fansInfo['phone'];
            $data['username']   = $fansInfo['phone'];
            return UserService::save($data);
        } else {
            $data['id']         = $info['user_id'];
            $data['username']   = $fansInfo['openid'];
            return UserService::save($data);
        }
    }

}
