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

namespace sveil\lib\rep;

use sveil\lib\exception\InvalidArgumentException;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;

/**
 * Basic WeChat Payment
 *
 * Abstract Class WePay
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep
 */
abstract class WePay
{

    /**
     * Merchant configuration
     * @var DataArray
     */
    protected $config;

    /**
     * Current request data
     * @var DataArray
     */
    protected $params;

    /**
     * Static cache
     * @var static
     */
    protected static $cache;

    /**
     * WeChat constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {

        if (empty($options['appid'])) {
            throw new InvalidArgumentException("Missing Config -- [appid]");
        }

        if (empty($options['mch_id'])) {
            throw new InvalidArgumentException("Missing Config -- [mch_id]");
        }

        if (empty($options['mch_key'])) {
            throw new InvalidArgumentException("Missing Config -- [mch_key]");
        }

        if (!empty($options['cache_path'])) {
            Tools::$cache_path = $options['cache_path'];
        }

        $this->config = new DataArray($options);
        // Merchant basic parameters
        $this->params = new DataArray([
            'appid'     => $this->config->get('appid'),
            'mch_id'    => $this->config->get('mch_id'),
            'nonce_str' => Tools::createNoncestr(),
        ]);

        // Merchant parameter support
        if ($this->config->get('sub_appid')) {
            $this->params->set('sub_appid', $this->config->get('sub_appid'));
        }

        if ($this->config->get('sub_mch_id')) {
            $this->params->set('sub_mch_id', $this->config->get('sub_mch_id'));
        }

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
     * Get WeChat payment notification
     *
     * @return array
     * @throws InvalidResponseException
     */
    public function getNotify()
    {
        $data = Tools::xml2arr(file_get_contents('php://input'));
        if (isset($data['sign']) && $this->getPaySign($data) === $data['sign']) {
            return $data;
        }
        throw new InvalidResponseException('Invalid Notify.', '0');
    }

    /**
     * Get the reply content of WeChat payment notification
     *
     * @return string
     */
    public function getNotifySuccessReply()
    {
        return Tools::arr2xml(['return_code' => 'SUCCESS', 'return_msg' => 'OK']);
    }

    /**
     * Generate payment signature
     *
     * @param array $data Signed data
     * @param string $signType Types of signatures
     * @param string $buff Participate in the signature string prefix
     * @return string
     */
    public function getPaySign(array $data, $signType = 'MD5', $buff = '')
    {

        ksort($data);

        if (isset($data['sign'])) {
            unset($data['sign']);
        }

        foreach ($data as $k => $v) {
            $buff .= "{$k}={$v}&";
        }

        $buff .= ("key=" . $this->config->get('mch_key'));

        if (strtoupper($signType) === 'MD5') {
            return strtoupper(md5($buff));
        }

        return strtoupper(hash_hmac('SHA256', $buff, $this->config->get('mch_key')));
    }

    /**
     * Convert short links
     *
     * @param string $longUrl URL to be converted, original string for signature, URLencode for transmission
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function shortUrl($longUrl)
    {

        $url = 'https://api.mch.weixin.qq.com/tools/shorturl';

        return $this->callPostApi($url, ['long_url' => $longUrl]);
    }

    /**
     * Array directly to xml data output
     *
     * @param array $data
     * @param bool $isReturn
     * @return string
     */
    public function toXml(array $data, $isReturn = false)
    {

        $xml = Tools::arr2xml($data);

        if ($isReturn) {
            return $xml;
        }

        echo $xml;
    }

    /**
     * Request interface with Post
     *
     * @param string $url Request URL
     * @param array $data Interface parameters
     * @param bool $isCert Whether to use two-way certificate
     * @param string $signType Data signature type MD5|SHA256
     * @param bool $needSignType Whether to pass the signature type parameter
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    protected function callPostApi($url, array $data, $isCert = false, $signType = 'HMAC-SHA256', $needSignType = true)
    {

        $option = [];

        if ($isCert) {
            $option['ssl_p12'] = $this->config->get('ssl_p12');
            $option['ssl_cer'] = $this->config->get('ssl_cer');
            $option['ssl_key'] = $this->config->get('ssl_key');
            if (is_string($option['ssl_p12']) && file_exists($option['ssl_p12'])) {
                $content = file_get_contents($option['ssl_p12']);
                if (openssl_pkcs12_read($content, $certs, $this->config->get('mch_id'))) {
                    $option['ssl_key'] = Tools::pushFile(md5($certs['pkey']) . '.pem', $certs['pkey']);
                    $option['ssl_cer'] = Tools::pushFile(md5($certs['cert']) . '.pem', $certs['cert']);
                } else {
                    throw new InvalidArgumentException("P12 certificate does not match MCH_ID --- ssl_p12");
                }

            }
            if (empty($option['ssl_cer']) || !file_exists($option['ssl_cer'])) {
                throw new InvalidArgumentException("Missing Config -- ssl_cer", '0');
            }
            if (empty($option['ssl_key']) || !file_exists($option['ssl_key'])) {
                throw new InvalidArgumentException("Missing Config -- ssl_key", '0');
            }
        }

        $params = $this->params->merge($data);
        $needSignType && ($params['sign_type'] = strtoupper($signType));
        $params['sign'] = $this->getPaySign($params, $signType);
        $result         = Tools::xml2arr(Tools::post($url, Tools::arr2xml($params), $option));

        if ($result['return_code'] !== 'SUCCESS') {
            throw new InvalidResponseException($result['return_msg'], '0');
        }

        return $result;
    }

}
