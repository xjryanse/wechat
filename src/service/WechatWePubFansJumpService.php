<?php

namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Cachex;
use xjryanse\logic\Arrays;
use Exception;
/**
 * 微信公众号粉丝
 */
class WechatWePubFansJumpService implements MainModelInterface {

    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;
    use \xjryanse\traits\MainModelRamTrait;
    use \xjryanse\traits\MainModelCacheTrait;
    use \xjryanse\traits\MainModelCheckTrait;
    use \xjryanse\traits\MainModelGroupTrait;
    use \xjryanse\traits\MainModelQueryTrait;

    use \xjryanse\traits\ObjectAttrTrait;

    protected static $mainModel;
    protected static $mainModelClass = '\\xjryanse\\wechat\\model\\WechatWePubFansJump';

    public static function getUrl($openid, $key) {
        return Cachex::funcGet(__METHOD__, function() use ($openid, $key) {
                    $con[] = ['openid', '=', $openid];
                    $con[] = ['jump_key', '=', $key];
                    $info = self::mainModel()->where($con)->find();
                    return $info ? $info['jump_url'] : '';
                });
    }

    public function fOpenid() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fNickname() {
        return $this->getFFieldValue(__FUNCTION__);
    }

    public function fSubscribe() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    /**
     * 20230722:复制已有的数据进行绑定
     * @param type $openid
     */
    public function idBindNew($openid){
        $info = $this->get();
        if(!$info){
            throw new Exception('链接绑定记录'.$this->uuid.'不存在');
        }
        if($info['openid'] == $openid){
            throw new Exception('您已绑定当前链接'.$this->uuid.'，无需重复绑定');
        }
        
        // 先删除
        $con[] = ['openid','=',$openid];
        self::where($con)->delete();
        // 再添加
        $data['jump_key']   = Arrays::value($info, 'jump_key');
        $data['jump_url']   = Arrays::value($info, 'jump_url');
        $data['openid']     = $openid;
        return self::save($data);
    }
    
    
}
