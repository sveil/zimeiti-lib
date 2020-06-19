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
 * Class Basic
 * Basic information settings
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wemini
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
