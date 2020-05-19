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

namespace sveil\rep\wechat;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Scan access management
 *
 * Class Scan
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Scan extends WeChat
{

    /**
     * Get business information
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getMerchantInfo()
    {

        $url = "https://api.weixin.qq.com/scan/merchantinfo/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Create product
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addProduct(array $data)
    {
        $url = "https://api.weixin.qq.com/scan/product/create?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, $data);
    }

    /**
     * Product release
     *
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @param string $status Set release status. on for submission review, off is to cancel the release
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function modProduct($keystandard, $keystr, $status = 'on')
    {

        $data = ['keystandard' => $keystandard, 'keystr' => $keystr, 'status' => $status];
        $url  = "https://api.weixin.qq.com/scan/product/modstatus?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Set tester whitelist
     *
     * @param array $openids Testers' openid list
     * @param array $usernames Tester's WeChat List
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setTestWhiteList($openids = [], $usernames = [])
    {

        $data = ['openid' => $openids, 'username' => $usernames];
        $url  = "https://api.weixin.qq.com/scan/product/modstatus?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Get product QR code
     *
     * @param string $keystandard
     * @param string $keystr
     * @param null|string $extinfo
     * @param integer $qrcode_size
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getQrc($keystandard, $keystr, $extinfo = null, $qrcode_size = 64)
    {

        $data                                 = ['keystandard' => $keystandard, 'keystr' => $keystr, 'qrcode_size' => $qrcode_size];
        is_null($extinfo) || $data['extinfo'] = $extinfo;
        $url                                  = "https://api.weixin.qq.com/scan/product/getqrcode?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query product information
     *
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getProductInfo($keystandard, $keystr)
    {

        $url = "https://api.weixin.qq.com/scan/product/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['keystandard' => $keystandard, 'keystr' => $keystr]);
    }

    /**
     * Query product information in bulk
     *
     * @param integer $offset 批量查询的起始位置，从0开始，包含该起始位置。
     * @param integer $limit 批量查询的数量。
     * @param string $status 支持按状态拉取。on为发布状态，off为未发布状态，check为审核中状态，reject为审核未通过状态，all为所有状态。
     * @param string $keystr 支持按部分编码内容拉取。填写该参数后，可将编码内容中包含所传参数的商品信息拉出。类似关键词搜索。
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getProductList($offset = 1, $limit = 10, $status = null, $keystr = null)
    {

        $data                               = ['offset' => $offset, 'limit' => $limit];
        is_null($status) || $data['status'] = $status;
        is_null($keystr) || $data['keystr'] = $keystr;
        $url                                = "https://api.weixin.qq.com/scan/product/getlist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Update product information
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateProduct(array $data)
    {

        $url = "https://api.weixin.qq.com/scan/product/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Clear product information
     *
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function clearProduct($keystandard, $keystr)
    {

        $url = "https://api.weixin.qq.com/scan/product/clear?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['keystandard' => $keystandard, 'keystr' => $keystr]);
    }

    /**
     * Check wxticket parameters
     *
     * @param string $ticket
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function checkTicket($ticket)
    {

        $url = "https://api.weixin.qq.com/scan/scanticket/check?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['ticket' => $ticket]);
    }

    /**
     * Clear scan code record
     *
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @param string $extinfo The extinfo passed in when calling "Get Product QR Code Interface" is the identification parameter
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function clearScanTicket($keystandard, $keystr, $extinfo)
    {

        $url = "https://api.weixin.qq.com/scan/scanticket/check?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['keystandard' => $keystandard, 'keystr' => $keystr, 'extinfo' => $extinfo]);
    }

}
