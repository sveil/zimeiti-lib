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

namespace sveil\lib\rep\wechat\wepay;

use sveil\lib\exception\InvalidDecryptException;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\wechat\wepay\Bill;
use sveil\lib\rep\wechat\wepay\Order;
use sveil\lib\rep\wechat\wepay\Refund;
use sveil\lib\rep\wechat\wepay\Transfers;
use sveil\lib\rep\wechat\wepay\TransfersBank;
use sveil\lib\rep\WePay;

/**
 * Class Pay
 * WeChat payment merchants
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wepay
 */
class Pay extends WePay
{
    /**
     * Unified order
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function createOrder(array $options)
    {
        return Order::instance($this->config->get())->create($options);
    }

    /**
     * Credit card payment
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function createMicropay($options)
    {
        return Order::instance($this->config->get())->micropay($options);
    }

    /**
     * Create JsApi and H5 payment parameters
     * @param string $prepay_id Unified order prepayment code
     * @return array
     */
    public function createParamsForJsApi($prepay_id)
    {
        return Order::instance($this->config->get())->jsapiParams($prepay_id);
    }

    /**
     * Get APP payment parameters
     * @param string $prepay_id Unified order prepayment code
     * @return array
     */
    public function createParamsForApp($prepay_id)
    {
        return Order::instance($this->config->get())->appParams($prepay_id);
    }

    /**
     * Get QR code for payment rules
     * @param string $product_id Merchant-defined product id Or order number
     * @return string
     */
    public function createParamsForRuleQrc($product_id)
    {
        return Order::instance($this->config->get())->qrcParams($product_id);
    }

    /**
     * checking order
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function queryOrder(array $options)
    {
        return Order::instance($this->config->get())->query($options);
    }

    /**
     * Close order
     * @param string $out_trade_no Merchant order number
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function closeOrder($out_trade_no)
    {
        return Order::instance($this->config->get())->close($out_trade_no);
    }

    /**
     * Request a refund
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function createRefund(array $options)
    {
        return Refund::instance($this->config->get())->create($options);
    }

    /**
     * Check refund
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function queryRefund(array $options)
    {
        return Refund::instance($this->config->get())->query($options);
    }

    /**
     * Transaction protection
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function report(array $options)
    {
        return Order::instance($this->config->get())->report($options);
    }

    /**
     * Authorization code query openid
     * @param string $authCode Scanning the payment authorization code, the device reads the barcode or QR code information in the user's WeChat
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function queryAuthCode($authCode)
    {
        return Order::instance($this->config->get())->queryAuthCode($authCode);
    }

    /**
     * Download statement
     * @param array $options Mute parameter
     * @param null|string $outType Output type
     * @return bool|string
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function billDownload(array $options, $outType = null)
    {
        return Bill::instance($this->config->get())->download($options, $outType);
    }

    /**
     * Pull order evaluation data
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function billCommtent(array $options)
    {
        return Bill::instance($this->config->get())->comment($options);
    }

    /**
     * Enterprise payment to change
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function createTransfers(array $options)
    {
        return Transfers::instance($this->config->get())->create($options);
    }

    /**
     * Check the company's payment to change
     * @param string $partner_trade_no The merchant order number used by the merchant when calling the enterprise payment API
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function queryTransfers($partner_trade_no)
    {
        return Transfers::instance($this->config->get())->query($partner_trade_no);
    }

    /**
     * Corporate payment to bank card
     * @param array $options
     * @return array
     * @throws LocalCacheException
     * @throws InvalidDecryptException
     * @throws InvalidResponseException
     */
    public function createTransfersBank(array $options)
    {
        return TransfersBank::instance($this->config->get())->create($options);
    }

    /**
     * Merchant enterprise payment to bank card operation for results query
     * @param string $partner_trade_no Merchant order number, need to be unique
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function queryTransFresBank($partner_trade_no)
    {
        return TransfersBank::instance($this->config->get())->query($partner_trade_no);
    }
}
