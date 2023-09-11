<?php
namespace xjryanse\wechat\WeApp\traits;

//use xjryanse\wechat\service\WechatWeOpenService;
use xjryanse\wechat\service\WechatWeAppService;
use xjryanse\logic\Arrays;
/**
 * 20230616 单例复用
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
        $weApp = WechatWeAppService::getInstance( $this->uuid )->get();

        $this->appId            = Arrays::value($weApp, 'appid');
        $this->appSecret        = Arrays::value($weApp, 'secret');
        $this->token            = Arrays::value($weApp, 'token');
        $this->encodingAesKey   = Arrays::value($weApp, 'encoding_aes_key');
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
