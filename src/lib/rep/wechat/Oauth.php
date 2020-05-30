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
 * WeChat web authorization
 *
 * Class Oauth
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Oauth extends WeChat
{

    /**
     * Oauth authorization jump interface
     *
     * @param string $redirect_url Authorized bounce address
     * @param string $state It will bring the state parameter after the redirect（Fill in the parameter value of a-zA-Z0-9, up to 128 bytes）
     * @param string $scope Authorization class type(Optional snsapi_base|snsapi_userinfo)
     * @return string
     */
    public function getOauthRedirect($redirect_url, $state = '', $scope = 'snsapi_base')
    {

        $appid        = $this->config->get('appid');
        $redirect_uri = urlencode($redirect_url);

        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
    }

    /**
     * Get AccessToken and openid through code
     *
     * @return bool|array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getOauthAccessToken()
    {

        $appid     = $this->config->get('appid');
        $appsecret = $this->config->get('appsecret');
        $code      = isset($_GET['code']) ? $_GET['code'] : '';
        $url       = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";

        return $this->httpGetForJson($url);
    }

    /**
     * Refresh AccessToken and renew
     *
     * @param string $refresh_token
     * @return bool|array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getOauthRefreshToken($refresh_token)
    {

        $appid = $this->config->get('appid');
        $url   = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$appid}&grant_type=refresh_token&refresh_token={$refresh_token}";

        return $this->httpGetForJson($url);
    }

    /**
     * Inspection authorization certificate（access_token）is it effective
     *
     * @param string $access_token Web page authorization interface calling credential,Note: This access_token is different from the basic supported access_token
     * @param string $openid User's unique ID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function checkOauthAccessToken($access_token, $openid)
    {

        $url = "https://api.weixin.qq.com/sns/auth?access_token={$access_token}&openid={$openid}";

        return $this->httpGetForJson($url);
    }

    /**
     * 拉取用户信息(需scope为 snsapi_userinfo)
     * @param string $access_token Web page authorization interface calling credential,Note: This access_token is different from the basic supported access_token
     * @param string $openid User's unique ID
     * @param string $lang Back to country language, zh_CN, zh_TW, en
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getUserInfo($access_token, $openid, $lang = 'zh_CN')
    {

        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang={$lang}";

        return $this->httpGetForJson($url);
    }

}
