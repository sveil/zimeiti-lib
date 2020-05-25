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

namespace sveil\rep\wechat;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Class Template
 * Template message
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Template extends WeChat
{
    /**
     * Set industry
     * @param string $industry_id1 Industry ID one of WeOpen template message
     * @param string $industry_id2 Industry ID two of WeOpen template message
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setIndustry($industry_id1, $industry_id2)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['industry_id1' => $industry_id1, 'industry_id2' => $industry_id2]);
    }

    /**
     * Get set industry information
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getIndustry()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Get template ID
     * @param string $tpl_id Template ID, available in "TM **" and "OPENTMTM **" forms
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addTemplate($tpl_id)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['template_id_short' => $tpl_id]);
    }

    /**
     * Get a list of templates
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAllPrivateTemplate()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Delete template ID
     * @param string $tpl_id Template message ID under WeOpen
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delPrivateTemplate($tpl_id)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['template_id' => $tpl_id]);
    }

    /**
     * Send template message
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function send(array $data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }
}
