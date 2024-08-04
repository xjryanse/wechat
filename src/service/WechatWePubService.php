<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\logic\Cachex;
use xjryanse\logic\Arrays;

/**
 * 微信公众号账户
 */
class WechatWePubService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;
    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePub';
    //一经写入就不会改变的值
    protected static $fixedFields = ['company_id', 'appid', 'secret', 'token', 'encoding_aes_key'
        , 'logo', 'qrcode', 'wx_pay_id', 'creater', 'create_time'];
    
    use \xjryanse\wechat\service\wePub\FieldTraits;

    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $fansCountsArr = WechatWePubFansService::groupBatchCount('acid', $ids);
                    $con[] = ['subscribe', '=', 1];
                    $SFansCountsArr = WechatWePubFansService::groupBatchCount('acid', $ids, $con);
                    foreach ($lists as &$v) {
                        //微信公众号访客数
                        $v['wePubFansCounts'] = Arrays::value($fansCountsArr, $v['id'], 0);
                        //关注粉丝数
                        $v['wePubSFansCounts'] = Arrays::value($SFansCountsArr, $v['id'], 0);
                    }

                    return $lists;
                });
    }

    /**
     * 带缓存get
     * @return type
     */
    public function getCache() {
        return Cachex::funcGet('WechatWePubService_getCache' . $this->uuid, function() {
                    return $this->get();
                });
    }

    /**
     * 0903:appid查数据
     */
    public static function appIdGet($appId) {
        $con[] = ['appid', '=', $appId];
        $info = self::staticConFind($con);
        return $info;
    }

    /**
     * 20221012：公司端口获取
     */
    public static function companyGet() {
        $companyId      = session(SESSION_COMPANY_ID);
        $companyInfo    = SystemCompanyService::getInstance($companyId)->get();
        $wePubId        = Arrays::value($companyInfo, 'we_pub_id');

        $info = self::getInstance($wePubId)->get();
        return $info;
    }

    public function fAppid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fSecret() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fToken() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fEncodingAesKey() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fWxPayId() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fQrcode() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fLogo() {
        return $this->getFFieldValue(__FUNCTION__);
    }

}
