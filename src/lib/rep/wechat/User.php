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
 * Class User
 * WeChat fans management
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat
 */
class User extends WeChat
{
    /**
     * Set user note name
     * @param string $openid
     * @param string $remark
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateMark($openid, $remark)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['openid' => $openid, 'remark' => $remark]);
    }

    /**
     * Get basic user information (Includes UnionID mechanism)
     * @param string $openid
     * @param string $lang
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getUserInfo($openid, $lang = 'zh_CN')
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid={$openid}&lang={$lang}";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Get basic user information in batches
     * @param array $openids
     * @param string $lang
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getBatchUserInfo(array $openids, $lang = 'zh_CN')
    {
        $url  = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=ACCESS_TOKEN';
        $data = ['user_list' => []];

        foreach ($openids as $openid) {
            $data['user_list'][] = ['openid' => $openid, 'lang' => $lang];
        }

        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Get user list
     * @param string $next_openid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getUserList($next_openid = '')
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&next_openid={$next_openid}";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Get the list of fans under the label
     * @param integer $tagid Tag ID
     * @param string $next_openid The first pull OPENID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getUserListByTag($tagid, $next_openid = '')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['tagid' => $tagid, 'next_openid' => $next_openid]);
    }

    /**
     * Obtain the blacklist of public accounts
     * @param string $begin_openid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getBlackList($begin_openid = '')
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/getblacklist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['begin_openid' => $begin_openid]);
    }

    /**
     * Blacklist users in batches
     * @param array $openids
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function batchBlackList(array $openids)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['openid_list' => $openids]);
    }

    /**
     * Bulk cancel black users
     * @param array $openids
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function batchUnblackList(array $openids)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchunblacklist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['openid_list' => $openids]);
    }
}
