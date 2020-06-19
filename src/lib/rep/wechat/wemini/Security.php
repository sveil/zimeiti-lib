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
 * Class Security
 * Applet content security
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wemini
 */
class Security extends WeChat
{
    /**
     * Check whether a picture contains illegal content
     * @param string $media The image file to be detected, the format supports PNG, JPEG, JPG, GIF,
     * and the image size does not exceed 750px x 1334px
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function imgSecCheck($media)
    {
        $url = 'https://api.weixin.qq.com/wxa/img_sec_check?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['media' => $media], true);
    }

    /**
     * Asynchronously verify whether the image / audio contains illegal content
     * @param string $media_url
     * @param string $media_type
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function mediaCheckAsync($media_url, $media_type)
    {
        $url = 'https://api.weixin.qq.com/wxa/media_check_async?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['media_url' => $media_url, 'media_type' => $media_type], true);
    }

    /**
     * Check whether a piece of text contains illegal content
     * @param string $content
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function msgSecCheck($content)
    {
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['content' => $content], true);
    }
}
