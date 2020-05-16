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
 * Basic information settings
 *
 * Class Basic
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Basic extends WeChat
{

    /**
     * 1. Set the applet privacy settings (whether it can be searched)
     * @param integer $status 1 means not searchable, 0 means searchable
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function changeWxaSearchStatus($status)
    {

        $url = 'https://api.weixin.qq.com/wxa/changewxasearchstatus?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['status' => $status], true);
    }

    /**
     * 2. Query the current privacy settings of the applet (whether it can be searched)
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWxaSearchStatus()
    {

        $url = 'https://api.weixin.qq.com/wxa/getwxasearchstatus?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

}
