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

namespace sveil\lib\service;

use sveil\lib\Service;
use sveil\Db;
use sveil\Exception;
use sveil\exception\PDOException;

/**
 * Class Wechat
 * WeChat data service
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 *
 * @method \wechat\card WeChatCard($appid) static WeChat card and coupon management
 * @method \wechat\custom WeChatCustom($appid) static WeChat customer service news
 * @method \wechat\limit WeChatLimit($appid) static Interface call frequency limit
 * @method \wechat\media WeChatMedia($appid) static WeChat material management
 * @method \wechat\menu WeChatMenu($appid) static WeChat menu management
 * @method \wechat\oauth WeChatOauth($appid) static WeChat web authorization
 * @method \wechat\pay WeChatPay($appid) static WeChat payment merchants
 * @method \wechat\product WeChatProduct($appid) static WeChat store management
 * @method \wechat\qrcode WeChatQrcode($appid) static WeChat QR code management
 * @method \wechat\receive WeChatReceive($appid) static WeChat push management
 * @method \wechat\scan WeChatScan($appid) static WeChat Scan Access Management
 * @method \wechat\script WeChatScript($appid) static WeChat front-end support
 * @method \wechat\shake WeChatShake($appid) static Around WeChat
 * @method \wechat\tags WeChatTags($appid) static WeChat user tag management
 * @method \wechat\template WeChatTemplate($appid) static WeChat template message
 * @method \wechat\user WeChatUser($appid) static WeChat fans management
 * @method \wechat\wifi WeChatWifi($appid) static WeChat store WIFI management
 *
 * ----- WeMini -----
 * @method \wemini\account WeMiniAccount($appid) static Applet account management
 * @method \wemini\basic WeMiniBasic($appid) static Applet basic information settings
 * @method \wemini\code WeMiniCode($appid) static Applet code management
 * @method \wemini\domain WeMiniDomain($appid) static Applet domain management
 * @method \wemini\tester WeMinitester($appid) static Applet member management
 * @method \wemini\user WeMiniUser($appid) static Applet account management
 * --------------------
 * @method \wemini\crypt WeMiniCrypt($options = []) static Applet data encryption
 * @method \wemini\delivery WeMiniDelivery($options = []) static Applet instant delivery
 * @method \wemini\image WeMiniImage($options = []) static Applet image Processing
 * @method \wemini\logistics WeMiniLogistics($options = []) static Applet logistics assistant
 * @method \wemini\message WeMiniMessage($options = []) static Applet news
 * @method \wemini\ocr WeMiniOcr($options = []) static Applet ORC service
 * @method \wemini\plugs WeMiniPlugs($options = []) static Applet plugin management
 * @method \wemini\poi WeMiniPoi($options = []) static Applet address management
 * @method \wemini\qrcode WeMiniQrcode($options = []) static Applet QR code management
 * @method \wemini\security WeMiniSecurity($options = []) static Applet content security
 * @method \wemini\soter WeMiniSoter($options = []) static Applet biometric authentication
 * @method \wemini\template WeMiniTemplate($options = []) static Applet template message support
 * @method \wemini\total WeMiniTotal($options = []) static Applet data interface
 *
 * ----- WePay -----
 * @method \wepay\bill WePayBill($appid) static WeChat merchant bills and comments
 * @method \wepay\order WePayOrder($appid) static WeChat merchant order
 * @method \wepay\refund WePayRefund($appid) static WeChat merchant refund
 * @method \wepay\coupon WePayCoupon($appid) static WeChat merchant voucher
 * @method \wepay\redpack WePayRedpack($appid) static WeChat red package support
 * @method \wepay\transfers WePayTransfers($appid) static WeChat merchant transfer to change
 * @method \wepay\transfersbank WePayTransfersBank($appid) static WeChat merchant transfer to bank
 *
 * ----- WeOpen -----
 * @method \weopen\login login() static WeOpen login
 * @method \weopen\service service() static WeOpen service
 *
 * ----- ThinkService -----
 * @method mixed wechat() static WeOpen tools
 * @method mixed config() static WeOpen config
 */
class Wechat extends Service
{

    /**
     * Interface type mode
     * @var string
     */
    private static $type = 'WeChat';

    /**
     * Instance WeChat object
     *
     * @param string $name
     * @param string $appid Authorized WeOpen APPID
     * @param string $type SDK type
     * @return mixed
     * @throws Exception
     * @throws PDOException
     */
    public static function instance($name, $appid = '', $type = null)
    {

        $config = [
            'cache_path'               => env('runtime_path') . 'wechat',
            'component_appid'          => sysconf('component_appid'),
            'component_token'          => sysconf('component_token'),
            'component_appsecret'      => sysconf('component_appsecret'),
            'component_encodingaeskey' => sysconf('component_encodingaeskey'),
        ];
        // 注册授权公众号 AccessToken 处理
        $config['GetAccessTokenCallback'] = function ($authorizerAppid) use ($config) {
            $where = ['authorizer_appid' => $authorizerAppid];
            if (!($refreshToken = Db::name('WechatServiceConfig')->where($where)->value('authorizer_refresh_token'))) {
                throw new Exception('The WeChat information is not configured.', '404');
            }
            $open   = new \weopen\miniapp($config);
            $result = $open->refreshAccessToken($authorizerAppid, $refreshToken);
            if (empty($result['authorizer_access_token']) || empty($result['authorizer_refresh_token'])) {
                throw new Exception($result['errmsg'], '0');
            }
            Db::name('WechatServiceConfig')->where($where)->update([
                'authorizer_access_token'  => $result['authorizer_access_token'],
                'authorizer_refresh_token' => $result['authorizer_refresh_token'],
            ]);
            return $result['authorizer_access_token'];
        };
        $app = new \weopen\miniapp($config);

        if (in_array(strtolower($name), ['service', 'miniapp'])) {
            return $app;
        }

        if (!in_array($type, ['WeChat', 'WeMini'])) {
            $type = self::$type;
        }

        return $app->instance($name, $appid, $type);
    }

    /**
     * Static initialization object
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     * @throws PDOException
     */
    public static function __callStatic($name, $arguments)
    {

        if (substr($name, 0, 6) === 'WeMini') {
            self::$type = 'WeMini';
            $name       = substr($name, 6);
        } elseif (substr($name, 0, 6) === 'WeChat') {
            self::$type = 'WeChat';
            $name       = substr($name, 6);
        } elseif (substr($name, 0, 5) === 'WePay') {
            self::$type = 'WePay';
            $name       = substr($name, 5);
        }

        return self::instance($name, isset($arguments[0]) ? $arguments[0] : '', self::$type);
    }

}
