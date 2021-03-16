<?php
namespace xjryanse\wechat\WePub\wxurl;
/**
 * 微信url模板
 */
class BaseUrlTpl
{
    //获取公众号全局access_token；access_token;
    public static $urlTpl   = [
        //获取公众号全局access_token    access_token
        'cgiBinToken'                       => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=SECRET',
        //获取微信服务器ip  server_ip
        'cgiBinGetcallbackip'               => 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=ACCESS_TOKEN',
        //【POST】网络监测  net_check
        'cgiBinCallbackCheck'               => 'https://api.weixin.qq.com/cgi-bin/callback/check?access_token=ACCESS_TOKEN',
        //获取用户授权code  oauth_code
        'connectOauth2Authorize'            => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect',
        //code换取用户授权access_token  oauth_access_token
        'snsOauth2AccessToken'              => 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code',
        //刷新用户授权的accesstoken refresh_access_token
        'snsOauth2RefreshToken'             => 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN',
        //拉取用户信息  get_user_info
        'snsUserInfo'                       => 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN',
        //检验授权凭证是否有效  is_access_token_valid
        'snsAuth'                           => 'https://api.weixin.qq.com/sns/auth?access_token=ACCESS_TOKEN&openid=OPENID',
        //获取jsapi_ticket  get_jsapi_ticket
        'cgiBinTicketGetticket'             => 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi',
        //生成二维码    qrcode_generate
        'cgiBinQrcodeCreate'                => 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=ACCESS_TOKEN',
        //通过ticket换二维码    showqrcode
        'cgiBinShowqrcode'                  => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=TICKET',    
        //公众号可通过本接口来获取帐号的关注者列表，关注者列表由一串OpenID（加密后的微信号，每个用户对每个公众号的OpenID是唯一的）组成。
        //一次拉取调用最多拉取10000个关注者的OpenID，可以通过多次拉取的方式来满足需求。 user_get
        'cgiBinUserGet'                     => 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&next_openid=NEXT_OPENID',
        //开发者拉取用户信息接口    cgiBin_user_info
        'cgiBinUserInfo'                    => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN',
        //开发者批量拉取用户信息接口    cgiBin_user_info_batchget
        'cgiBinUserInfoBatchget'            => 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=ACCESS_TOKEN',
        //发送模板消息接口  message_template_send
        'cgiBinMessageTemplateSend'         => 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN',
        //获取公众号的自动回复规则      get_current_autoreply_info
        'cgiBinGetCurrentAutoreplyInfo'     =>'https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=ACCESS_TOKEN',
        //【客服模块】
        //获取所有客服账号              customservice_getkflist
        'cgiBinCustomerserviceGetkflist'    =>'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=ACCESS_TOKEN',
        //获取素材列表                  material_batchget_material
        'cgiBinMaterialBatchgetMaterial'    => 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=ACCESS_TOKEN',
        //【自定义菜单】
        //查询接口                      get_current_selfmenu_info
        'cgiBinGetCurrentSelfmenuInfo'      => 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=ACCESS_TOKEN',
        //创建公众号菜单                menu_create
        'cgiBinMenuCreate'                  => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN',
        //客服消息发送                  custom_send
        'cgiBinMessageCustomSend'           =>" https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN",
        //获取永久素材                  material_get_material
        'cgiBinMaterialGetMaterial'         =>'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=ACCESS_TOKEN',
        //获取用户增减数据              getusersummary
        'datacubeGetusersummary'            =>'https://api.weixin.qq.com/datacube/getusersummary?access_token=ACCESS_TOKEN',
        //获取累计用户数据              getusercumulate
        'datacubeGetusercumulate'           =>'https://api.weixin.qq.com/datacube/getusercumulate?access_token=ACCESS_TOKEN',

        //批量查询卡券列表              cardBatchget
        'cardBatchget'                      =>'https://api.weixin.qq.com/card/batchget?access_token=ACCESS_TOKEN',
    ];
}
