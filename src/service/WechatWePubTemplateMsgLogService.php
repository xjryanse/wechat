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
class WechatWePubTemplateMsgLogService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWePubTemplateMsgLog';
    /**
     * 来源表id取记录
     * @param type $fromTableId
     * @param type $fromTable
     * @return type
     */
    public static function listByFromTableId($fromTableId, $fromTable='') {
        $con[] = ['from_table_id','in',$fromTableId];
        if($fromTable){
            $con[] = ['from_table','=',$fromTable];
        }
        $listsRaw   = self::lists($con);
        $lists      = $listsRaw ? $listsRaw->toArray() : [];
        
        $openids    = array_column($lists,'openid');
        $cond[]      = ['openid','in',$openids];
        $ids        = WechatWePubFansService::ids($cond);
        $wechatWePubFans = WechatWePubFansService::batchGet($ids,'openid','openid,headimgurl,nickname');
        // 循环用户
        foreach($lists as &$v){
            $v['wePubfansInfo'] = $wechatWePubFans[$v['openid']];
        }

        return $lists;
    }
    
    /**
     * 待执行任务列表
     * @param type $withinSecond    只查若干秒内:默认5分钟
     */
    public static function todoListIds( $withinSecond = 300 )
    {
        $con[] = ['send_status','=',XJRYANSE_OP_TODO];
        $con[] = ['create_time','>=',date('Y-m-d H:i:s',time() - $withinSecond)];
        Debug::debug('$todoListIds 的 $con',$con);
        $ids = self::mainModel()->where( $con )->column('id');
        return $ids;
    }
    /**
     * 批量设为正在执行任务列表
     */
    public static function setDoing( $ids = [] )
    {
        if(!$ids){
            return false;
        }
        $con[] = ['id','in',$ids];
        $con[] = ['send_status','=',XJRYANSE_OP_TODO];

        $res = self::mainModel()->where($con)->update(['send_status'=>XJRYANSE_OP_DOING]);
        return $res;
    }
    
    public static function addTodo($acid,$message,$data=[])
    {
        $fromTable      = Arrays::value($data, 'from_table');
        $fromTableId    = Arrays::value($data, 'from_table_id');
        //拿公司id
        $service        = DbOperate::getService($fromTable);
        $info           = $service::getInstance($fromTableId)->get();
        $data['company_id']     = Arrays::value($info, 'company_id');
        $data['acid']           = $acid;
        $data['message']        = json_encode($message);
        $data['send_status']    = XJRYANSE_OP_TODO;
        return self::save($data);
    }
    
    /**
     * 来源表和来源id查是否有记录：
     * 一般用于判断该笔记录是否已入账，避免重复入账
     * @param type $fromTable   来源表
     * @param type $fromTableId 来源表id
     */
    public static function hasLog( $fromTable, $fromTableId ,$con = [])
    {
        $con[] = ['from_table','=',$fromTable];
        $con[] = ['from_table_id','=',$fromTableId];

        return self::count($con) ? self::find( $con ) : false;
    }

}
