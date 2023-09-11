<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Debug;
use xjryanse\logic\DbOperate;
use xjryanse\logic\Arrays;
use xjryanse\wechat\service\WechatWePubFansService;

/**
 * 模板消息发送记录
 */
class WechatWePubTemplateMsgLogService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelQueryTrait;
    use \xjryanse\traits\RedisModelTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubTemplateMsgLog';
    //直接执行后续触发动作
    protected static $directAfter = true;

    //2023-01-08：
    public static function extraAfterSave(&$data, $uuid) {
        if ($data['send_status'] == XJRYANSE_OP_TODO) {
            // self::redisTodoAdd($data);
        }
    }

    /**
     * 来源表id取记录
     * @param type $fromTableId
     * @param type $fromTable
     * @return type
     */
    public static function listByFromTableId($fromTableId, $fromTable = '') {
        $con[] = ['from_table_id', 'in', $fromTableId];
        if ($fromTable) {
            $con[] = ['from_table', '=', $fromTable];
        }
        $listsRaw = self::lists($con);
        $lists = $listsRaw ? $listsRaw->toArray() : [];

        $openids = array_column($lists, 'openid');
        $cond[] = ['openid', 'in', $openids];
        $ids = WechatWePubFansService::ids($cond);
        $wechatWePubFans = WechatWePubFansService::batchGet($ids, 'openid', 'openid,headimgurl,nickname');
        // 循环用户
        foreach ($lists as &$v) {
            $v['wePubfansInfo'] = $wechatWePubFans[$v['openid']];
        }

        return $lists;
    }

    /**
     * 待执行任务列表
     * @param type $withinSecond    只查若干秒内:默认5分钟
     */
    public static function todoListIds($withinSecond = 300) {
        // 没有则从数据库取
        $con[] = ['send_status', '=', XJRYANSE_OP_TODO];
        $con[] = ['create_time', '>=', date('Y-m-d H:i:s', time() - $withinSecond)];
        Debug::debug('$todoListIds 的 $con', $con);
        // 20230611:控频50条
        $data = self::mainModel()->where($con)->limit(50)->select();
        $dataArr = $data ? $data->toArray() : [];
        return array_column($dataArr, 'id');

//        if(!self::redisHasTodoTime()){
//            // 没有则从数据库取
//            $con[] = ['send_status','=',XJRYANSE_OP_TODO];
//            $con[] = ['create_time','>=',date('Y-m-d H:i:s',time() - $withinSecond)];
//            Debug::debug('$todoListIds 的 $con',$con);
//            $data = self::mainModel()->where( $con )->select();
//            $dataArr = $data ? $data->toArray() : [];
//            self::redisTodoAddBatch($dataArr);
//        }
//        // 20230415：调整从redis 取数据
//        $todoList = self::redisTodoList();
//        Debug::debug('redis 提取的 $todoList', $todoList);
//        $ids = array_column($todoList, 'id');
//
//        return $ids;
    }

    /**
     * 批量设为正在执行任务列表
     */
    public static function setDoing($ids = []) {
        if (!$ids) {
            return false;
        }
        $con[] = ['id', 'in', $ids];
        $con[] = ['send_status', '=', XJRYANSE_OP_TODO];

        $res = self::mainModel()->where($con)->update(['send_status' => XJRYANSE_OP_DOING]);
        return $res;
    }

    public static function addTodo($acid, $message, $data = []) {
        $fromTable = Arrays::value($data, 'from_table');
        $fromTableId = Arrays::value($data, 'from_table_id');
        //拿公司id
        if (!Arrays::value($data, 'company_id') && $fromTable && $fromTableId) {
            $service = DbOperate::getService($fromTable);
            $info = $service::getInstance($fromTableId)->get();
            $data['company_id'] = Arrays::value($info, 'company_id') ? : session(SESSION_COMPANY_ID);
        }
        $data['acid'] = $acid;
        $data['message'] = json_encode($message, JSON_UNESCAPED_UNICODE);
        $data['send_status'] = XJRYANSE_OP_TODO;

        return self::save($data);
    }

    /**
     * 来源表和来源id查是否有记录：
     * 一般用于判断该笔记录是否已入账，避免重复入账
     * @param type $fromTable   来源表
     * @param type $fromTableId 来源表id
     */
    public static function hasLog($fromTable, $fromTableId, $con = []) {
        $con[] = ['from_table', '=', $fromTable];
        $con[] = ['from_table_id', '=', $fromTableId];

        return self::count($con) ? self::find($con) : false;
    }

    /**
     * 20230322
     * @param type $templateKey
     * @param type $fromTableId
     * @param type $con
     * @return type
     */
    public static function keyIdHasLog($templateKey, $fromTableId, $con = []) {
        $con[] = ['template_key', '=', $templateKey];
        $con[] = ['from_table_id', '=', $fromTableId];

        return self::count($con) ? true : false;
    }

    /**
     * 20230607:提取用户信息
     * @param type $param
     */
    public static function findUserMsg($param) {
        $id = Arrays::value($param, 'id');
        $info = self::getInstance($id)->get();
        if (!$info) {
            return [];
        }
        $msgArr = json_decode($info['message'], true);
        $data = $msgArr['data'];
        $keys = array_keys($data);
        $res = [];
        foreach ($keys as $key) {
            $res[$key] = Arrays::value($data[$key], 'value');
        }

        return $res;
    }

}
