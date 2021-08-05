<?php
namespace xjryanse\wechat\WeOpen;

use xjryanse\curl\Query;
use xjryanse\logic\Arrays2d;
use xjryanse\logic\Url;
use xjryanse\wechat\service\WechatWeOpenAppTemplateService;

/**
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/code_template/gettemplatedraftlist.html
 * 小程序模板接口
 */
class WeAppTpl extends Base
{
    use \xjryanse\wechat\WeOpen\traits\InstTrait;
    /**
     * 获取代码草稿列表
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/code_template/gettemplatedraftlist.html
     */
    public function getTemplateDraftList(){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/wxa/gettemplatedraftlist?access_token='.$componentAccessToken;
        $res        = Query::geturl($url);
        if($res['errmsg'] == 'ok'){
            $con    = [];
            $con[]  = ['we_open_id','=',$this->uuid];
            $con[]  = ['draft_id','>',0];
            //先删
            WechatWeOpenAppTemplateService::mainModel()->where($con)->delete();
            $saveData = $res['draft_list'];
            $saveDataCov = Arrays2d::keyReplace($saveData, ['create_time'=>'draft_create_time'],true);
            //再加
            $preData['we_open_id']  = $this->uuid;
            $preData['appid']       = $this->appId;
            WechatWeOpenAppTemplateService::saveAll( $saveDataCov , $preData);
        }
        return $res;
    }
    
    /**
     * 获取代码模板列表
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/code_template/gettemplatelist.html
     * @param type $templateType
     * @return type
     */
    public function getTemplateList($templateType = ''){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();
        $url = 'https://api.weixin.qq.com/wxa/gettemplatelist?access_token='.$componentAccessToken;
        if($templateType !== ''){
            $data['template_type']  = intval($templateType);
            $url = Url::addParam($url, $data);
        }
        $res = Query::geturl($url);
        if($res['errmsg'] == 'ok'){
            $con    = [];
            $con[]  = ['we_open_id','=',$this->uuid];
            $con[]  = ['template_id','>',0];
            if($templateType){
                $con[]  = ['template_type','=',$templateType];
            }
            //先删
            WechatWeOpenAppTemplateService::mainModel()->where($con)->delete();

            $saveData = $res['template_list'];
            $saveDataCov = Arrays2d::keyReplace($saveData, ['create_time'=>'draft_create_time'],true);
            //再加
            $preData['we_open_id']  = $this->uuid;
            $preData['appid']       = $this->appId;
            WechatWeOpenAppTemplateService::saveAll( $saveDataCov , $preData);
        }        

        return $res;
    }    
    /**
     * 删除指定代码模板
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/code_template/deletetemplate.html
     * @param type $templateId
     * @return type
     */
    public function deleteTemplate($templateId){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/wxa/deletetemplate?access_token='.$componentAccessToken;
        $data['template_id']      = $templateId;
        $res = Query::posturl($url, $data);
        return $res;
    }
    /**
     * 将草稿添加到代码模板库
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/code_template/addtotemplate.html
     * @param type $draftId         草稿id
     * @param type $templateType    模板类别
     */
    public function addToTemplate($draftId,$templateType=1){
        $componentAccessToken      = Token::getInstance( $this->uuid )->getApiComponentToken();        
        $url = 'https://api.weixin.qq.com/wxa/addtotemplate?access_token='.$componentAccessToken;
        $data['draft_id']           = $draftId;
        $data['template_type']      = $templateType;
        $res = Query::posturl($url, $data);
        return $res;
    }
}
