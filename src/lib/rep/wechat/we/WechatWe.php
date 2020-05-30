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

namespace sveil\lib\rep\wechat\we;

use sveil\lib\common\File;
use sveil\lib\common\We;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\service\JsonRpcClient;
use think\Exception;
use think\exception\PDOException;

/**
 * WeChat processing management
 *
 * Class WechatService
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\we
 *
 * ----- WeOpen for Open -----
 * @method \weopen\login login() static Login with WeChat
 * @method \weopen\service service() static Use other services
 *
 * ----- WeMini for Open -----
 * @method \wemini\code WeMiniCode() static Applet code management
 * @method \wemini\user WeMiniUser() static Applet user management
 * @method \wemini\basic WeMiniBasic() static Applet Basic Information
 * @method \wemini\domain WeMiniDomain() static Applet domain name management
 * @method \wemini\tester WeMiniTester() static Applet member management
 * @method \wemini\account WeMiniAccount() static Applet account management
 *
 * ----- ThinkService -----
 * @method mixed wechat() static Use WeChat tool
 */
class WechatWe extends We
{

    /**
     * Get WeChat payment configuration
     *
     * @param array|null $options
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function config($options = null)
    {

        if (empty($options)) {
            $options = [
                // Required parameters of WeChat function
                'appid'          => self::getAppid(),
                'token'          => sysconf('wechat_token'),
                'appsecret'      => sysconf('wechat_appsecret'),
                'encodingaeskey' => sysconf('wechat_encodingaeskey'),
                // Necessary parameters of WeChat payment
                'mch_id'         => sysconf('wechat_mch_id'),
                'mch_key'        => sysconf('wechat_mch_key'),
                'cache_path'     => env('runtime_path') . 'wechat' . DIRECTORY_SEPARATOR,
            ];
        }

        if (sysconf('wechat_mch_ssl_type') === 'p12') {
            $options['ssl_p12'] = self::_parseCertPath(sysconf('wechat_mch_ssl_p12'));
        } else {
            $options['ssl_key'] = self::_parseCertPath(sysconf('wechat_mch_ssl_key'));
            $options['ssl_cer'] = self::_parseCertPath(sysconf('wechat_mch_ssl_cer'));
        }

        return parent::config($options);
    }

    /**
     * Parsing the certification path
     *
     * @param string $path
     * @return mixed
     * @throws Exception
     */
    private static function _parseCertPath($path)
    {
        if (preg_match('|^[a-z0-9]{16,16}\/[a-z0-9]{16,16}\.|i', $path)) {
            return File::instance('local')->path($path, true);
        }
        return $path;
    }

    /**
     * Static magic loading method
     *
     * @param string $name Static class name
     * @param array $arguments Parameter set
     * @return mixed
     * @throws Exception
     * @throws PDOException
     */
    public static function __callStatic($name, $arguments)
    {

        $config = [];

        if (is_array($arguments) && count($arguments) > 0) {
            $option = array_shift($arguments);
            $config = is_array($option) ? $option : self::config();
        }

        if (in_array($name, ['wechat'])) {
            return self::instance(trim($name, '_'), 'WeChat', $config);
        } elseif (substr($name, 0, 6) === 'WeChat') {
            return self::instance(substr($name, 6), 'WeChat', $config);
        } elseif (substr($name, 0, 6) === 'WeMini') {
            return self::instance(substr($name, 6), 'WeMini', $config);
        } elseif (substr($name, 0, 5) === 'WePay') {
            return self::instance(substr($name, 5), 'WePay', $config);
        } elseif (substr($name, 0, 6) === 'AliPay') {
            return self::instance(substr($name, 6), 'AliPay', $config);
        } else {
            throw new Exception("class {$name} not found");
        }

    }

    /**
     * Interface object instantiation
     *
     * @param string $name Interface name
     * @param string $type Interface Type
     * @param array $config WeChat configuration
     * @return mixed
     * @throws Exception
     * @throws PDOException
     */
    public static function instance($name, $type = 'WeChat', $config = [])
    {

        if (self::getType() === 'api' || in_array($type, ['WePay', 'AliPay']) || "{$type}{$name}" === 'WeChatPay') {
            if (class_exists($class = "\\{$type}\\" . ucfirst(strtolower($name)))) {
                return new $class(empty($config) ? self::config() : $config);
            } else {
                throw new Exception("Class {$class} not found");
            }
        } else {
            set_time_limit(3600);
            list($appid, $appkey) = [sysconf('wechat_thr_appid'), sysconf('wechat_thr_appkey')];
            $token                = strtolower("{$name}-{$appid}-{$appkey}-{$type}");
            if (class_exists('Yar_Client')) {
                return new \Yar_Client(config('wechat.service_url') . "/service/api.client/yar/{$token}");
            } else {
                $location = config('wechat.service_url') . "/service/api.client/jsonrpc/{$token}";
                return JsonRpcClient::instance()->create($location);
            }
        }

    }

    /**
     * Get WeChat web page JSSDK
     *
     * @param string $url JS signature address
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     * @throws Exception
     * @throws PDOException
     */
    public static function getWebJssdkSign($url = null)
    {

        $url = is_null($url) ? request()->url(true) : $url;

        if (self::getType() === 'api') {
            return self::WeChatScript()->getJsSign($url);
        } else {
            return self::wechat()->jsSign($url);
        }

    }

    /**
     * Initial access authorization
     *
     * @param string $url URL of authorization page
     * @param integer $isfull Authorized WeChat mode
     * @param boolean $isRedirect Whether to jump
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     * @throws Exception
     * @throws PDOException
     */
    public static function getWebOauthInfo($url, $isfull = 0, $isRedirect = true)
    {

        $appid                   = self::getAppid();
        list($openid, $fansinfo) = [session("{$appid}_openid"), session("{$appid}_fansinfo")];

        if ((empty($isfull) && !empty($openid)) || (!empty($isfull) && !empty($openid) && !empty($fansinfo))) {
            empty($fansinfo) || FansService::set($fansinfo);
            return ['openid' => $openid, 'fansinfo' => $fansinfo];
        }

        if (self::getType() === 'api') {
            $wechat = self::WeChatOauth();
            if (request()->get('state') !== $appid) {
                $snsapi   = empty($isfull) ? 'snsapi_base' : 'snsapi_userinfo';
                $param    = (strpos($url, '?') !== false ? '&' : '?') . 'rcode=' . encode($url);
                $OauthUrl = $wechat->getOauthRedirect($url . $param, $appid, $snsapi);
                if ($isRedirect) {
                    redirect($OauthUrl, [], 301)->send();
                }

                exit("window.location.href='{$OauthUrl}'");
            }
            if (($token = $wechat->getOauthAccessToken()) && isset($token['openid'])) {
                session("{$appid}_openid", $openid = $token['openid']);
                if (empty($isfull) && request()->get('rcode')) {
                    redirect(decode(request()->get('rcode')), [], 301)->send();
                }
                session("{$appid}_fansinfo", $fansinfo = $wechat->getUserInfo($token['access_token'], $openid));
                empty($fansinfo) || FansService::set($fansinfo);
            }
            redirect(decode(request()->get('rcode')), [], 301)->send();
        } else {
            $result = self::wechat()->oauth(session_id(), $url, $isfull);
            session("{$appid}_openid", $openid = $result['openid']);
            session("{$appid}_fansinfo", $fansinfo = $result['fans']);
            if ((empty($isfull) && !empty($openid)) || (!empty($isfull) && !empty($openid) && !empty($fansinfo))) {
                empty($fansinfo) || FansService::set($fansinfo);
                return ['openid' => $openid, 'fansinfo' => $fansinfo];
            }
            if ($isRedirect && !empty($result['url'])) {
                redirect($result['url'], [], 301)->send();
            }
            exit("window.location.href='{$result['url']}'");
        }

    }

    /**
     * Get the current WeChat APPID
     *
     * @return bool|string
     * @throws Exception
     * @throws PDOException
     */
    public static function getAppid()
    {

        if (self::getType() === 'api') {
            return sysconf('wechat_appid');
        } else {
            return sysconf('wechat_thr_appid');
        }

    }

    /**
     * Get interface authorization mode
     *
     * @return string
     * @throws Exception
     * @throws PDOException
     */
    public static function getType()
    {

        $type = strtolower(sysconf('wechat_type'));

        if (in_array($type, ['api', 'thr'])) {
            return $type;
        }

        throw new Exception('请在后台配置微信对接授权模式');
    }

}
