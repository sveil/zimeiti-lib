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
 * Applet search
 *
 * Class Search
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Search extends WeChat
{

    /**
     * Submit applet page url and parameter information
     *
     * @param array $pages
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function submitPages($pages)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguideacct?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['pages' => $pages], true);
    }

}
