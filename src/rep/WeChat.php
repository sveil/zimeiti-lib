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

namespace sveil\rep;

use sveil\exception\InvalidArgumentException;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;

/**
 * WeChat Basic
 *
 * Abstract Class WeChat
 * @author Richard <richard@sveil.com>
 * @package sveil\rep
 */
abstract class WeChat
{

    /**
     * Current WeChat configuration
     * @var DataArray
     */
    public $config;

    /**
     * Visit AccessToken
     * @var string
     */
    public $access_token = '';

    /**
     * Current request method parameters
     * @var array
     */
    protected $currentMethod = [];

    /**
     * Current mode
     * @var bool
     */
    protected $isTry = false;

    /**
     * Static cache
     * @var static
     */
    protected static $cache;

    /**
     * Register substitute function
     * @var string
     */
    protected $GetAccessTokenCallback;

    /**
     * BasicWeChat constructor
     * @param array $options
     */
    public function __construct(array $options)
    {

        if (empty($options['appid'])) {
            throw new InvalidArgumentException("Missing Config -- [appid]");
        }

        if (empty($options['appsecret'])) {
            throw new InvalidArgumentException("Missing Config -- [appsecret]");
        }

        if (isset($options['GetAccessTokenCallback']) && is_callable($options['GetAccessTokenCallback'])) {
            $this->GetAccessTokenCallback = $options['GetAccessTokenCallback'];
        }

        if (!empty($options['cache_path'])) {
            Tools::$cache_path = $options['cache_path'];
        }

        $this->config = new DataArray($options);
    }

    /**
     * Statically create objects
     *
     * @param array $config
     * @return static
     */
    public static function instance(array $config)
    {

        $key = md5(get_called_class() . serialize($config));

        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        return self::$cache[$key] = new static($config);
    }

    /**
     * Get access accessToken
     *
     * @return string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAccessToken()
    {

        if (!empty($this->access_token)) {
            return $this->access_token;
        }

        $cache              = $this->config->get('appid') . '_access_token';
        $this->access_token = Tools::getCache($cache);

        if (!empty($this->access_token)) {
            return $this->access_token;
        }

        // Handle open platform authorized public account acquisition AccessToken
        if (!empty($this->GetAccessTokenCallback) && is_callable($this->GetAccessTokenCallback)) {
            $this->access_token = call_user_func_array($this->GetAccessTokenCallback, [$this->config->get('appid'), $this]);
            if (!empty($this->access_token)) {
                Tools::setCache($cache, $this->access_token, 7000);
            }
            return $this->access_token;
        }

        list($appid, $secret) = [$this->config->get('appid'), $this->config->get('appsecret')];
        $url                  = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $result               = Tools::json2arr(Tools::get($url));

        if (!empty($result['access_token'])) {
            Tools::setCache($cache, $result['access_token'], 7000);
        }

        return $this->access_token = $result['access_token'];
    }

    /**
     * Set the external interface AccessToken
     *
     * @param string $access_token
     * @throws LocalCacheException
     *
     * When users use their own cache driver, you can directly set AccessToekn after instantiating the object directly
     * - Maintain AccessToken uniformity when used in distributed projects
     * - After using this method, the user will ensure that the incoming AccessToekn is valid AccessToekn
     */
    public function setAccessToken($access_token)
    {

        if (!is_string($access_token)) {
            throw new InvalidArgumentException("Invalid AccessToken type, need string.");
        }

        $cache = $this->config->get('appid') . '_access_token';

        Tools::setCache($cache, $this->access_token = $access_token);

    }

    /**
     * Clean and delete AccessToken
     *
     * @return bool
     */
    public function delAccessToken()
    {

        $this->access_token = '';

        return Tools::delCache($this->config->get('appid') . '_access_token');
    }

    /**
     * Get interface data with GET and convert to array
     *
     * @param string $url interface address
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    protected function httpGetForJson($url)
    {

        try {
            return Tools::json2arr(Tools::get($url));
        } catch (InvalidResponseException $e) {
            if (isset($this->currentMethod['method']) && empty($this->isTry)) {
                if (in_array($e->getCode(), ['40014', '40001', '41001', '42001'])) {
                    $this->delAccessToken();
                    $this->isTry = true;
                    return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
                }
            }
            throw new InvalidResponseException($e->getMessage(), $e->getCode());
        }

    }

    /**
     * Get interface data with POST and convert to array
     *
     * @param string $url interface address
     * @param array $data Request data
     * @param bool $buildToJson
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    protected function httpPostForJson($url, array $data, $buildToJson = true)
    {

        try {
            return Tools::json2arr(Tools::post($url, $buildToJson ? Tools::arr2json($data) : $data));
        } catch (InvalidResponseException $e) {
            if (!$this->isTry && in_array($e->getCode(), ['40014', '40001', '41001', '42001'])) {
                [$this->delAccessToken(), $this->isTry = true];
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }
            throw new InvalidResponseException($e->getMessage(), $e->getCode());
        }

    }

    /**
     * Register the current request interface
     *
     * @param string $url interface address
     * @param string $method Current interface method
     * @param array $arguments Request parameters
     * @return mixed
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    protected function registerApi(&$url, $method, $arguments = [])
    {

        $this->currentMethod = ['method' => $method, 'arguments' => $arguments];

        if (empty($this->access_token)) {
            $this->access_token = $this->getAccessToken();
        }

        return $url = str_replace('ACCESS_TOKEN', $this->access_token, $url);
    }

    /**
     * Interface general POST request method
     *
     * @param string $url Interface URL
     * @param array $data POST submit interface parameters
     * @param bool $isBuildJson
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function callPostApi($url, array $data, $isBuildJson = true)
    {

        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data, $isBuildJson);
    }

    /**
     * Interface general GET request method
     *
     * @param string $url Interface URL
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function callGetApi($url)
    {

        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

}
