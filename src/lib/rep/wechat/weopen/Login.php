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

namespace sveil\lib\rep\wechat\weopen;

use sveil\lib\common\DataArray;
use sveil\lib\common\Tools;
use sveil\lib\exception\InvalidArgumentException;
use sveil\lib\exception\LocalCacheException;

/**
 * Class Login
 * Website application WeChat login
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\weopen
 */
class Login
{
    /**
     * Current configuration object
     * @var DataArray
     */
    protected $config;

    /**
     * Login constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->config = new DataArray($options);

        if (empty($options['appid'])) {
            throw new InvalidArgumentException("Missing Config -- [appid]");
        }

        if (empty($options['appsecret'])) {
            throw new InvalidArgumentException("Missing Config -- [appsecret]");
        }
    }

    /**
     * Step first: Request CODE
     * @param string $redirectUri Please use urlEncode to process the link
     * @return string
     */
    public function auth($redirectUri)
    {
        $appid       = $this->config->get('appid');
        $redirectUri = urlencode($redirectUri);

        return "https://open.weixin.qq.com/connect/qrconnect?appid={$appid}&redirect_uri={$redirectUri}&response_type=code&scope=snsapi_login&state={$appid}#wechat_redirect";
    }

    /**
     * Step second: Get access_token by code
     * @return mixed
     * @throws LocalCacheException
     */
    public function getAccessToken()
    {
        $appid  = $this->config->get('appid');
        $secret = $this->config->get('appsecret');
        $code   = isset($_GET['code']) ? $_GET['code'] : '';
        $url    = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";

        return json_decode(Tools::get($url));
    }

    /**
     * Refresh the validity period of AccessToken
     * @param string $refreshToken
     * @return array
     * @throws LocalCacheException
     */
    public function refreshToken($refreshToken)
    {
        $appid = $this->config->get('appid');
        $url   = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$appid}&grant_type=refresh_token&refresh_token={$refreshToken}";

        return json_decode(Tools::get($url));
    }

    /**
     * Check if the authorization certificate (access_token) is valid
     * @param string $accessToken Call voucher
     * @param string $openid Ordinary user ID, unique to the current developer account
     * @return array
     * @throws LocalCacheException
     */
    public function checkAccessToken($accessToken, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/auth?access_token={$accessToken}&openid={$openid}";

        return json_decode(Tools::get($url));
    }

    /**
     * Obtain user personal information (UnionID mechanism)
     * @param string $accessToken Call voucher
     * @param string $openid Ordinary user ID, unique to the current developer account
     * @return array
     * @throws LocalCacheException
     */
    public function getUserinfo($accessToken, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$accessToken}&openid={$openid}";

        return json_decode(Tools::get($url));
    }
}
