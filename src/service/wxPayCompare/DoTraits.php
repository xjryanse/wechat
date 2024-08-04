<?php
namespace xjryanse\wechat\service\wxPayCompare;

use xjryanse\logic\DataCheck;

/**
 * 字段复用列表
 */
trait DoTraits{
    /**
     * 参数
     * @param type $param
     */
    public static function doDownloadByDate($param){
        $keys = ['date'];
        DataCheck::must($param, $keys);
        // 20240723
        return self::syncByDate($param['date']);
    }
}
