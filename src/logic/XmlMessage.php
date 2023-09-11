<?php
namespace xjryanse\wechat\logic;

/**
 * XML消息解析
 */
class XmlMessage {
    use \xjryanse\traits\InstTrait;
    /**
     * 原始消息字符串
     * @var type 
     */
    protected $messageRaw = '';
    
    protected $simpleXmlMessage ;
    /**
     * 设置消息
     */
    public function setMessage( string $message )
    {
        $this->messageRaw = $message;
        $this->simpleXmlMessage = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
    }
    /**
     * [20220519]获取消息
     * @return type
     */
    public function getMessage(){
        return json_decode(json_encode($this->simpleXmlMessage),JSON_UNESCAPED_UNICODE);
    }
    /*
     * 接收用户
     */
    public function toUserName()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->ToUserName : '';        
    }
    /*
     * 发送用户
     */
    public function fromUserName()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->FromUserName : '';        
    }
    /*
     * 消息时间
     */
    public function createTime()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->CreateTime : '';        
    }
    /*
     * 消息类型
     */
    public function msgType()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->MsgType : '';        
    }
    /*
     * 消息内容
     */
    public function content()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->Content : '';        
    }
    /*
     * 消息id
     */
    public function msgId()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->MsgId : '';        
    }
    /*
     * 消息加密
     */
    public function encrypt()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->Encrypt : '';        
    }
    /*
     * 事件名
     */
    public function event()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->Event : '';        
    }
    /*
     * 事件名
     */
    public function eventKey()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->EventKey : '';        
    }
    
    /*
     * 图片Url
     */
    public function picUrl()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->PicUrl : '';        
    }
    /*
     * 图片
     */
    public function image()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->Image : '';        
    }
    /*
     * 音乐
     */
    public function music()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->Music : '';        
    }
    /*
     * 20220519
     * 小程序来源
     */
    public function sessionForm()
    {
        return $this->simpleXmlMessage ? $this->simpleXmlMessage->SessionFrom : '';        
    }
}
