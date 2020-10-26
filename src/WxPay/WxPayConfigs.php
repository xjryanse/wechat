<?php
namespace xjryanse\wechat\WxPay;

use xjryanse\wechat\service\WechatWxPayConfigService;
use xjryanse\wechat\WxPay\lib\WxPayConfigInterface;
/*
 * 微信支付配置信息
 */
class WxPayConfigs extends WxPayConfigInterface
{
    /*
     * 配置信息
     */
    protected $info;

    use \xjryanse\traits\InstTrait;
    /**
     * 获取配置项信息（存于数据库）
     * @return type
     */
    protected function getInfo()
    {
        if( $this->info ){
            return $this->info;
        }

        $this->info = WechatWxPayConfigService::getInstance( $this->uuid )->get();
        if( !$this->info ){
            $this->info = WechatWxPayConfigService::getByAppId( $this->uuid );
        }
        return $this->info;
    }
    
    //=======【基本信息设置】=====================================
    /**
     * TODO: 修改这里配置为您自己申请的商户信息
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     */
    public function GetAppId()
    {
        $info = $this->getInfo();
        return $info ? $info['AppId'] : '';
    }

    public function GetMerchantId()
    {
        $info = $this->getInfo();
        return $info ? $info['MerchantId'] : '';
        return '1579507281';
    }

    //=======【支付相关配置：支付成功回调地址/签名方式】===================================
    /**
     * TODO:支付回调url
     * 签名和验证签名方式， 支持md5和sha256方式
     **/
    public function GetNotifyUrl()
    {
        $info = $this->getInfo();
        return $info ? $info['NotifyUrl'] : '';
    }

    public function GetSignType()
    {
        $info = $this->getInfo();
        return $info ? $info['SignType'] : 'MD5';        
    }

    //=======【curl代理设置】===================================
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @var unknown_type
     */
    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        $info       = $this->getInfo();
        $proxyHost  = $info ? $info["ProxyHost"] : "0.0.0.0";
        $proxyPort  = $info ? $info["ProxyPort"] : 0;
    }

    //=======【上报信息配置】===================================
    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * @var int
     */
    public function GetReportLevenl()
    {
        $info = $this->getInfo();
        return $info ? $info["ReportLevenl"] : 0 ;
    }

    //=======【商户密钥信息-需要业务方继承】===================================
    /*
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）, 请妥善保管， 避免密钥泄露
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）， 请妥善保管， 避免密钥泄露
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * @var string
     */
    public function GetKey()
    {
        $info = $this->getInfo();
        return $info ? $info["Key"] : '' ;
    }
    
    public function GetAppSecret()
    {
        $info = $this->getInfo();
        return $info ? $info["AppSecret"] : '' ;
    }

    //=======【证书路径设置-需要业务方继承】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * 注意:
     * 1.证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载；
     * 2.建议将证书文件名改为复杂且不容易猜测的文件名；
     * 3.商户服务器要做好病毒和木马防护工作，不被非法侵入者窃取证书文件。
     * @var path
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        $info           = $this->getInfo();
        $sslCertPath    = $info ? $info["SSLCertParth"] : '/www/mch/'.$this->GetMerchantId().'_apiclient_cert.pem'  ;
        $sslKeyPath     = $info ? $info["SSLKeyParth"]  : '/www/mch/'.$this->GetMerchantId().'_apiclient_key.pem'   ;
    }
}
