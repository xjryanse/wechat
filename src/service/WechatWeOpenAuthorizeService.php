<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;

/**
 * 微信开放平台
 */
class WechatWeOpenAuthorizeService extends Base implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWeOpenAuthorize';

    /**
     * 授权后保存授权数据
     */
    public static function authSave($weOpenId, $authData) {
        $data = $authData['authorization_info'];
        $data['we_open_id'] = $weOpenId;
        $data['func_info'] = json_encode($data['func_info']);
        $authorizerAppid = Arrays::value($data, 'authorizer_appid');
        $con[] = ['authorizer_appid', '=', $authorizerAppid];
        $id = self::mainModel()->where($con)->value('id');
        if ($id) {
            self::getInstance($id)->update($data);
        } else {
            self::save($data);
        }
        return self::find($con, 0);
    }

    /**
     * 根据授权方appid，取授权信息
     * @param type $authorizerAppid
     * @return type
     */
    public static function getByAuthorizerAppid($authorizerAppid) {
        $con[] = ['authorizer_appid', '=', $authorizerAppid];
        return self::find($con);
    }

    /**
     * 授权账号appid,取开放平台id
     */
    public static function authorizerAppidGetWeOpenId($authorizerAppid) {
        $con[] = ['authorizer_appid', '=', $authorizerAppid];
        return self::mainModel()->where($con)->value('we_open_id');
    }

    /**
     * 20220514
     * @param type $authorizerAppid
     * @return type
     */
    public static function appidGetId($authorizerAppid) {
        $con[] = ['authorizer_appid', '=', $authorizerAppid];
        return self::mainModel()->where($con)->value('id');
    }

    /**
     * 设定小程序状态
     * 
     */
    public function setAppStatus($status) {
        $data['app_status'] = $status;
        return $this->update($data);
    }

    /**
     *
     */
    public function fId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 排序
     */
    public function fSort() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 状态(0禁用,1启用)
     */
    public function fStatus() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 有使用(0否,1是)
     */
    public function fHasUsed() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未锁，1：已锁）
     */
    public function fIsLock() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 锁定（0：未删，1：已删）
     */
    public function fIsDelete() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 备注
     */
    public function fRemark() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建者，user表
     */
    public function fCreater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新者，user表
     */
    public function fUpdater() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 创建时间
     */
    public function fCreateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    /**
     * 更新时间
     */
    public function fUpdateTime() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
