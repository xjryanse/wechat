<?php
namespace xjryanse\wechat\service\wePubTemplateMsgLog;

use xjryanse\logic\Arrays;
use xjryanse\logic\DataCheck;
use xjryanse\wechat\service\WechatWePubTemplateMsgService;
use xjryanse\sql\service\SqlService;
use Exception;
use think\Db;
/**
 * 字段复用列表
 */
trait DoTraits{
    /**
     * 参数
     * @param type $param
     */
    public static function doMsgSend($param){
        $keys = ['template_key','id'];
        DataCheck::must($param, $keys);
        
        $templateKey    = Arrays::value($param, 'template_key');
        $tplId          = WechatWePubTemplateMsgService::keyToId($templateKey);
        $tplInfo        = WechatWePubTemplateMsgService::getInstance($tplId)->get();

        $sqlKey         = Arrays::value($tplInfo, 'sql_key');
        if(!$sqlKey){
            throw new Exception('模板'.$templateKey.'的sql_key未配置，请联系您的软件服务商');
        }
        $sql    = SqlService::keyBaseSql($sqlKey);
        // dump($sql);
        $id     = Arrays::value($param, 'id');
        $info   = Db::table($sql.' as mainTable')->where('id',$id)->find();

        $fromTable = '';

        self::templateMsgMatchAddTodo($fWePubId, $templateKey, $openids, $info, $fromTable, $id);
    }
}
