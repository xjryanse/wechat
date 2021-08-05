<?php
namespace xjryanse\wechat\WeOpen\traits;

use xjryanse\wechat\service\WechatWeOpenService;
use xjryanse\logic\Arrays;
/**
 * 单例复用
 */
trait InstTrait
{
    protected $appId;
    protected $appSecret;
    protected $token;           // 消息加解密token
    protected $encodingAesKey;  //消息加解密key       
    protected $uuid;
    protected static $instances;

    protected function __clone(){}

    public function __construct( $uuid = 0 ){
        $this->uuid      = $uuid;
        $weOpen = WechatWeOpenService::getInstance( $this->uuid )->get();
        $this->appId            = Arrays::value($weOpen, 'appid');
        $this->appSecret        = Arrays::value($weOpen, 'secret');
        $this->token            = Arrays::value($weOpen, 'token');
        $this->encodingAesKey   = Arrays::value($weOpen, 'encoding_aes_key');
    }
    /**
     * 有限多例
     */
    public static function getInstance( $uuid = 0 )
    {
        if( !isset( self::$instances[ $uuid ] ) || ! self::$instances[ $uuid ] ){
            self::$instances[ $uuid ] = new self( $uuid );
        }
        return self::$instances[ $uuid ];
    }
}
