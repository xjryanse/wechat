<?php
namespace xjryanse\wechat\service;

use xjryanse\system\interfaces\MainModelInterface;
use xjryanse\logic\Cachex;
/**
 * 微信公众号账户
 */
class WechatWePubService implements MainModelInterface
{
    use \xjryanse\traits\InstTrait;
    use \xjryanse\traits\MainModelTrait;

    protected static $mainModel;
    protected static $mainModelClass    = '\\xjryanse\\wechat\\model\\WechatWePub';
    //一经写入就不会改变的值
    protected static $fixedFields = ['company_id','appid','secret','token','encoding_aes_key'
        ,'logo','qrcode','wx_pay_id','creater','create_time'];
    /**
     * 带缓存get
     * @return type
     */
    public function getCache(){
        return  Cachex::funcGet('WechatWePubService_getCache'.$this->uuid, function(){
            return $this->get();
        });
    }
    
    public function fAppid() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    public function fSecret() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    public function fToken() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    public function fEncodingAesKey() {
        return $this->getFFieldValue(__FUNCTION__);
    }    
    public function fWxPayId() {
        return $this->getFFieldValue(__FUNCTION__);
    }    
    public function fQrcode() {
        return $this->getFFieldValue(__FUNCTION__);
    }
    
    public function fLogo() {
        return $this->getFFieldValue(__FUNCTION__);
    }
}
