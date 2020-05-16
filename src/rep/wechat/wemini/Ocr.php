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
 * Applet Ocr Service
 *
 * Class Ocr
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Ocr extends WeChat
{

    /**
     * This interface provides bank card OCR recognition based on applets
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function bankcard($data)
    {

        $url = 'https://api.weixin.qq.com/cv/ocr/bankcard?access_token=ACCESS_TOCKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * This interface provides OCR recognition of business licenses based on applets
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function businessLicense($data)
    {

        $url = 'https://api.weixin.qq.com/cv/ocr/bizlicense?access_token=ACCESS_TOCKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * This interface provides OCR recognition of driving licenses based on applets
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function driverLicense($data)
    {

        $url = 'https://api.weixin.qq.com/cv/ocr/drivinglicense?access_token=ACCESS_TOCKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * This interface provides OCR identification of ID card based on applet
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function idcard($data)
    {

        $url = 'https://api.weixin.qq.com/cv/ocr/idcard?access_token=ACCESS_TOCKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * This interface provides general printed OCR recognition based on applets
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function printedText($data)
    {

        $url = 'https://api.weixin.qq.com/cv/ocr/comm?access_token=ACCESS_TOCKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * This interface provides OCR recognition of driving licenses based on applets
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function vehicleLicense($data)
    {

        $url = 'https://api.weixin.qq.com/cv/ocr/driving?access_token=ACCESS_TOCKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

}
