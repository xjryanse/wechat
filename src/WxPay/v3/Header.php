<?php

namespace xjryanse\wechat\WxPay\v3;


/**
 * v3接口开发（官方首页只剩下V3版本了）
 * @createTime 2024-05-06
 * https://pay.weixin.qq.com/docs/merchant/development/interface-rules/signature-generation.html
 * 我们希望商户的技术开发人员按照当前文档约定的规则构造签名串。微信支付会使用同样的方式构造签名串。如果商户构造签名串的方式错误，将导致签名验证不通过。下面先说明签名串的具体格式。
 */
class Header {
    
    /**
     * 签名串一共有五行，每一行为一个参数。结尾以\n（换行符，ASCII编码值为0x0A）结束，包括最后一行。如果参数本身以\n结束，也需要附加一个\n
     * @param type $method  获取HTTP请求的方法（GET，POST，PUT）等
     * @param type $url     获取请求的绝对URL，并去除域名部分得到参与签名的URL。如果请求中有查询参数，URL末尾应附加有'?'和对应的查询字符串。
     * @param type $body    获取请求中的请求报文主体（request body）
     */
    public static function authorization($method, $url, $randStr, $body = ''){
        $timestamp  = time();

        $str        = $method . "\n" . $url . "\n" . $timestamp . "\n".$randStr. "\n". $body ;

        dump($str);
        return 'authorization';
    }
    
    

}
