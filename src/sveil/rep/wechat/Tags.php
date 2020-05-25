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
 * Class Tags
 * User label management
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Tags extends WeChat
{
    /**
     * Get fan tag list
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTags()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Create a fan tag
     * @param string $name
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createTags($name)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['tag' => ['name' => $name]]);
    }

    /**
     * Update fans tags
     * @param integer $id Tag ID
     * @param string $name Label name
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateTags($id, $name)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['tag' => ['name' => $name, 'id' => $id]]);
    }

    /**
     * Remove fans tag
     * @param int $tagId
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteTags($tagId)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['tag' => ['id' => $tagId]]);
    }

    /**
     * Set labels for users in batches
     * @param array $openids
     * @param integer $tagId
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function batchTagging(array $openids, $tagId)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['openid_list' => $openids, 'tagid' => $tagId]);
    }

    /**
     * Unlabel users in batches
     * @param array $openids
     * @param integer $tagId
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function batchUntagging(array $openids, $tagId)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['openid_list' => $openids, 'tagid' => $tagId]);
    }

    /**
     * Get a list of tags on the user
     * @param string $openid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getUserTagId($openid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['openid' => $openid]);
    }
}
