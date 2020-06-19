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

namespace sveil\lib\common;

/**
 * Class Http
 * CURL Data Request Manager
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\common
 */
class Http
{
    /**
     * Simulate network requests with get
     * @param string $url HTTP request URL
     * @param array $query GET request parameters
     * @param array $options CURL parameters
     * @return boolean|string
     */
    public static function get($url, $query = [], $options = [])
    {
        $options['query'] = $query;

        return self::request('get', $url, $options);
    }

    /**
     * Simulate network requests with post
     * @param string $url HTTP request URL
     * @param array $data POST request parameters
     * @param array $options CURL parameters
     * @return boolean|string
     */
    public static function post($url, $data = [], $options = [])
    {
        $options['data'] = $data;

        return self::request('post', $url, $options);
    }

    /**
     * CURL simulate network request
     * @param string $method Request method
     * @param string $url Request URL
     * @param array $options Request parameters[headers,data]
     * @return boolean|string
     */
    public static function request($method, $url, $options = [])
    {
        $curl = curl_init();

        // GET parameter settings
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . http_build_query($options['query']);
        }

        // Browser proxy settings
        curl_setopt($curl, CURLOPT_USERAGENT, self::getUserAgent());

        // CURL header information settings
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }

        // Cookie information settings
        if (!empty($options['cookie'])) {
            curl_setopt($curl, CURLOPT_COOKIE, $options['cookie']);
        }

        if (!empty($options['cookie_file'])) {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $options['cookie_file']);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $options['cookie_file']);
        }

        // POST data settings
        if (strtolower($method) === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, self::buildQueryData($options['data']));
        }

        // Request timeout setting
        if (isset($options['timeout']) && is_numeric($options['timeout'])) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $options['timeout']);
        } else {
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $content = curl_exec($curl);
        curl_close($curl);

        return $content;
    }

    /**
     * POST data filtering
     * @param array $data Data to be processed
     * @param boolean $build Whether to compile data
     * @return array|string
     */
    private static function buildQueryData($data, $build = true)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_object($value) && $value instanceof \CURLFile) {
                $build = false;
            } elseif (is_string($value) && class_exists('CURLFile', false) && stripos($value, '@') === 0) {
                if (($filename = realpath(trim($value, '@'))) && file_exists($filename)) {
                    list($build, $data[$key]) = [false, new \CURLFile($filename)];
                }
            }
        }

        return $build ? http_build_query($data) : $data;
    }

    /**
     * Get browser proxy information
     * @return string
     */
    private static function getUserAgent()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }

        $userAgents = [
            "Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
            "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0",
            "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; rv:11.0) like Gecko",
            "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50",
            "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
            "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
        ];

        return $userAgents[array_rand($userAgents, 1)];
    }
}
