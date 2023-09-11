<?php
namespace xjryanse\wechat\WxPay\v2;

use xjryanse\logic\Debug;
use xjryanse\logic\Strings;
use xjryanse\wechat\model\WechatWxPayConfig;
use Exception;
/**
 * 
 * 微信支付API基本类
 * @author widyhu
 *
 */
abstract class Base {
    /**
     * 支付配置
     * @var type 
     */
    protected $payConf;
    
    protected $values = [];
    /**
     * 设置支付参数
     * @param WechatWxPayConfig $config
     */
    public function setConf(WechatWxPayConfig $config){
        $this->payConf = $config;
    }
    /**
     * 返回结果返回值
     * @return type
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * SDK版本号
     * @var string
     */
    public static $VERSION = "3.0.10";
    
    /**
     * 获取毫秒级别的时间戳
     */
    protected static function getMillisecond() {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }
    
    /**
     * 以post方式提交xml到对应的接口url
     * 
     * @param WxPayConfigInterface $config  配置对象
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws Exception
     */
    protected function postXmlCurl( $xml, $url, $useCert = false, $second = 30) {
        $ch = curl_init();
        $curlVersion = curl_version();
        $ua = "WXPaySDK/" . self::$VERSION . " (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlVersion['version'] . " "
                . $this->payConf['MerchantId'];

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        $proxyHost = "0.0.0.0";
        $proxyPort = 0;
        // $config->GetProxy($proxyHost, $proxyPort);
        //如果有配置代理这里就设置代理
        if ($proxyHost != "0.0.0.0" && $proxyPort != 0) {
            curl_setopt($ch, CURLOPT_PROXY, $proxyHost);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //严格校验
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下
            $sslCertPath = "";
            $sslKeyPath = "";
            $config->GetSSLCertPath($sslCertPath, $sslKeyPath);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $sslCertPath);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $sslKeyPath);
            Debug::debug('$sslCertPath',$sslCertPath);
            Debug::debug('$sslKeyPath',$sslKeyPath);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new Exception("curl出错，错误码:$error");
        }
    }    
    
    /**
     * 
     * 上报数据， 上报的时候将屏蔽所有异常流程
     * @param WxPayConfigInterface $config  配置对象
     * @param string $usrl
     * @param int $startTimeStamp
     * @param array $data
     */
    protected function reportCostTime( $url, $startTimeStamp, $data) {
        //如果不需要上报数据
        // $reportLevenl = $config->GetReportLevenl();
        $reportLevenl = $this->payConf['ReportLevenl'];
        if ($reportLevenl == 0) {
            return;
        }
        //如果仅失败上报
        if ($reportLevenl == 1 &&
                array_key_exists("return_code", $data) &&
                $data["return_code"] == "SUCCESS" &&
                array_key_exists("result_code", $data) &&
                $data["result_code"] == "SUCCESS") {
            return;
        }

        //上报逻辑
        $endTimeStamp = self::getMillisecond();
        $objInput = new WxPayReport();
        $objInput->SetInterface_url($url);
        $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
        //返回状态码
        if (array_key_exists("return_code", $data)) {
            $objInput->SetReturn_code($data["return_code"]);
        }
        //返回信息
        if (array_key_exists("return_msg", $data)) {
            $objInput->SetReturn_msg($data["return_msg"]);
        }
        //业务结果
        if (array_key_exists("result_code", $data)) {
            $objInput->SetResult_code($data["result_code"]);
        }
        //错误代码
        if (array_key_exists("err_code", $data)) {
            $objInput->SetErr_code($data["err_code"]);
        }
        //错误代码描述
        if (array_key_exists("err_code_des", $data)) {
            $objInput->SetErr_code_des($data["err_code_des"]);
        }
        //商户订单号
        if (array_key_exists("out_trade_no", $data)) {
            $objInput->SetOut_trade_no($data["out_trade_no"]);
        }
        //设备号
        if (array_key_exists("device_info", $data)) {
            $objInput->SetDevice_info($data["device_info"]);
        }

        try {
            self::report($config, $objInput);
        } catch (\Exception $e) {
            //不做任何处理
        }
    }    
    

    /**
     * 
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 直接输出xml
     * @param string $xml
     */
    public static function replyNotify($xml) {
        echo $xml;
    }    
    
    
    
    /**
     * 输出xml字符
     * @throws Exception
     * */
    public function toXml() {
        if (!is_array($this->values) || count($this->values) <= 0) {
            throw new Exception("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($this->values as $key => $val) {
            if ( is_numeric($val) || (is_string($val) && Strings::isJson($val)) ) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws Exception
     */
    public static function fromXml($xml) {
        if (!$xml) {
            throw new Exception("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function toUrlParams() {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 生成签名
     * @param WxPayConfigInterface $config  配置对象
     * @param bool $needSignType  是否需要补signtype
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function makeSign($needSignType = true) {
        $signType   = $this->payConf['SignType'];
        $key        = $this->payConf['Key'];
        if ($needSignType) {
            $this->values['sign_type'] = $signType;
        }

        // 签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        // 签名步骤二：在string后加入KEY
        // $string = $string . "&key=" . $config->GetKey();
        $string = $string . "&key=" . $key;
        // 签名步骤三：MD5加密或者HMAC-SHA256
        if ($signType == "MD5") {
            $string = md5($string);
        } else if ($signType == "HMAC-SHA256") {
            $string = hash_hmac("sha256", $string, $key);
        } else {
            throw new Exception("签名类型".$signType."不支持！");
        }

        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    
    
    /*******返回结果处理********/
    public function resultInit($xml) {
        // $obj = new self();
        // $obj->fromXml($xml);
        // 返回结果数组
        $resArr = $this->fromXml($xml);
        Debug::debug('调试打印【微信支付】返回对象', $resArr);
        //失败则直接返回失败
        if ($resArr['return_code'] != 'SUCCESS') {
            foreach ($resArr as $key => $value) {
                #除了return_code和return_msg之外其他的参数存在，则报错
                if ($key != "return_code" && $key != "return_msg") {
                    throw new Exception("输入数据存在异常！");
                }
            }
        }
        // 20221119：忽略了校验环节
        return $resArr;
    }
    
    /**
     * @param WxPayConfigInterface $config  配置对象
     * 检测签名
     */
    public function checkSign() {
        if (!array_key_exists('sign', $this->values)) {
            throw new Exception("无签名信息！");
        }

        $sign = $this->makeSign(false);
        if ($this->values['sign'] == $sign) {
            //签名正确
            return true;
        }
        throw new Exception("签名错误！");
    }
}
