<?php
namespace xjryanse\wechat\logic;

/**
 * 回复消息模板
 */
class XmlRespTemplate {
    //文本回复XML模板
    public static $text = '<xml>'
            . '<ToUserName><![CDATA[%s]]></ToUserName>'
            . '<FromUserName><![CDATA[%s]]></FromUserName>'
            . '<CreateTime>%s</CreateTime>'
            . '<MsgType><![CDATA[text]]></MsgType>'
            . '<Content><![CDATA[%s]]></Content>'
            . '</xml>';//文本回复XML模板
    //图片回复XML模板
    public static $image = '<xml>'
                . '<ToUserName><![CDATA[%s]]></ToUserName>'
                . '<FromUserName><![CDATA[%s]]></FromUserName>'
                . '<CreateTime>%s</CreateTime>'
                . '<MsgType><![CDATA[image]]></MsgType>'
                . '<Image><MediaId><![CDATA[%s]]></MediaId></Image>'
            . '</xml>';//图片回复XML模板
    //视频模板
    public static $video = '<xml>'
                . '<ToUserName><![CDATA[%s]]></ToUserName>'
                . '<FromUserName><![CDATA[%s]]></FromUserName>'
                . '<CreateTime>%s</CreateTime>'
                . '<MsgType><![CDATA[video]]></MsgType>
                    <Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                    </Video>'
            . '</xml>';//视频模板
    //音乐模板
    public static $music = '<xml>'
                . '<ToUserName><![CDATA[%s]]></ToUserName>'
                . '<FromUserName><![CDATA[%s]]></FromUserName>'
                . '<CreateTime>%s</CreateTime>'
                . '<MsgType><![CDATA[music]]></MsgType>'
                . '<Music>'
                    . '<Title><![CDATA[%s]]></Title>'
                    . '<Description><![CDATA[%s]]></Description>'
                    . '<MusicUrl><![CDATA[%s]]></MusicUrl>'
                    . '<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>'
                    . '<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>'
                . '</Music>'
            . '</xml>';//音乐模板
    //新闻主体
    public static $news = '<xml>'
                . '<ToUserName><![CDATA[%s]]></ToUserName>'
                . '<FromUserName><![CDATA[%s]]></FromUserName>'
                . '<CreateTime>%s</CreateTime>'
                . '<MsgType><![CDATA[news]]></MsgType>'
                . '<ArticleCount>%s</ArticleCount>'
                . '<Articles>%s</Articles>'
            . '</xml>';// 新闻主体
    //某个新闻模板
    public static $news_item = '<item>'
                . '<Title><![CDATA[%s]]></Title>'
                . '<Description><![CDATA[%s]]></Description>'
                . '<PicUrl><![CDATA[%s]]></PicUrl>'
                . '<Url><![CDATA[%s]]></Url>'
            . '</item>';//某个新闻模板
    
    //文本回复XML模板
    public static $diy = '<xml>'
            . '<ToUserName><![CDATA[%s]]></ToUserName>'
            . '<FromUserName><![CDATA[%s]]></FromUserName>'
            . '<CreateTime>%s</CreateTime>'
            . '<MsgType><![CDATA[%s]]></MsgType>'
            . '<Content><![CDATA[%s]]></Content>'
            . '</xml>';//文本回复XML模板
    
    /**
     * 回复文本消息
     * @param type $fromUser    发送用户
     * @param type $toUser      接收用户
     * @param type $content     发送内容
     * @return type
     */
    public static function textGetStr($fromUser,$toUser,$content)
    {
        $strTpl     = self::$text;
        return sprintf($strTpl, $toUser, $fromUser, time(), $content);
    }
    
    public static function diyStr($fromUser,$toUser,$msgType,$content)
    {
        $strTpl     = self::$diy;
        return sprintf($strTpl, $toUser, $fromUser, time(),$msgType, $content);
    }
}
