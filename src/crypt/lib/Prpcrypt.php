<?php

namespace xjryanse\wechat\crypt\lib;

use Exception;
use xjryanse\logic\Debug;
/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt {

    public $key;

    function __construct($k) {
        $this->key = base64_decode($k . "=");
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($text, $appid) {
        //获得16位随机字符串，填充到明文之前
        $random = $this->getRandomStr();
        $text = $random . pack("N", strlen($text)) . $text . $appid;
        // 网络字节序
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = substr($this->key, 0, 16);
        //使用自定义的填充方式对明文进行补位填充
        $pkc_encoder = new PKCS7Encoder;
        $text = $pkc_encoder->encode($text);
        mcrypt_generic_init($module, $this->key, $iv);
        //加密
        $encrypted = mcrypt_generic($module, $text);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        //print(base64_encode($encrypted));
        //使用BASE64对加密后的字符串进行编码
        return array(ErrorCode::$OK, base64_encode($encrypted));
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($encrypted, $appid) {
        //靠谱
        // https://zhuanlan.zhihu.com/p/27210740
        $iv = substr($this->key, 0, 16);          
        $decrypted = openssl_decrypt($encrypted,'AES-256-CBC',substr($this->key, 0, 32),OPENSSL_ZERO_PADDING,$iv);

        //去除补位字符
        $pkc_encoder = new PKCS7Encoder;
        $result = $pkc_encoder->decode($decrypted);
        //去除16位随机字符串,网络字节序和AppId
        if (strlen($result) < 16){
            return "";
        }
        
        $content        = substr($result, 16, strlen($result));
        $len_list       = unpack("N", substr($content, 0, 4));
        $xml_len        = $len_list[1];
        $xml_content    = substr($content, 4, $xml_len);
        $from_appid     = substr($content, $xml_len + 4);   
        if ($from_appid != $appid){
            throw new Exception('appid不匹配',ErrorCode::$ValidateAppidError);
        }
        return array(0, $xml_content);
    }

    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    function getRandomStr() {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

}
