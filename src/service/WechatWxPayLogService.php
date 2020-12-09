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
     * 拆解val数据保存
     */
    public function tearValData()
    {
        $info = $this->get();
        //val转数组
        $data       = json_decode($info['val'], true);
        return $this->update($data);
    }
    /**
     * 根据商户单号获取支付信息
     */
    public static function getByOutTradeNo( $paySn ,$con = [])
    {
        $con[] = [ 'out_trade_no','=',$paySn ];
        return self::find( $con );
    }
}
