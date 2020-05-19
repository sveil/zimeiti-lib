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

namespace sveil\rep\wechat\weopen;

use sveil\common\DataArray;
use sveil\common\Tools;
use sveil\exception\InvalidArgumentException;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;

/**
 * Usage platform support
 *
 * Class Usage
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\weopen
 */
class Usage
{

    /**
     * Current configuration object
     * @var DataArray
     */
    protected $config;

    /**
     * Service constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {

        if (empty($options['component_token'])) {
            throw new InvalidArgumentException("Missing Config -- [component_token]");
        }

        if (empty($options['component_appid'])) {
            throw new InvalidArgumentException("Missing Config -- [component_appid]");
        }

        if (empty($options['component_appsecret'])) {
            throw new InvalidArgumentException("Missing Config -- [component_appsecret]");
        }

        if (empty($options['component_encodingaeskey'])) {
            throw new InvalidArgumentException("Missing Config -- [component_encodingaeskey]");
        }

        if (!empty($options['cache_path'])) {
            Tools::$cache_path = $options['cache_path'];
        }

        $this->config = new DataArray($options);
    }

    /**
     * Receive Tickets Pushed by WeOpen
     *
     * @return bool|array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getComonentTicket()
    {

        $receive = new Receive([
            'token'          => $this->config->get('component_token'),
            'appid'          => $this->config->get('component_appid'),
            'appsecret'      => $this->config->get('component_appsecret'),
            'encodingaeskey' => $this->config->get('component_encodingaeskey'),
            'cache_path'     => $this->config->get('cache_path'),
        ]);
        $data = $receive->getReceive();

        if (!empty($data['ComponentVerifyTicket'])) {
            Tools::setCache('component_verify_ticket', $data['ComponentVerifyTicket']);
        }

        return $data;
    }

    /**
     * Get or refresh service AccessToken
     *
     * @return bool|string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getComponentAccessToken()
    {
        $cache = 'wechat_component_access_token';

        if (($componentAccessToken = Tools::getCache($cache))) {
            return $componentAccessToken;
        }

        $data = [
            'component_appid'         => $this->config->get('component_appid'),
            'component_appsecret'     => $this->config->get('component_appsecret'),
            'component_verify_ticket' => Tools::getCache('component_verify_ticket'),
        ];
        $url    = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
        $result = $this->httpPostForJson($url, $data);

        if (empty($result['component_access_token'])) {
            throw new InvalidResponseException($result['errmsg'], $result['errcode'], $data);
        }

        Tools::setCache($cache, $result['component_access_token'], 7000);

        return $result['component_access_token'];
    }

    /**
     * Obtain the basic information of the authorized party's account
     *
     * @param string $authorizerAppid Appid of authorized WeOpen or Applet
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAuthorizerInfo($authorizerAppid)
    {

        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token={$componentAccessToken}";
        $data                 = [
            'authorizer_appid' => $authorizerAppid,
            'component_appid'  => $this->config->get('component_appid'),
        ];
        $result = $this->httpPostForJson($url, $data);

        if (empty($result['authorizer_info'])) {
            throw new InvalidResponseException($result['errmsg'], $result['errcode'], $data);
        }

        return $result['authorizer_info'];
    }

    /**
     * Confirm to accept the authorization of the high-level authority of a certain authority set by WeOpen
     *
     * @param string $authorizerAppid Appid of authorized WeOpen or Applet
     * @param string $funcscopeCategoryId Permission Set ID
     * @param string $confirmValue Whether to confirm (1. Confirm authorization, 2. Cancel confirmation)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setAuthorization($authorizerAppid, $funcscopeCategoryId, $confirmValue)
    {
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_confirm_authorization?component_access_token={$componentAccessToken}";
        return $this->httpPostForJson($url, [
            'confirm_value'         => $confirmValue,
            'authorizer_appid'      => $authorizerAppid,
            'funcscope_category_id' => $funcscopeCategoryId,
            'component_appid'       => $this->config->get('component_appid'),
        ]);
    }

    /**
     * Set options for authorized parties
     *
     * @param string $authorizerAppid Appid of authorized WeOpen or Applet
     * @param string $optionName Option name
     * @param string $optionValue Set option value
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setAuthorizerOption($authorizerAppid, $optionName, $optionValue)
    {
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_set_authorizer_option?component_access_token={$componentAccessToken}";
        return $this->httpPostForJson($url, [
            'option_name'      => $optionName,
            'option_value'     => $optionValue,
            'authorizer_appid' => $authorizerAppid,
            'component_appid'  => $this->config->get('component_appid'),
        ]);
    }

    /**
     * Get the option setting information of the authorized party
     *
     * @param string $authorizerAppid Appid of authorized WeOpen or Applet
     * @param string $optionName Option name
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAuthorizerOption($authorizerAppid, $optionName)
    {
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_option?component_access_token={$componentAccessToken}";
        return $this->httpPostForJson($url, [
            'option_name'      => $optionName,
            'authorizer_appid' => $authorizerAppid,
            'component_appid'  => $this->config->get('component_appid'),
        ]);
    }

    /**
     * Get pre-authentication code pre_auth_code
     *
     * @return string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getPreauthCode()
    {
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token={$componentAccessToken}";
        $result               = $this->httpPostForJson($url, ['component_appid' => $this->config->get('component_appid')]);
        if (empty($result['pre_auth_code'])) {
            throw new InvalidResponseException('GetPreauthCode Faild.', '0', $result);
        }
        return $result['pre_auth_code'];
    }

    /**
     * Obtain authorized bounce address
     *
     * @param string $redirectUri Callback URI
     * @param integer $authType Account type to be authorized
     * @return bool
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAuthRedirect($redirectUri, $authType = 3)
    {
        $redirectUri    = urlencode($redirectUri);
        $preAuthCode    = $this->getPreauthCode();
        $componentAppid = $this->config->get('component_appid');
        return "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid={$componentAppid}&pre_auth_code={$preAuthCode}&redirect_uri={$redirectUri}&auth_type={$authType}";
    }

    /**
     * Use authorization code in exchange for WeOpen or Applet interface call credentials and authorization information
     *
     * @param null $authCode Authorization code
     * @return bool|array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getQueryAuthorizerInfo($authCode = null)
    {
        if (is_null($authCode) && isset($_GET['auth_code'])) {
            $authCode = $_GET['auth_code'];
        }
        if (empty($authCode)) {
            return false;
        }
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token={$componentAccessToken}";
        $data                 = [
            'authorization_code' => $authCode,
            'component_appid'    => $this->config->get('component_appid'),
        ];
        $result = $this->httpPostForJson($url, $data);
        if (empty($result['authorization_info'])) {
            throw new InvalidResponseException($result['errmsg'], $result['errcode'], $data);
        }
        $authorizerAppid       = $result['authorization_info']['authorizer_appid'];
        $authorizerAccessToken = $result['authorization_info']['authorizer_access_token'];
        // Cache authorized WeOpen to access ACCESS_TOKEN
        Tools::setCache("{$authorizerAppid}_access_token", $authorizerAccessToken, 7000);
        return $result['authorization_info'];
    }

    /**
     * Get (refresh) the token of authorized WeOpen
     *
     * @param string $authorizerAppid Appid of authorized WeOpen or Applet
     * @param string $authorizerRefreshToken Authorization party's refresh token
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function refreshAccessToken($authorizerAppid, $authorizerRefreshToken)
    {
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token={$componentAccessToken}";
        $data                 = [
            'authorizer_appid'         => $authorizerAppid,
            'authorizer_refresh_token' => $authorizerRefreshToken,
            'component_appid'          => $this->config->get('component_appid'),
        ];
        $result = $this->httpPostForJson($url, $data);
        if (empty($result['authorizer_access_token'])) {
            throw new InvalidResponseException($result['errmsg'], $result['errcode'], $data);
        }
        // Cache authorized WeOpen to access ACCESS_TOKEN
        Tools::setCache("{$authorizerAppid}_access_token", $result['authorizer_access_token'], 7000);
        return $result;
    }

    /**
     * oauth authorization jump interface
     *
     * @param string $authorizerAppid Appid of authorized WeOpen or Applet
     * @param string $redirectUri Callback address
     * @param string $scope snsapi_userinfo|snsapi_base
     * @return string
     */
    public function getOauthRedirect($authorizerAppid, $redirectUri, $scope = 'snsapi_userinfo')
    {
        $redirectUri    = urlencode($redirectUri);
        $componentAppid = $this->config->get('component_appid');
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$authorizerAppid}&redirect_uri={$redirectUri}&response_type=code&scope={$scope}&state={$authorizerAppid}&component_appid={$componentAppid}#wechat_redirect";
    }

    /**
     * Get AccessToken by code
     *
     * @param string $authorizerAppid Appid of authorized WeOpen or Applet
     * @return bool|array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getOauthAccessToken($authorizerAppid)
    {
        if (empty($_GET['code'])) {
            return false;
        }
        $componentAppid       = $this->config->get('component_appid');
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid={$authorizerAppid}&code={$_GET['code']}&grant_type=authorization_code&component_appid={$componentAppid}&component_access_token={$componentAccessToken}";
        return $this->httpGetForJson($url);
    }

    /**
     * Get basic information of all currently authorized accounts
     *
     * @param integer $count Number of pulls, maximum 500
     * @param integer $offset Offset position / start position
     * @return array|bool
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAuthorizerList($count = 500, $offset = 0)
    {
        $componentAppid       = $this->config->get('component_appid');
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_list?component_access_token={$componentAccessToken}";
        return $this->httpPostForJson($url, ['count' => $count, 'offset' => $offset, 'component_appid' => $componentAppid]);
    }

    /**
     * Reset all API calls to Usage platforms
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function clearQuota()
    {
        $componentAppid       = $this->config->get('component_appid');
        $componentAccessToken = $this->getComponentAccessToken();
        $url                  = "https://api.weixin.qq.com/cgi-bin/component/clear_quota?component_access_token={$componentAccessToken}";
        return $this->httpPostForJson($url, ['component_appid' => $componentAppid]);
    }

    /**
     * Create a designated authorized WeOpen interface instance
     *
     * @param string $name Interface instance name to be loaded
     * @param string $authorizerAppid Appid of authorized WeOpen
     * @param string $type Load SDK type WeChat|WeMini
     * @return \wechat\card|\wechat\custom|\wechat\media|\wechat\menu|\wechat\oauth|\wechat\pay|\wechat\product|\wechat\qrcode|\wechat\receive|\wechat\scan|\wechat\script|\wechat\shake|\wechat\tags|\wechat\template|\wechat\user|\wechat\wifi
     */
    public function instance($name, $authorizerAppid, $type = 'WeChat')
    {
        $className = "{$type}\\" . ucfirst(strtolower($name));
        return new $className($this->getConfig($authorizerAppid));
    }

    /**
     * Obtain authorized WeOpen configuration parameters
     *
     * @param string $authorizerAppid Appid of authorized WeOpen
     * @return array
     */
    public function getConfig($authorizerAppid)
    {
        $config                   = $this->config->get();
        $config['appid']          = $authorizerAppid;
        $config['token']          = $this->config->get('component_token');
        $config['appsecret']      = $this->config->get('component_appsecret');
        $config['encodingaeskey'] = $this->config->get('component_encodingaeskey');
        return $config;
    }

    /**
     * Get interface data with POST and convert to array
     *
     * @param string $url interface address
     * @param array $data Request data
     * @param bool $buildToJson
     * @return array
     * @throws LocalCacheException
     */
    protected function httpPostForJson($url, array $data, $buildToJson = true)
    {
        return json_decode(Tools::post($url, $buildToJson ? Tools::arr2json($data) : $data), true);
    }

    /**
     * Get interface data with GET and convert to array
     *
     * @param string $url interface address
     * @return array
     * @throws LocalCacheException
     */
    protected function httpGetForJson($url)
    {
        return json_decode(Tools::get($url), true);
    }

}
