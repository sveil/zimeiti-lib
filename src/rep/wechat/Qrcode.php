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
 * QR code management
 *
 * Class Qrcode
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Qrcode extends WeChat
{

    /**
     * Create QR code ticket
     *
     * @param string|integer $scene Scenes
     * @param int $expire_seconds Effective time
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create($scene, $expire_seconds = 0)
    {

        // QR code scene type
        if (is_integer($scene)) {
            $data = ['action_info' => ['scene' => ['scene_id' => $scene]]];
        } else {
            $data = ['action_info' => ['scene' => ['scene_str' => $scene]]];
        }

        // Temporary QR code
        if ($expire_seconds > 0) {
            $data['expire_seconds'] = $expire_seconds;
            $data['action_name']    = is_integer($scene) ? 'QR_SCENE' : 'QR_STR_SCENE';
        } else {
            // Permanent QR code
            $data['action_name'] = is_integer($scene) ? 'QR_LIMIT_SCENE' : 'QR_LIMIT_STR_SCENE';
        }

        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Exchange ticket for QR code
     *
     * @param string $ticket The obtained QR code ticket, With this ticket, you can exchange QR codes within the valid time.
     * @return string
     */
    public function url($ticket)
    {
        return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
    }

    /**
     * Long link to short link interface
     *
     * @param string $longUrl Long links that need to be converted
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function shortUrl($longUrl)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['action' => 'long2short', 'long_url' => $longUrl]);
    }

}
