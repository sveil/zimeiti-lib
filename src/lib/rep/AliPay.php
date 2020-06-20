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
 * Abstract Class AliPay
 * Alipay payment base class
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep
 */
abstract class AliPay
{
    /**
     * Support configuration
     * @var DataArray
     */
    protected $config;

    /**
     * Current request data
     * @var DataArray
     */
    protected $options;

    /**
     * DzContent data
     * @var DataArray
     */
    protected $params;

    /**
     * Static cache
     * @var static
     */
    protected static $cache;

    /**
     * Normal request gateway
     * @var string
     */
    protected $gateway = 'https://openapi.alipay.com/gateway.do?charset=utf-8';

    /**
     * AliPay constructor
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->params = new DataArray([]);
        $this->config = new DataArray($options);

        if (empty($options['appid'])) {
            throw new InvalidArgumentException("Missing Config -- [appid]");
        }

        if (empty($options['public_key'])) {
            throw new InvalidArgumentException("Missing Config -- [public_key]");
        }

        if (empty($options['private_key'])) {
            throw new InvalidArgumentException("Missing Config -- [private_key]");
        }

        if (!empty($options['debug'])) {
            $this->gateway = 'https://openapi.alipaydev.com/gateway.do?charset=utf-8';
        }

        $this->options = new DataArray([
            'app_id'    => $this->config->get('appid'),
            'charset'   => empty($options['charset']) ? 'utf-8' : $options['charset'],
            'format'    => 'JSON',
            'version'   => '1.0',
            'sign_type' => empty($options['sign_type']) ? 'RSA2' : $options['sign_type'],
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        if (isset($options['notify_url']) && $options['notify_url'] !== '') {
            $this->options->set('notify_url', $options['notify_url']);
        }

        if (isset($options['return_url']) && $options['return_url'] !== '') {
            $this->options->set('return_url', $options['return_url']);
        }

        if (isset($options['app_auth_token']) && $options['app_auth_token'] !== '') {
            $this->options->set('app_auth_token', $options['app_auth_token']);
        }
    }

    /**
     * Statically create objects
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
     * Check Alipay order status
     * @param string $out_trade_no
     * @return array|boolean
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function query($out_trade_no = '')
    {
        $this->options->set('method', 'alipay.trade.query');

        return $this->getResult(['out_trade_no' => $out_trade_no]);
    }

    /**
     * Alipay order refund operation
     * @param array|string $options Refund parameters or refund merchant order number
     * @param null $refund_amount Refund amount
     * @return array|boolean
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function refund($options, $refund_amount = null)
    {
        if (!is_array($options)) {
            $options = ['out_trade_no' => $options, 'refund_amount' => $refund_amount];
        }

        $this->options->set('method', 'alipay.trade.refund');

        return $this->getResult($options);
    }

    /**
     * Close Alipay order in progress
     * @param array|string $options
     * @return array|boolean
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function close($options)
    {
        if (!is_array($options)) {
            $options = ['out_trade_no' => $options];
        }

        $this->options->set('method', 'alipay.trade.close');

        return $this->getResult($options);
    }

    /**
     * Get notification data
     * @param boolean $needSignType Whether the sign_type field is required
     * @return boolean|array
     * @throws InvalidResponseException
     */
    public function notify($needSignType = false)
    {
        $data = $_POST;

        if (empty($data) || empty($data['sign'])) {
            throw new InvalidResponseException('Illegal push request.', 0, $data);
        }

        $string  = $this->getSignContent($data, $needSignType);
        $content = wordwrap($this->config->get('public_key'), 64, "\n", true);
        $res     = "-----BEGIN PUBLIC KEY-----\n{$content}\n-----END PUBLIC KEY-----";

        if (openssl_verify($string, base64_decode($data['sign']), $res, OPENSSL_ALGO_SHA256) !== 1) {
            throw new InvalidResponseException('Data signature verification failed.', 0, $data);
        }

        return $data;
    }

    /**
     * Verify the data signature returned by the interface
     * @param array $data Notification data
     * @param null|string $sign Data signature
     * @return array|boolean
     * @throws InvalidResponseException
     */
    protected function verify($data, $sign)
    {
        $content = wordwrap($this->config->get('public_key'), 64, "\n", true);
        $res     = "-----BEGIN PUBLIC KEY-----\n{$content}\n-----END PUBLIC KEY-----";

        if ($this->options->get('sign_type') === 'RSA2') {
            if (openssl_verify(json_encode($data, 256), base64_decode($sign), $res, OPENSSL_ALGO_SHA256) !== 1) {
                throw new InvalidResponseException('Data signature verification failed.');
            }
        } else {
            if (openssl_verify(json_encode($data, 256), base64_decode($sign), $res, OPENSSL_ALGO_SHA1) !== 1) {
                throw new InvalidResponseException('Data signature verification failed.');
            }
        }

        return $data;
    }

    /**
     * Get data signature
     * @return string
     */
    protected function getSign()
    {
        $content = wordwrap($this->trimCert($this->config->get('private_key')), 64, "\n", true);
        $string  = "-----BEGIN RSA PRIVATE KEY-----\n{$content}\n-----END RSA PRIVATE KEY-----";

        if ($this->options->get('sign_type') === 'RSA2') {
            openssl_sign($this->getSignContent($this->options->get(), true), $sign, $string, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($this->getSignContent($this->options->get(), true), $sign, $string, OPENSSL_ALGO_SHA1);
        }

        return base64_encode($sign);
    }

    /**
     * Remove the content and blanks before and after the certificate
     * @param string $sign
     * @return string
     */
    protected function trimCert($sign)
    {
        // if (file_exists($sign)) $sign = file_get_contents($sign);
        return preg_replace(['/\s+/', '/\-{5}.*?\-{5}/'], '', $sign);
    }

    /**
     * Data signature processing
     * @param array $data Need to sign data
     * @param boolean $needSignType Whether the sign_type field is required
     * @return bool|string
     */
    private function getSignContent(array $data, $needSignType = false)
    {
        list($attrs) = [[], ksort($data)];

        if (isset($data['sign'])) {
            unset($data['sign']);
        }

        if (empty($needSignType)) {
            unset($data['sign_type']);
        }

        foreach ($data as $key => $value) {
            if ($value === '' || is_null($value)) {
                continue;
            }
            array_push($attrs, "{$key}={$value}");
        }

        return join('&', $attrs);
    }

    /**
     * Data packet generation and data signature
     * @param array $options
     */
    protected function applyData($options)
    {
        $this->options->set('biz_content', json_encode($this->params->merge($options), 256));
        $this->options->set('sign', $this->getSign());
    }

    /**
     * Request interface and verify access data
     * @param array $options
     * @return array|boolean
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    protected function getResult($options)
    {
        $this->applyData($options);
        $method = str_replace('.', '_', $this->options['method']) . '_response';
        $data   = json_decode(Tools::get($this->gateway, $this->options->get()), true);

        if (!isset($data[$method]['code']) || $data[$method]['code'] !== '10000') {
            throw new InvalidResponseException(
                "Error: " .
                (empty($data[$method]['code']) ? '' : "{$data[$method]['msg']} [{$data[$method]['code']}]\r\n") .
                (empty($data[$method]['sub_code']) ? '' : "{$data[$method]['sub_msg']} [{$data[$method]['sub_code']}]\r\n"),
                $data[$method]['code'], $data
            );
        }

        return $data[$method];
        // Remove the returned result signature check
        // return $this->verify($data[$method], $data['sign']);
    }

    /**
     * Generate payment HTML code
     * @return string
     */
    protected function buildPayHtml()
    {
        $html = "<form id='alipaysubmit' name='alipaysubmit' action='{$this->gateway}' method='post'>";

        foreach ($this->options->get() as $key => $value) {
            $value = str_replace("'", '&apos;', $value);
            $html .= "<input type='hidden' name='{$key}' value='{$value}'/>";
        }

        $html .= "<input type='submit' value='ok' style='display:none;'></form>";

        return "{$html}<script>document.forms['alipaysubmit'].submit();</script>";
    }

    /**
     * New: Extract the serial number from the certificate
     * @param string $sign
     * @return string
     */
    public function getCertSN($sign)
    {
        // if (file_exists($sign)) $sign = file_get_contents($sign);
        $ssl = openssl_x509_parse($sign);

        return md5($this->_arr2str(array_reverse($ssl['issuer'])) . $ssl['serialNumber']);
    }

    /**
     * New: Extract the root certificate serial number
     * @param string $sign
     * @return string|null
     */
    public function getRootCertSN($sign)
    {
        $sn = null;
        // if (file_exists($sign)) $sign = file_get_contents($sign);
        $array = explode("-----END CERTIFICATE-----", $sign);

        for ($i = 0; $i < count($array) - 1; $i++) {
            $ssl[$i] = openssl_x509_parse($array[$i] . "-----END CERTIFICATE-----");

            if (strpos($ssl[$i]['serialNumber'], '0x') === 0) {
                $ssl[$i]['serialNumber'] = $this->_hex2dec($ssl[$i]['serialNumber']);
            }
            if ($ssl[$i]['signatureTypeLN'] == "sha1WithRSAEncryption" || $ssl[$i]['signatureTypeLN'] == "sha256WithRSAEncryption") {
                if ($sn == null) {
                    $sn = md5($this->_arr2str(array_reverse($ssl[$i]['issuer'])) . $ssl[$i]['serialNumber']);
                } else {
                    $sn = $sn . "_" . md5($this->_arr2str(array_reverse($ssl[$i]['issuer'])) . $ssl[$i]['serialNumber']);
                }
            }
        }

        return $sn;
    }

    /**
     * New: Array to string
     * @param array $array
     * @return string
     */
    private function _arr2str($array)
    {
        $string = [];

        if ($array && is_array($array)) {
            foreach ($array as $key => $value) {
                $string[] = $key . '=' . $value;
            }
        }

        return implode(',', $string);
    }

    /**
     * New: 0x to high precision digital
     * @param string $hex
     * @return int|string
     */
    private function _hex2dec($hex)
    {
        list($dec, $len) = [0, strlen($hex)];

        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
        }

        return $dec;
    }

    /**
     * Application data manipulation
     * @param array $options
     * @return mixed
     */
    abstract public function apply($options);
}
