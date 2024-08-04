<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\wechat\service\WechatWeAppService;
use xjryanse\logic\Arrays;
use Exception;

/**
 * 微信小程序二维码场景值
 */
class WechatWeAppQrSceneService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWeAppQrScene';
    // 是否缓存get数据
    protected static $getCache = true;

    public static function paramGetId($weAppId, $param = []) {
        ksort($param);
        $paramStr = json_encode($param, JSON_UNESCAPED_UNICODE);
        $md5 = md5($paramStr);
        $con[] = ['we_app_id', '=', $weAppId];
        $con[] = ['md5', '=', $md5];

        $info = self::where($con)->find();
        if (!$info) {
            $data['we_app_id'] = $weAppId;
            $data['param'] = $paramStr;
            $data['md5'] = $md5;
            $info = self::save($data);
        }

        return $info ? $info['id'] : '';
    }

    public static function extraPreSave(&$data, $uuid) {
        if (isset($data['param'])) {
            $data['md5'] = md5($data['param']);
        }

        return $data;
    }

    public static function extraPreUpdate(&$data, $uuid) {
        if (isset($data['param'])) {
            $data['md5'] = md5($data['param']);
        }

        return $data;
    }

    /**
     * id取参数
     */
    public static function idGetParam($id) {
        $info = self::mainModel()->where('id', $id)->find();
        $param = $info['param'];
        return json_decode($param, JSON_UNESCAPED_UNICODE);
    }
    /**
     * 20230908：生成二维码
     * @param type $param
     * @param type $fromTable
     * @param type $fromTableId
     */
    public static function generate(array $param, $fromTable, $fromTableId){
        // 提取公司信息
        $companyId      = session(SESSION_COMPANY_ID);
        $companyInfo    = SystemCompanyService::getInstance($companyId)->get();
        
        $weAppId        = Arrays::value($companyInfo, 'we_app_id');
        if(!$weAppId){
            throw new Exception('未配置小程序信息');
        }
        $weAppInfo = WechatWeAppService::getInstance($weAppId)->get();

        $data['we_app_id']      = Arrays::value($weAppInfo, 'appid');
        $data['param']          = json_encode($param,JSON_UNESCAPED_UNICODE);
        $data['from_table']     = $fromTable;
        $data['from_table_id']  = $fromTableId;

        return self::save($data);
    }
    /**
     * 20230911:来源表id转二维码id
     * 适用于单表
     */
    public static function fromTableIdToId($fromTableId, $con = []){
        $con[] = ['from_table_id','=',$fromTableId];
        $info  = self::staticConFind($con);
        return Arrays::value($info,'id');
    }

}
