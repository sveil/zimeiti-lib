<?php

// +----------------------------------------------------------------------
// | Library for Sveil
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/boy12371/think-lib
// | github：https://github.com/boy12371/think-lib
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\wemini;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * WeChat open platform account management
 *
 * Class User
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class User extends WeChat
{

    /**
     * 1. Create an open platform account and bind WeOpen / Applet
     * @param string $appid Appid of authorized WeOpen or Applet
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create($appid)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/open/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $appid], true);
    }

    /**
     * 2. Bind WeOpen or Applet to the open platform account
     * @param string $appid Appid of authorized WeOpen or Applet
     * @param string $openAppid Open platform account appid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function bind($appid, $openAppid)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/open/bind?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $appid, 'open_appid' => $openAppid]);
    }

    /**
     * 3. Unbind WeOpen or Applet from the open platform account
     * @param string $appid Appid of authorized WeOpen or Applet
     * @param string $openAppid Open platform account appid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function unbind($appid, $openAppid)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/open/unbind?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $appid, 'open_appid' => $openAppid]);
    }

    /**
     * 3. Obtain the open platform account bound to WeOpen or Applet
     * @param string $appid Appid of authorized WeOpen or Applet
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function get($appid)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/open/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $appid]);
    }

}
