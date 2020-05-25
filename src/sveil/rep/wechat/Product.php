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
 * Class Product
 * Store management
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Product extends WeChat
{
    /**
     * Submit review / unpublish product
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @param string $status Set release status. on for submission review, off is to cancel the release.
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function modStatus($keystandard, $keystr, $status = 'on')
    {
        $data = ['keystandard' => $keystandard, 'keystr' => $keystr, 'status' => $status];
        $url  = "https://api.weixin.qq.com/scan/product/modstatus?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Set tester whitelist
     * @param array $openids Testers' openid list
     * @param array $usernames Tester's WeChat List
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setTestWhiteList(array $openids = [], array $usernames = [])
    {
        $data = ['openid' => $openids, 'username' => $usernames];
        $url  = "https://api.weixin.qq.com/scan/testwhitelist/set?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Get product QR code
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @param integer $qrcode_size QR code size(Integer), The value represents the number of pixels in the side length, The default value is 100.
     * @param array $extinfo Customized by merchant, It is recommended to use only uppercase and lowercase letters,
     * numbers and -_ (). * These 6 commonly used characters.
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getQrcode($keystandard, $keystr, $qrcode_size, $extinfo = [])
    {
        $data                               = ['keystandard' => $keystandard, 'keystr' => $keystr, 'qrcode_size' => $qrcode_size];
        empty($extinfo) || $data['extinfo'] = $extinfo;
        $url                                = "https://api.weixin.qq.com/scan/product/getqrcode?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query product information
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getProduct($keystandard, $keystr)
    {
        $data                               = ['keystandard' => $keystandard, 'keystr' => $keystr];
        empty($extinfo) || $data['extinfo'] = $extinfo;
        $url                                = "https://api.weixin.qq.com/scan/product/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query product information in bulk
     * @param integer $offset The starting position of the batch query, starting from 0, including the starting position
     * @param integer $limit Number of batch queries
     * @param null|string $status Support pull by status. on is released, off is unreleased, check is under review,
     * reject is the status of unsuccessful review, all for all states
     * @param string $keystr Supports pulling content based on partial encoding, After filling in this parameter,
     * you can pull out the commodity information containing the passed parameter in the encoded content. Similar keyword search.
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getProductList($offset, $limit = 10, $status = null, $keystr = '')
    {
        $data                               = ['offset' => $offset, 'limit' => $limit];
        is_null($status) || $data['status'] = $status;
        empty($keystr) || $data['keystr']   = $keystr;
        $url                                = "https://api.weixin.qq.com/scan/product/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Update product information
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
     * @param string $ticket
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function scanTicketCheck($ticket)
    {
        $url = "https://api.weixin.qq.com/scan/scanticket/check?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['ticket' => $ticket]);
    }

    /**
     * Clear scan code record
     * @param string $keystandard Commodity coding standard
     * @param string $keystr Commodity coding content
     * @param string $extinfo The extinfo passed in when calling "Get Product QR Code Interface" is the identification parameter
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function clearScanticket($keystandard, $keystr, $extinfo)
    {
        $data = ['keystandard' => $keystandard, 'keystr' => $keystr, 'extinfo' => $extinfo];
        $url  = "https://api.weixin.qq.com/scan/scanticket/check?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }
}
