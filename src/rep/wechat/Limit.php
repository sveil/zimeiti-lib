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

namespace sveil\rep\wechat;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Interface call frequency limit
 *
 * Class Limit
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Limit extends WeChat
{

    /**
     * WeOpen call or Usage to help WeOpen call all API calls to WeOpen（Including third parties to help them call）count reset
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function clearQuota()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/clear_quota?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['appid' => $this->config->get('appid')]);
    }

    /**
     * Network detection
     *
     * @param string $action 执行的检测动作
     * @param string $operator 指定平台从某个运营商进行检测
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function ping($action = 'all', $operator = 'DEFAULT')
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/callback/check?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['action' => $action, 'check_operator' => $operator]);
    }

    /**
     * Get WeChat server IP address
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCallbackIp()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

}
