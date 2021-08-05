<?php

namespace xjryanse\wechat\WeApp;

use Exception;
/**
 * 对微信小程序用户加密数据的解密示例代码
 * 一般用于解密手机号码
 */
class WXBizDataCrypt {

    private $appid;
    private $sessionKey;

    /**
     * 构造函数
     * @param $appid string 小程序的appid
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     */
    public function __construct($appid, $sessionKey) {
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($encryptedData, $iv) {
        if (strlen($this->sessionKey) != 24) {
            throw new Exception('encodingAesKey 非法',ErrorCode::$IllegalAesKey);
        }
        $aesKey = base64_decode($this->sessionKey);
        if (strlen($iv) != 24) {
            throw new Exception('iv 非法',ErrorCode::$IllegalIv);
        }
        $aesIV      = base64_decode($iv);
        $aesCipher  = base64_decode($encryptedData);
        $result     = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj    = json_decode($result);
        if ($dataObj == NULL) {
            throw new Exception('aes 解密失败 $dataObj 为null',ErrorCode::$IllegalBuffer);
        }
        if ($dataObj->watermark->appid != $this->appid) {
            throw new Exception('aes 解密失败 appid不匹配',ErrorCode::$IllegalBuffer);
        }
        return json_decode($result,true);
    }
}
