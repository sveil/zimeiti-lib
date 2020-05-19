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
 * Applet Operation and Maintenance Center
 *
 * Class Operation
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Operation extends WeChat
{

    /**
     * Real-time log query
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function realtimelogSearch($data)
    {

        $url = 'https://api.weixin.qq.com/wxaapi/userlog/userlog_search?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

}
