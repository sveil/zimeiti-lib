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
 * Class Wifi
 * Store WIFI management
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat
 */
class Wifi extends WeChat
{
    /**
     * Get a list of Wi-Fi stores
     * @param integer $pageindex Paginated subscripts, starting from 1 by default
     * @param integer $pagesize Number of pages, default is 10, maximum is 20
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getShopList($pageindex = 1, $pagesize = 2)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/shop/list?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['pageindex' => $pageindex, 'pagesize' => $pagesize]);
    }

    /**
     * Query Wi-Fi information of stores
     * @param integer $shop_id Store ID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getShopWifi($shop_id)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/shop/list?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id]);
    }

    /**
     * Modify store network information
     * @param integer $shop_id Store ID
     * @param string $old_ssid SSID of old wireless network equipment
     * @param string $ssid SSID of new wireless network equipment
     * @param string $password Password of wireless network equipment (Optional)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function upShopWifi($shop_id, $old_ssid, $ssid, $password = null)
    {
        $data                                   = ['shop_id' => $shop_id, 'old_ssid' => $old_ssid, 'ssid' => $ssid];
        is_null($password) || $data['password'] = $password;
        $url                                    = 'https://api.weixin.qq.com/bizwifi/shop/update?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Clear store network and equipment
     * @param integer $shop_id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function clearShopWifi($shop_id)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/shop/clean?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id]);
    }

    /**
     * Add cryptographic device
     * @param integer $shop_id Store ID
     * @param string $ssid Ssid of wireless network equipment
     * @param null|string $password Password of wireless network equipment
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addShopWifi($shop_id, $ssid, $password = null)
    {
        $data = ['shop_id' => $shop_id, 'ssid' => $ssid, 'password' => $password];
        $url  = 'https://api.weixin.qq.com/bizwifi/device/add?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Add portal device
     * @param integer $shop_id Store ID
     * @param string $ssid Ssid of wireless network equipment
     * @param bool $reset Reset secretkey，false-no reset，true-reset，default is false
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addShopPortal($shop_id, $ssid, $reset = false)
    {
        $data = ['shop_id' => $shop_id, 'ssid' => $ssid, 'reset' => $reset];
        $url  = 'https://api.weixin.qq.com/bizwifi/apportal/register?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query device
     * @param null|integer $shop_id Query by store id
     * @param null|integer $pageindex Paginated subscripts, starting from 1 by default
     * @param null|integer $pagesize Number of pages, default is 10, maximum is 20
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryShopWifi($shop_id = null, $pageindex = null, $pagesize = null)
    {
        $data                                     = [];
        is_null($pagesize) || $data['pagesize']   = $pagesize;
        is_null($pageindex) || $data['pageindex'] = $pageindex;
        is_null($shop_id) || $data['shop_id']     = $shop_id;
        $url                                      = 'https://api.weixin.qq.com/bizwifi/device/list?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Remove device
     * @param string $bssid The wireless mac address of the wireless network device to be deleted, Format colon separated,
     * Character length is 17, And lowercase letters, such as：00:1f:7a:ad:5c:a8
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delShopWifi($bssid)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/device/delete?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['bssid' => $bssid]);
    }

    /**
     * Get material QR code
     * @param integer $shop_id Store ID
     * @param string $ssid The wireless network name added to the store
     * @param integer $img_id Material Style Number: 0-QR code, Can be used to freely design promotional materials; 1-QR code material,
     * 155mm×215mm(Width × height), Can be posted directly
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getQrc($shop_id, $ssid, $img_id = 1)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/qrcode/get?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id, 'ssid' => $ssid, 'img_id' => $img_id]);
    }

    /**
     * Set up business homepage
     * @param integer $shop_id Store ID
     * @param integer $template_id Template ID, 0-Default template, 1-Custom URL
     * @param null|string $url Custom link, Required when template_id is 1
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setHomePage($shop_id, $template_id, $url = null)
    {
        $data                            = ['shop_id' => $shop_id, 'template_id' => $template_id];
        is_null($url) && $data['struct'] = ['url' => $url];
        $url                             = 'https://api.weixin.qq.com/bizwifi/homepage/set?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query merchant homepage
     * @param integer $shop_id Store id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getHomePage($shop_id)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/homepage/get?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id]);
    }

    /**
     * Set WeChat Homepage Welcome Message
     * @param integer $shop_id Store ID
     * @param integer $bar_type WeChat homepage welcome text content: 0-Welcome + WeOpen name; 1-Welcome + Store name;
     * 2-Connected + WeOpen name + WiFi；3-Connected + Store name + WiFi
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setBar($shop_id, $bar_type = 1)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/bar/set?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id, 'bar_type' => $bar_type]);
    }

    /**
     * Set up networking completion page
     * @param integer $shop_id Store ID
     * @param string $finishpage_url URL of the completed page
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setFinishPage($shop_id, $finishpage_url)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/finishpage/set?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id, 'finishpage_url' => $finishpage_url]);
    }

    /**
     * Wi-Fi statistics
     * @param string $begin_date Start date and timestamp, format yyyy-mm-dd，The maximum time is 30 days
     * @param string $end_date End date and timestamp, format yyyy-mm-dd，The maximum time is 30 days
     * @param integer $shop_id Search by store ID, -1 is the total statistics
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function staticList($begin_date, $end_date, $shop_id = -1)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/statistics/list?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id, 'begin_date' => $begin_date, 'end_date' => $end_date]);
    }

    /**
     * Set store card coupon information
     * @param integer $shop_id Store ID, can be set to 0, meaning all stores
     * @param integer $card_id Coupon ID
     * @param string $card_describe Card description, cannot exceed 18 characters
     * @param string $start_time Coupon delivery start time (in seconds)
     * @param string $end_time Coupon delivery end time (The unit is seconds), Note: The validity period of the card cannot be exceeded
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setCouponput($shop_id, $card_id, $card_describe, $start_time, $end_time)
    {
        $data = ['shop_id' => $shop_id, 'card_id' => $card_id, 'card_describe' => $card_describe, 'start_time' => $start_time, 'end_time' => $end_time];
        $url  = 'https://api.weixin.qq.com/bizwifi/couponput/set?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Check store card coupon information
     * @param integer $shop_id Store ID, can be set to 0, meaning all stores
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCouponput($shop_id)
    {
        $url = 'https://api.weixin.qq.com/bizwifi/couponput/get?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['shop_id' => $shop_id]);
    }
}
