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

namespace sveil\lib\rep\wechat;

use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WeChat;

/**
 * Class Mini
 * Applet management permission set
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat
 */
class Mini extends WeChat
{
    /**
     * 1. Get Applet associated with WeOpen
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getLinkWxamp()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/wxamplinkget?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, [], true);
    }

    /**
     * 2. Associated applets
     * @param string $miniAppid Applet appid
     * @param integer $notifyUsers Whether to send a template message to notify public fans
     * @param integer $showProfile Whether to display WeOpen homepage
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function linkWxamp($miniAppid, $notifyUsers = 1, $showProfile = 1)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/wxamplink?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, [
            'appid'        => $miniAppid,
            'notify_users' => $notifyUsers,
            'show_profile' => $showProfile,
        ]);
    }

    /**
     * 3. Remove the associated applet
     * @param string $miniAppid Applet appid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function unlinkWxamp($miniAppid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/wxampunlink?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['appid' => $miniAppid]);
    }

    /**
     * Usage calls the quick registration API to complete the registration
     * @param string $ticket WeOpen scanning authorization certificate (Carry when the scan page of WeOpen jumps back to Usage)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function fastRegister($ticket)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/account/fastregister?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['ticket' => $ticket]);
    }
}
