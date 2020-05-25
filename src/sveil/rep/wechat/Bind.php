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
 * Class Bind
 * WeChat open platform account management
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Bind extends WeChat
{
    /**
     * Create an open platform account and bind WeOpen
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/open/create?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $this->config->get('appid')]);
    }

    /**
     * Bind WeOpen to the open platform account
     * @param string $openidAppid Open platform account APPID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function link($openidAppid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/open/bind?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $this->config->get('appid'), 'open_appid' => $openidAppid]);
    }

    /**
     * Unbind WeOpen from the open platform account
     * @param string $openidAppid Open platform account APPID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function unlink($openidAppid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/open/unbind?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $this->config->get('appid'), 'open_appid' => $openidAppid]);
    }

    /**
     * Get the open platform account bound to WeOpen
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function get()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/open/get?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $this->config->get('appid')]);
    }
}
