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

namespace sveil\common;

use sveil\common\DataArray;
use sveil\exception\InvalidInstanceException;

/**
 * Class We
 * Load buffer
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 *
 * ----- AliPay ----
 * @method \alipay\app AliPayApp($options) static Alipay App Payment Gateway
 * @method \alipay\bill AliPayBill($options) static Alipay bill download
 * @method \alipay\pos AliPayPos($options) static Alipay credit card payment
 * @method \alipay\scan AliPayScan($options) static Alipay scan code payment
 * @method \alipay\transfer AliPayTransfer($options) static Alipay transfer to account
 * @method \alipay\wap AliPayWap($options) static Alipay mobile site payment
 * @method \alipay\web AliPayWeb($options) static Alipay website payment
 *
 * ----- WeChat -----
 * @method \wechat\card WeChatCard($options = []) static WeChat coupon management
 * @method \wechat\custom WeChatCustom($options = []) static WeChat customer service message
 * @method \wechat\limit WeChatLimit($options = []) static Interface call frequency limit
 * @method \wechat\media WeChatMedia($options = []) static WeChat material management
 * @method \wechat\menu WeChatMenu($options = []) static WeChat menu management
 * @method \wechat\oauth WeChatOauth($options = []) static WeChat website authorization
 * @method \wechat\pay WeChatPay($options = []) static WeChat payment merchants
 * @method \wechat\product WeChatProduct($options = []) static WeChat store management
 * @method \wechat\qrcode WeChatQrcode($options = []) static WeChat QR code management
 * @method \wechat\receive WeChatReceive($options = []) static WeChat push management
 * @method \wechat\scan WeChatScan($options = []) static WeChat Scan Access Management
 * @method \wechat\script WeChatScript($options = []) static WeChat front-end support
 * @method \wechat\shake WeChatShake($options = []) static Around WeChat
 * @method \wechat\tags WeChatTags($options = []) static WeChat user tag management
 * @method \wechat\template WeChatTemplate($options = []) static WeChat template message
 * @method \wechat\user WeChatUser($options = []) static WeChat fan management
 * @method \wechat\wifi WeChatWifi($options = []) static WeChat store WIFI management
 *
 * ----- WeMini -----
 * @method \wemini\crypt WeMiniCrypt($options = []) static Applet data encryption
 * @method \wemini\delivery WeMiniDelivery($options = []) static Wemini instant delivery
 * @method \wemini\guide WeMiniGuide($options = []) static Wemini shopping guide assistant
 * @method \wemini\image WeMiniImage($options = []) static Wemini Image Processing
 * @method \wemini\logistics WeMiniLogistics($options = []) static Wemini Logistics assistant
 * @method \wemini\message WeMiniMessage($options = []) static Wemini message
 * @method \wemini\newtmpl WeMiniNewtmpl($options = []) static Wemini Subscribe message
 * @method \wemini\ocr WeMiniOcr($options = []) static Wemini ORC service
 * @method \wemini\operation WeMiniOperation($options = []) static Wemini Operation and Maintenance Center
 * @method \wemini\plugs WeMiniPlugs($options = []) static Wemini Plugin management
 * @method \wemini\poi WeMiniPoi($options = []) static Wemini Address management
 * @method \wemini\qrcode WeMiniQrcode($options = []) static Wemini QR code management
 * @method \wemini\search WeMiniSearch($options = []) static Wemini search
 * @method \wemini\security WeMiniSecurity($options = []) static Wemini Content security
 * @method \wemini\soter WeMiniSoter($options = []) static Wemini Biometric authentication
 * @method \wemini\template WeMiniTemplate($options = []) static Wemini Template message support
 * @method \wemini\total WeMiniTotal($options = []) static Wemini Data interface
 *
 * ----- WePay -----
 * @method \wepay\bill WePayBill($options = []) static WeChat merchant bills and comments
 * @method \wepay\coupon WePayCoupon($options = []) static WeChat merchant vouchers
 * @method \wepay\order WePayOrder($options = []) static WeChat merchant order
 * @method \wepay\refund WePayRefund($options = []) static WeChat merchant refund
 * @method \wepay\redpack WePayRedpack($options = []) static WeChat red package support
 * @method \wepay\transfers WePayTransfers($options = []) static WeChat merchant money to change
 * @method \wepay\transfersbank WePayTransfersBank($options = []) static WeChat merchant money to bank
 */
class We
{
    /**
     * Define current version
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * Static configuration
     * @var DataArray
     */
    private static $config;

    /**
     * Set and get parameters
     * @param array $option
     * @return array
     */
    public static function config($option = null)
    {
        if (is_array($option)) {
            self::$config = new DataArray($option);
        }

        if (self::$config instanceof DataArray) {
            return self::$config->get();
        }

        return [];
    }

    /**
     * Static magic loading method
     * @param string $name Static class name
     * @param array $arguments Parameter set
     * @return mixed
     * @throws InvalidInstanceException
     */
    public static function __callStatic($name, $arguments)
    {
        if (substr($name, 0, 6) === 'WeChat') {
            $class = 'WeChat\\' . substr($name, 6);
        } elseif (substr($name, 0, 6) === 'WeMini') {
            $class = 'WeMini\\' . substr($name, 6);
        } elseif (substr($name, 0, 6) === 'AliPay') {
            $class = 'AliPay\\' . substr($name, 6);
        } elseif (substr($name, 0, 5) === 'WePay') {
            $class = 'WePay\\' . substr($name, 5);
        }

        if (!empty($class) && class_exists($class)) {
            $option = array_shift($arguments);
            $config = is_array($option) ? $option : self::$config->get();
            return new $class($config);
        }

        throw new InvalidInstanceException("class {$name} not found");
    }
}
