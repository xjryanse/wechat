<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;

/**
 * 微信支付订单
 */
class WechatWxPayLogOrderService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;


    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWxPayLogOrder';

}
