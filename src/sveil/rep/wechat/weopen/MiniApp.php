<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-lib
// | github：https://github.com/sveil/zimeiti-lib
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\weopen;

use sveil\common\Tools;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\wechat\weopen\Usage;

/**
 * Class MiniApp
 * Authorization support for WeOpen Applet
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\weopen
 */
class MiniApp extends Usage
{
    /**
     * code in exchange for session_key
     * @param string $appid AppID of the applet
     * @param string $code Code obtained at login
     * @return mixed
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function session($appid, $code)
    {
        $component_appid        = $this->config->get('component_appid');
        $component_access_token = $this->getComponentAccessToken();
        $url                    = "https://api.weixin.qq.com/sns/component/jscode2session?appid={$appid}&js_code={$code}&grant_type=authorization_code&component_appid={$component_appid}&component_access_token={$component_access_token}";

        return json_decode(Tools::get($url), true);
    }

    /**
     * 1. Registration process and interface description
     * @param string $authorizerAppid WeOpen appid
     * @param integer $copyWxVerify Whether to reuse the qualification of WeOpen for WeChat authentication
     * (1: apply for multiplexing qualification for WeChat authentication 0: not apply)
     * @param string $redirectUri After the user scan code authorization, the MP scan code page will jump to this address
     * (Note: 1. The link needs urlencode 2.Host must be the same as the domain name of the login authorization initiation
     * page filled in on the WeChat open platform of the third-party platform)
     * @return string
     */
    public function getCopyRegisterMiniUrl($authorizerAppid, $copyWxVerify, $redirectUri)
    {
        $redirectUri    = urlencode($redirectUri);
        $componentAppid = $this->config->get('component_appid');

        return "https://mp.weixin.qq.com/cgi-bin/fastregisterauth?appid={$authorizerAppid}&component_appid={$componentAppid}&copy_wx_verify={$copyWxVerify}&redirect_uri={$redirectUri}";
    }

    /**
     * 2.7.1 Jump from the third-party platform to the WeChat public platform authorization registration page
     * @param string $authorizerAppid WeOpen appid
     * @param string $redirectUri After filling in the new administrator information and clicking submit, it will jump to this address
     * @return string
     */
    public function getComponentreBindAdmin($authorizerAppid, $redirectUri)
    {
        $redirectUri    = urlencode($redirectUri);
        $componentAppid = $this->config->get('component_appid');

        return "https://mp.weixin.qq.com/wxopen/componentrebindadmin?appid={$authorizerAppid}&component_appid={$componentAppid}&redirect_uri={$redirectUri}";
    }

    /**
     * 1、Get all temporary code drafts in the draft box
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTemplateDraftList()
    {
        $component_access_token = $this->getComponentAccessToken();
        $url                    = "https://api.weixin.qq.com/wxa/gettemplatedraftlist?access_token={$component_access_token}";

        return $this->httpGetForJson($url);
    }

    /**
     * 2、Get all applet code templates in the code template library
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTemplateList()
    {
        $component_access_token = $this->getComponentAccessToken();
        $url                    = "https://api.weixin.qq.com/wxa/gettemplatelist?access_token={$component_access_token}";

        return $this->httpGetForJson($url);
    }

    /**
     * 3、Select the draft of the draft box as the applet code template
     * @param integer $draft_id Template ID, this field can be obtained through the "Get all temporary code drafts in the draft box" interface
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addToTemplate($draft_id)
    {
        $component_access_token = $this->getComponentAccessToken();
        $url                    = "https://api.weixin.qq.com/wxa/addtotemplate?access_token={$component_access_token}";

        return $this->httpPostForJson($url, ['draft_id' => $draft_id]);
    }

    /**
     * 4、Delete the specified applet code template
     * @param integer $template_id Template ID to be deleted
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteTemplate($template_id)
    {
        $component_access_token = $this->getComponentAccessToken();
        $url                    = "https://api.weixin.qq.com/wxa/deletetemplate?access_token={$component_access_token}";

        return $this->httpPostForJson($url, ['template_id' => $template_id]);
    }
}
