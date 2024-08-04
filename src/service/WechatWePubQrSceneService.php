<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\system\service\SystemCompanyService;
use xjryanse\wechat\service\WechatWeAppService;
use xjryanse\wechat\service\WechatWePubService;
use xjryanse\wechat\WePub\QRCode as wpQrCode;
use app\qrcode\service\QrcodeService;
use xjryanse\logic\Arrays;
use Exception;

/**
 * 微信小程序二维码场景值
 */
class WechatWePubQrSceneService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubQrScene';
    // 是否缓存get数据
    protected static $getCache = true;
    
    use \xjryanse\wechat\service\wePubQrScene\FieldTraits;

    public static function paramGetId($wePubId, $param = []) {
        ksort($param);
        $paramStr = json_encode($param, JSON_UNESCAPED_UNICODE);
        $md5 = md5($paramStr);
        $con[] = ['we_pub_id', '=', $wePubId];
        $con[] = ['md5', '=', $md5];

        $info = self::where($con)->find();
        if (!$info) {
            $data['we_pub_id']  = $wePubId;
            $data['param']      = $paramStr;
            $data['md5']        = $md5;
            $info               = self::saveRam($data);
        }

        return $info ? $info['id'] : '';
    }

    public static function extraPreSave(&$data, $uuid) {
        self::stopUse(__METHOD__);
        if (isset($data['param'])) {
            $data['md5'] = md5($data['param']);
        }

        return $data;
    }

    public static function extraPreUpdate(&$data, $uuid) {
        self::stopUse(__METHOD__);
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
        
        $wePubId        = Arrays::value($companyInfo, 'we_pub_id');
        if(!$wePubId){
            throw new Exception('未配置公众号信息，端口'.$companyId);
        }

        $data['we_pub_id']      = $wePubId;
        $data['param']          = json_encode($param,JSON_UNESCAPED_UNICODE);
        $data['from_table']     = $fromTable;
        $data['from_table_id']  = $fromTableId;

        return self::saveRam($data);
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
    
    /**
     * 微信二维码链接
     * 20240613
     */
    public function wxQrcodeUrl(){
        $info = $this->get();
        if(!$info){
            throw new Exception('公众号场景二维码信息不存在'.$this->uuid);
        }
        $wePubId = Arrays::value($info, 'we_pub_id');
        // 20240613:生成永久二维码（数量请控制）
        $actionName = 'QR_LIMIT_STR_SCENE';
        $sceneStr   = $this->uuid;
        $res = wpQrCode::getInstance($wePubId)->qrcodeCreate($actionName, $sceneStr);
        // ["ticket"] => string(96) "gQGo8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTm1aWFYtcDhlUGoxMDAwME0wN0gAAgRopWpmAwQAAAAA"
        // ["url"] => string(45) "http://weixin.qq.com/q/02NmZXV-p8ePj10000M07H"
        return Arrays::value($res, 'url');
    }
    /**
     * 20240613:生成微信公众号场景值二维码
     * 拼接了一些参数
     */
    public function wxQrcode(){
        $wePubId    = $this->fWePubId();
        $label      = $this->fLabel();

        $logo = WechatWePubService::getInstance($wePubId)->fLogo();

        $arr['content']     = $this->wxQrcodeUrl();
        $arr['size']        = 500;
        $arr['suffix']      = "png";
        $arr['margin']      = 30;
        if($logo){
            $arr['logo_path']   = './'.$logo['rawPath'];
            $arr['logo_size']   = "150, 150";
        }

        $arr['save_path']   = "./ttt0613.png";
        $arr['label']       = $label;
        // 返回结果，应直接使用echo 输出
        return QrcodeService::qrcode( $arr );
    }
    
    
}
