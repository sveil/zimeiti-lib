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

namespace sveil\rep\wechat\wemini;

use sveil\common\Tools;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Class Qrcode
 * WeChat Applet QR Code Management
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Qrcode extends WeChat
{
    /**
     * Get applet code (permanently valid)
     * Interface A: Applicable to business scenarios that require a small number of codes
     * @param string $path Cannot be empty, the maximum length is 128 bytes
     * @param integer $width QR code width
     * @param bool $auto_color Automatically configure the line color, if the color is still black,
     * it is not recommended to configure the main color
     * @param array $line_color auto_color active when false
     * @param boolean $is_hyaline Do you need a transparent background
     * @param null|string $outType Output type
     * @return array|string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createMiniPath($path, $width = 430, $auto_color = false, $line_color = ["r" => "0", "g" => "0", "b" => "0"], $is_hyaline = true, $outType = null)
    {
        $url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $data   = ['path' => $path, 'width' => $width, 'auto_color' => $auto_color, 'line_color' => $line_color, 'is_hyaline' => $is_hyaline];
        $result = Tools::post($url, Tools::arr2json($data));

        if (is_array($json = json_decode($result, true))) {
            if (!$this->isTry && isset($json['errcode']) && in_array($json['errcode'], ['40014', '40001', '41001', '42001'])) {
                [$this->delAccessToken(), $this->isTry = true];
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }

            return Tools::json2arr($result);
        }

        return is_null($outType) ? $result : $outType($result);
    }

    /**
     * Get applet code (permanently valid)
     * Interface B: suitable for business scenarios where the number of codes is extremely large
     * @param string $scene Maximum 32 visible characters, only supports numbers
     * @param string $page Must be the page where the published applet exists
     * @param integer $width QR code width
     * @param bool $auto_color Automatically configure the line color, if the color is still black,
     * it is not recommended to configure the main color
     * @param array $line_color auto_color active when false
     * @param boolean $is_hyaline Do you need a transparent background
     * @param null|string $outType Output type
     * @return array|string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createMiniScene($scene, $page, $width = 430, $auto_color = false, $line_color = ["r" => "0", "g" => "0", "b" => "0"], $is_hyaline = true, $outType = null)
    {
        $url  = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=ACCESS_TOKEN';
        $data = ['scene' => $scene, 'width' => $width, 'auto_color' => $auto_color, 'page' => $page, 'line_color' => $line_color, 'is_hyaline' => $is_hyaline];
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $result = Tools::post($url, Tools::arr2json($data));

        if (is_array($json = json_decode($result, true))) {
            if (!$this->isTry && isset($json['errcode']) && in_array($json['errcode'], ['40014', '40001', '41001', '42001'])) {
                [$this->delAccessToken(), $this->isTry = true];
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }

            return Tools::json2arr($result);
        }

        return is_null($outType) ? $result : $outType($result);
    }

    /**
     * Get applet code (permanently valid)
     * Interface C: suitable for business scenarios where a small number of codes are required
     * @param string $path Cannot be empty, the maximum length is 128 bytes
     * @param integer $width QR code width
     * @param null|string $outType Output type
     * @return array|string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createDefault($path, $width = 430, $outType = null)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $result = Tools::post($url, Tools::arr2json(['path' => $path, 'width' => $width]));

        if (is_array($json = json_decode($result, true))) {
            if (!$this->isTry && isset($json['errcode']) && in_array($json['errcode'], ['40014', '40001', '41001', '42001'])) {
                [$this->delAccessToken(), $this->isTry = true];
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }

            return Tools::json2arr($result);
        }

        return is_null($outType) ? $result : $outType($result);
    }
}
