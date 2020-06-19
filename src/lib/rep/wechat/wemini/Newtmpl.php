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

namespace sveil\lib\rep\wechat\wemini;

use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WeChat;

/**
 * Class Mini
 * WeOpen Applets subscription message support
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wemini
 */
class Newtmpl extends WeChat
{
    /**
     * Get the category of the applet account
     * @param array $data Category information list
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addCategory($data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/addcategory?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Get the category of the applet account
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCategory()
    {
        $url = 'https://api.weixin.qq.com/wxaapi/newtmpl/getcategory?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callGetApi($url);
    }

    /**
     * Delete the category of the applet account
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteCategory()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/deletecategory?access_token=TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, [], true);
    }

    /**
     * Get the public template title under the category of the account
     * @param string $ids Category id, multiple separated by commas
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getPubTemplateTitleList($ids)
    {
        $url = 'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatetitles?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['ids' => $ids, 'start' => '0', 'limit' => '30'], true);
    }

    /**
     * Get the keyword list under the template title
     * @param string $tid Template title id, available through the interface
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getPubTemplateKeyWordsById($tid)
    {
        $url = 'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatekeywords?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['tid' => $tid], true);
    }

    /**
     * Combine templates and add to personal template library under account
     * @param string $tid Template title id, which can be obtained through the interface, and can also be obtained by logging in
     * to the background of the applet
     * @param array $kidList A list of template keywords assembled by the developer himself. The keyword order can be freely
     * matched (for example, [3,5,4] or [4,5,3]). A maximum of 5 keywords and a minimum of 2 keyword combinations are supported.
     * @param string $sceneDesc Service scene description, within 15 words
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addTemplate($tid, array $kidList, $sceneDesc = '')
    {
        $url = 'https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['tid' => $tid, 'kidList' => $kidList, 'sceneDesc' => $sceneDesc], false);
    }

    /**
     * Get a list of personal templates under the current account
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTemplateList()
    {
        $url = 'https://api.weixin.qq.com/wxaapi/newtmpl/gettemplate?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, [], true);
    }

    /**
     * Delete personal templates under account
     * @param string $priTmplId Template id to delete
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delTemplate($priTmplId)
    {
        $url = 'https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['priTmplId' => $priTmplId], true);
    }

    /**
     * Send subscription message
     * @param array $data Array of message objects sent
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function send(array $data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }
}
