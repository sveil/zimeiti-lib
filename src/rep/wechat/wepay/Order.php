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

namespace sveil\rep\wechat\wepay;

use sveil\common\Tools;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WePay;

/**
 * WeChat merchant order
 *
 * Class Order
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wepay
 */
class Order extends WePay
{

    /**
     * Unified order
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create(array $options)
    {

        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

        return $this->callPostApi($url, $options, false, 'MD5');
    }

    /**
     * Credit card payment
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function micropay(array $options)
    {

        $url = 'https://api.mch.weixin.qq.com/pay/micropay';

        return $this->callPostApi($url, $options, false, 'MD5');
    }

    /**
     * checking order
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function query(array $options)
    {

        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';

        return $this->callPostApi($url, $options);
    }

    /**
     * Close order
     *
     * @param string $outTradeNo Merchant order number
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function close($outTradeNo)
    {

        $url = 'https://api.mch.weixin.qq.com/pay/closeorder';

        return $this->callPostApi($url, ['out_trade_no' => $outTradeNo]);
    }

    /**
     * Create JsApi and H5 payment parameters
     *
     * @param string $prepayId Unified order prepayment code
     * @return array
     */
    public function jsapiParams($prepayId)
    {

        $option              = [];
        $option["appId"]     = $this->config->get('appid');
        $option["timeStamp"] = (string) time();
        $option["nonceStr"]  = Tools::createNoncestr();
        $option["package"]   = "prepay_id={$prepayId}";
        $option["signType"]  = "MD5";
        $option["paySign"]   = $this->getPaySign($option, 'MD5');
        $option['timestamp'] = $option['timeStamp'];

        return $option;
    }

    /**
     * Get QR code for payment rules
     *
     * @param string $productId Merchant-defined product id or order number
     * @return string
     */
    public function qrcParams($productId)
    {

        $data = [
            'appid'      => $this->config->get('appid'),
            'mch_id'     => $this->config->get('mch_id'),
            'time_stamp' => (string) time(),
            'nonce_str'  => Tools::createNoncestr(),
            'product_id' => (string) $productId,
        ];
        $data['sign'] = $this->getPaySign($data, 'MD5');

        return "weixin://wxpay/bizpayurl?" . http_build_query($data);
    }

    /**
     * Obtain WeChat App Payment Secret Parameters
     *
     * @param string $prepayId Unified order prepayment code
     * @return array
     */
    public function appParams($prepayId)
    {

        $data = [
            'appid'     => $this->config->get('appid'),
            'partnerid' => $this->config->get('mch_id'),
            'prepayid'  => (string) $prepayId,
            'package'   => 'Sign=WXPay',
            'timestamp' => (string) time(),
            'noncestr'  => Tools::createNoncestr(),
        ];
        $data['sign'] = $this->getPaySign($data, 'MD5');

        return $data;
    }

    /**
     * Credit card payment Cancel the order
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function reverse(array $options)
    {

        $url = 'https://api.mch.weixin.qq.com/secapi/pay/reverse';

        return $this->callPostApi($url, $options, true);
    }

    /**
     * Credit card payment Authorization code query openid
     * @param string $authCode Scanning the payment authorization code, the device reads the barcode or QR code information in the user's WeChat
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryAuthCode($authCode)
    {

        $url = 'https://api.mch.weixin.qq.com/tools/authcodetoopenid';

        return $this->callPostApi($url, ['auth_code' => $authCode]);
    }

    /**
     * Credit card payment Transaction protection
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function report(array $options)
    {

        $url = 'https://api.mch.weixin.qq.com/payitil/report';

        return $this->callPostApi($url, $options);
    }

}
