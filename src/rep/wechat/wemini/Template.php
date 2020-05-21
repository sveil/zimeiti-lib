<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\wemini;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * WeOpen applet template message support
 *
 * Class Template
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Template extends WeChat
{

    /**
     * Get the title list of the applet template library
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTemplateLibraryList()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['offset' => '0', 'count' => '20'], true);
    }

    /**
     * Get the keyword library under a template title in the template library
     *
     * @param string $template_id The template title id can be obtained through the interface, or you can log in to
     * the background of the applet to view and obtain
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTemplateLibrary($template_id)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['id' => $template_id], true);
    }

    /**
     * Combine templates and add to personal template library under account
     *
     * @param string $template_id The template title id can be obtained through the interface, or you can log in to
     * the background of the applet to view and obtain
     * @param array $keyword_id_list A list of template keywords assembled by the developer himself. The keyword
     * order can be freely matched (such as [3,5,4] or [4,5,3]), and up to 10 keyword combinations
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addTemplate($template_id, array $keyword_id_list)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['id' => $template_id, 'keyword_id_list' => $keyword_id_list], true);
    }

    /**
     * Get a list of templates that already exist in the account
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTemplateList()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['offset' => '0', 'count' => '20'], true);
    }

    /**
     * Delete template message
     *
     * @param string $template_id Template id to delete
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delTemplate($template_id)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['template_id' => $template_id], true);
    }

    /**
     * Send template message
     *
     * @param array $data Array of message objects sent
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function send(array $data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

}
