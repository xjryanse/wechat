<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\user\service\UserService;
use xjryanse\logic\Debug;
use xjryanse\logic\Arrays;
use xjryanse\logic\Arrays2d;


/**
 * 微信公众号粉丝用户绑定
 */
class WechatWePubFansUserService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubFansUser';
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
        //2023-01-08：
        UserService::clearCommExtraDetailsCache($data['user_id']);
    }

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $userCount = UserService::groupBatchCount('id', array_column($lists, 'user_id'));
                    foreach ($lists as &$v) {
                        $v['isUserExist'] = Arrays::value($userCount, $v['user_id'], 0);
                    }
                    return $lists;
                });
    }

    /**
     * 查询用户openid是否有绑定
     */
    public static function openidHasBind($openid) {
        $con[] = ['openid', '=', $openid];
        $info = self::find($con);
        if (!$info) {
            return false;
        }
        // 20220924:用户名是openid,需换绑，认为是未绑
        $username = UserService::getInstance($info['user_id'])->fUsername();
        if ($username == $openid) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * 给定用户id,查询用户是否已绑定
     */
    public static function userHasBind($userId){
        $con[] = ['user_id', '=', $userId];
        return self::count($con) ? true : false;
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
        return $res;
    }

    /*
     * 根据user_id,取绑定的openid列表
     */

    public static function columnOpenidByUserId($userId) {
        $con[] = ['user_id', '=', $userId];
        return self::column('openid', $con);
    }

    /**
     * 根据userId取openid
     * @param type $openid
     * @param type $scene
     * @return type
     */
    public static function getUserIdByOpenid($openid, $scene = '') {
        $con[] = ['openid', '=', $openid];
        if ($scene) {
            $con[] = ['scene', '=', $scene];
        }
        Debug::debug('getUserIdByOpenid的$con', $con);
        $info = self::find($con, 1);
        Debug::debug('getUserIdByOpenid的$info', $info);
        return $info ? $info['user_id'] : '';
//        return Cachex::funcGet( __CLASS__ . '_' . __METHOD__. $openid. $scene. session(SESSION_COMPANY_ID), function() use ($openid, $scene){
//            $con[] = ['openid','=',$openid ];
//            if( $scene ){
//                $con[] = ['scene','=',$scene ];
//            }
//            $info = self::find( $con );
//            return $info ? $info['user_id'] : '';
//        });
    }

    /**
     * 公司id
     * @param type $openid
     * @param type $scene
     * @return type
     */
    public static function getCompanyIdByOpenid($openid, $scene = '') {
        $con[] = ['openid', '=', $openid];
        if ($scene) {
            $con[] = ['scene', '=', $scene];
        }
        $info = self::find($con);

        return $info ? $info['company_id'] : '';
    }

    /**
     * 更新绑定用户信息
     * @param type $openid  openid
     * @param type $scene   场景
     */
    public static function updateUserIdByOpenid($openid, $userId, $scene = "", $companyId = 0) {
        $data['openid'] = $openid;
        $data['scene'] = $scene;
        $data['user_id'] = $userId;

        $con[] = ['openid', '=', $openid];
        $con[] = ['scene', '=', $scene];
        if (!$companyId) {
            $con[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
        } else {
            $data['company_id'] = $companyId;
            $con[] = ['company_id', '=', $companyId];
        }

        $id = self::mainModel()->where($con)->value('id');
        if ($id) {
            if ($userId) {
                //更新
                self::getInstance($id)->update($data);
                $res = self::getInstance($id)->get();
            } else {
                //解绑
                $res = self::getInstance($id)->delete();
            }
        } else {
            if ($userId) {
                //新增
                $res = self::save($data);
            }
        }
        return $res;
    }
	
	/**
     * 获取当前的账号：当有多个没有指定is_current时，返回false,表示获取失败
     * 20231210
     */
    public static function getCurrent($openid){
        $con = [];
        $con[] = ['openid','=',$openid];
        $lists = self::mainModel()->where($con)->select();

        $arr = $lists ? $lists->toArray() : [];
        if(!$arr){
            return false;
        }
        if(count($arr) == 1){
            return $arr[0];
        }
        
        // 提取当前,没有的话会返回空数组，性质一样
        $con[] = ['is_current','=',1];
        return Arrays2d::listFind($arr, $con);
    }
    /*
     * 20231210：设为当前端口
     */
    public function setCurrent(){
        $info = $this->get();
        $con = [];
        $con[] = ['openid','=',$info['openid']];
        self::mainModel()->where($con)->update(['is_current'=>0]);
        return $this->updateRam(['is_current'=>1]);
    }

}
