<?php

// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-lib
// | github：https://github.com/sveil/zimeiti-lib
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\wemini;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * WeChat Applet Plugin Management
 *
 * Class Plugs
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Plugs extends WeChat
{

    /**
     * 1. Apply to use the plugin
     *
     * @param string $plugin_appid 插件appid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function apply($plugin_appid)
    {

        $url = 'https://api.weixin.qq.com/wxa/plugin?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['action' => 'apply', 'plugin_appid' => $plugin_appid], true);
    }

    /**
     * 2. Query the added plugin
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getList()
    {

        $url = 'https://api.weixin.qq.com/wxa/plugin?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['action' => 'list'], true);
    }

    /**
     * 3. Delete the added plugin
     *
     * @param string $plugin_appid Plugin appid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function unbind($plugin_appid)
    {

        $url = 'https://api.weixin.qq.com/wxa/plugin?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['action' => 'unbind', 'plugin_appid' => $plugin_appid], true);
    }

    /**
     * 4. Get all current plug-in users
     * Modify the status of the plug-in application
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function devplugin($data)
    {

        $url = 'https://api.weixin.qq.com/wxa/devplugin?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * 4. Get all current plug-in users（Used by plugin developers）
     * @param integer $page 拉取第page页的数据
     * @param integer $num 表示每页num条记录
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function devApplyList($page = 1, $num = 10)
    {

        $url = 'https://api.weixin.qq.com/wxa/plugin?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $data = ['action' => 'dev_apply_list', 'page' => $page, 'num' => $num];

        return $this->callPostApi($url, $data, true);
    }

    /**
     * 5. Modify the status of the plug-in application（Used by plugin developers）
     * @param string $action dev_agree：Agree to apply；dev_refuse：Reject application；dev_delete：Delete rejected applicants
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function devAgree($action = 'dev_agree')
    {

        $url = 'https://api.weixin.qq.com/wxa/plugin?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['action' => $action], true);
    }

}
