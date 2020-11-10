<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信支付记录
 */
class WechatWxPayLogService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWxPayLog';

    /**
     * 拆解返回数据保存
     */
    public static function tearData()
    {
        $con[]  = ['appid','=',''];
        $list   = self::lists( $con );
        foreach( $list as &$v){
            $data       = json_decode($v['val'], true);
            $data['id'] = $v['id'];
            self::getInstance( $data['id'] )->update( $data );
//            $this->async( $v['id'] );
        }
    }
    /**
     * 处理订单财务数据
     */
    private function dealOrder()
    {
        
    }

}
