<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Arrays;
use xjryanse\sql\service\SqlService;
use xjryanse\logic\DataCheck;
/**
 * 模板消息
 */
class WechatWePubTemplateMsgService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\StaticModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubTemplateMsg';

    use \xjryanse\wechat\service\wePubTemplateMsg\SendTraits;

    
    public static function extraDetails($ids) {
        return self::commExtraDetails($ids, function($lists) use ($ids) {
                    $cond[] = ['company_id', '=', session(SESSION_COMPANY_ID)];
                    $logArr = WechatWePubTemplateMsgLogService::groupBatchCount('template_key', array_column($lists, 'template_key'), $cond);
                    $userArr = WechatWePubTemplateMsgUserService::groupBatchCount('template_key', array_column($lists, 'template_key'), $cond);
                    foreach ($lists as &$v) {
                        $v['logCount'] = Arrays::value($logArr, $v['template_key'], 0);
                        $v['userCount'] = Arrays::value($userArr, $v['template_key'], 0);
                    }
                    return $lists;
                });
    }

    public static function keyToId($key){
        $con[] = ['template_key','=',$key];
        $info = self::staticConFind($con);
        return $info ? $info['id'] : '';
    }
    
    /**
     * 根据模板key取信息
     * @param type $templateKey
     * @return type
     */
    public static function getByTemplateKey($templateKey) {
        $con[] = ['template_key', '=', $templateKey];
        return self::find($con);
    }
    /**
     * 
     */
    public static function sendWithDataId($dataId){
        
        
    }
    
    /**
     * 20240127:异步消息推送
     */
    public static function doAsyncMsgGenerate(){
        $con[] = ['status','=',1];
        $con[] = ['sql_key','<>',''];
        $lists = self::staticConList($con);

        foreach($lists as $v){
            $sqlKey = $v['sql_key'];
            // 20240127:防null
            if(!$sqlKey){
                continue;
            }
            $templateKey = $v['template_key'];
            // 20240127:防null
            if(!$templateKey){
                continue;
            }
            // 提取数据;发送模板消息
            self::sqlDataSend($templateKey, $sqlKey);
        }
        return true;
    }
    /**
     * sql 提取数据，然后发送
     */
    protected static function sqlDataSend($templateKey, $sqlKey){
        // 提取数据
        $dataArr = SqlService::sqlPaginateDataRaw($sqlKey);
        foreach($dataArr['data'] as $info){
            // 20240127:匹配发送消息
            $userId = Arrays::value($info, 'accUserId');
            // 20240714：抽离发送逻辑
            self::doSendByDataAndUser($templateKey, $info, $userId);
        }
        return true;
    }
    /**
     * sql发送测试
     */
    public function doSqlSendTest(){
        $info = $this->get();
        $keys = ['template_key','sql_key'];
        DataCheck::must($info, $keys);

        $templateKey    = Arrays::value($info, 'template_key');
        $sqlKey         = Arrays::value($info, 'sql_key');
        
        return self::sqlDataSend($templateKey, $sqlKey);
    }

}
