<?php
namespace xjryanse\wechat\WeOpen\traits;

use xjryanse\wechat\WeOpen\Auth;
use Exception;
/**
 * 授权方appid复用类
 * 必须和xjryanse\wechat\WeOpen\traits\InstTrait同时使用;
 */
trait AuthAppidInstTrait
{
    protected $authAppId;
    /**
     * 设定授权方appid
     * @param type $authAppId
     */
    public function setAuthAppId( $authAppId ){
        $this->authAppId = $authAppId;
    }
    
    public function getAuthAppId( ){
        return $this->authAppId;
    }
    
    public function getApiAuthorizerToken(){
        if(!$this->getAuthAppId()){
            throw new Exception('AuthAppidInstTrait-getApiAuthorizerToken未指定授权账号appid');
        }
        return Auth::getInstance( $this->uuid )->apiAuthorizerToken( $this->getAuthAppId() );       
    }
}
