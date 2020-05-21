<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
// +----------------------------------------------------------------------

namespace sveil\common;

use sveil\exception\InvalidArgumentException;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;

/**
 * Network request support
 *
 * Class Tools
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Tools
{

    /**
     * Cache path
     *
     * @var null
     */
    public static $cache_path = null;

    /**
     * Cache write operation
     *
     * @var array
     */
    public static $cache_callable = [
        'set' => null, // Write cache
        'get' => null, // Get cache
        'del' => null, // Delete cache
        'put' => null, // Write file
    ];

    /**
     * Network cache
     *
     * @var array
     */
    private static $cache_curl = [];

    /**
     * Generate random strings
     *
     * @param int $length Specify character length
     * @param string $str String prefix
     * @return string
     */
    public static function createNoncestr($length = 32, $str = "")
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";

        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    /**
     * Get file type from file suffix
     *
     * @param string|array $ext File extension
     * @param array $mine File suffix MINE information
     * @return string
     * @throws LocalCacheException
     */
    public static function getExtMine($ext, $mine = [])
    {

        $mines = self::getMines();

        foreach (is_string($ext) ? explode(',', $ext) : $ext as $e) {
            $mine[] = isset($mines[strtolower($e)]) ? $mines[strtolower($e)] : 'application/octet-stream';
        }

        return join(',', array_unique($mine));
    }

    /**
     * Get all file extension types
     *
     * @return array
     * @throws LocalCacheException
     */
    private static function getMines()
    {

        $mines = self::getCache('all_ext_mine');

        if (empty($mines)) {
            $content = file_get_contents('http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types');
            preg_match_all('#^([^\s]{2,}?)\s+(.+?)$#ism', $content, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                foreach (explode(" ", $match[2]) as $ext) {
                    $mines[$ext] = $match[1];
                }
            }
            self::setCache('all_ext_mine', $mines);
        }

        return $mines;
    }

    /**
     * Create CURL file object
     *
     * @param $filename
     * @param string $mimetype
     * @param string $postname
     * @return \CURLFile|string
     * @throws LocalCacheException
     */
    public static function createCurlFile($filename, $mimetype = null, $postname = null)
    {

        if (is_string($filename) && file_exists($filename)) {
            if (is_null($postname)) {
                $postname = basename($filename);
            }
            if (is_null($mimetype)) {
                $mimetype = self::getExtMine(pathinfo($filename, 4));
            }
            if (function_exists('curl_file_create')) {
                return curl_file_create($filename, $mimetype, $postname);
            }
            return "@{$filename};filename={$postname};type={$mimetype}";
        }

        return $filename;
    }

    /**
     * Array to XML content
     *
     * @param array $data
     * @return string
     */
    public static function arr2xml($data)
    {
        return "<xml>" . self::_arr2xml($data) . "</xml>";
    }

    /**
     * XML content generation
     *
     * @param array $data data
     * @param string $content
     * @return string
     */
    private static function _arr2xml($data, $content = '')
    {

        foreach ($data as $key => $val) {
            is_numeric($key) && $key = 'item';
            $content .= "<{$key}>";
            if (is_array($val) || is_object($val)) {
                $content .= self::_arr2xml($val);
            } elseif (is_string($val)) {
                $content .= '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $val) . ']]>';
            } else {
                $content .= $val;
            }
            $content .= "</{$key}>";
        }

        return $content;
    }

    /**
     * Parsing XML content into array
     *
     * @param string $xml
     * @return array
     */
    public static function xml2arr($xml)
    {

        $entity = libxml_disable_entity_loader(true);
        $data   = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        libxml_disable_entity_loader($entity);

        return json_decode(json_encode($data), true);
    }

    /**
     * Array to XML content
     *
     * @param array $data
     * @return null|string|string
     */
    public static function arr2json($data)
    {

        $json = json_encode(self::buildEnEmojiData($data), JSON_UNESCAPED_UNICODE);

        return $json === '[]' ? '{}' : $json;
    }

    /**
     * Array object Emoji compilation processing
     *
     * @param array $data
     * @return array
     */
    public static function buildEnEmojiData(array $data)
    {

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::buildEnEmojiData($value);
            } elseif (is_string($value)) {
                $data[$key] = self::emojiEncode($value);
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Array object Emoji reverse analysis processing
     *
     * @param array $data
     * @return array
     */
    public static function buildDeEmojiData(array $data)
    {

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::buildDeEmojiData($value);
            } elseif (is_string($value)) {
                $data[$key] = self::emojiDecode($value);
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Convert Emoji to String
     *
     * @param string $content
     * @return string
     */
    public static function emojiEncode($content)
    {

        return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($string) {
            return addslashes($string[0]);
        }, json_encode($content)));

    }

    /**
     * Emoji string to graphics
     *
     * @param string $content
     * @return string
     */
    public static function emojiDecode($content)
    {

        return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, json_encode($content)));

    }

    /**
     * Parsing JSON content to array
     *
     * @param string $json
     * @return array
     * @throws InvalidResponseException
     */
    public static function json2arr($json)
    {

        $result = json_decode($json, true);

        if (empty($result)) {
            throw new InvalidResponseException('invalid response.', '0');
        }

        if (!empty($result['errcode'])) {
            throw new InvalidResponseException($result['errmsg'], $result['errcode'], $result);
        }

        return $result;
    }

    /**
     * Simulate access with get method
     *
     * @param string $url Access URL
     * @param array $query GET number
     * @param array $options
     * @return boolean|string
     * @throws LocalCacheException
     */
    public static function get($url, $query = [], $options = [])
    {

        $options['query'] = $query;

        return self::doRequest('get', $url, $options);
    }

    /**
     * Simulate access with post method
     *
     * @param string $url Access URL
     * @param array $data POST data
     * @param array $options
     * @return boolean|string
     * @throws LocalCacheException
     */
    public static function post($url, $data = [], $options = [])
    {

        $options['data'] = $data;

        return self::doRequest('post', $url, $options);
    }

    /**
     * CURL simulate network requests
     *
     * @param string $method request method
     * @param string $url request URL
     * @param array $options Request parameters[headers,data,ssl_cer,ssl_key]
     * @return boolean|string
     * @throws LocalCacheException
     */
    public static function doRequest($method, $url, $options = [])
    {

        $curl = curl_init();

        // GET parameter setting
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . http_build_query($options['query']);
        }

        // CURL header information settings
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }

        // POST data settings
        if (strtolower($method) === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, self::_buildHttpData($options['data']));
        }

        // Certificate file settings
        if (!empty($options['ssl_cer'])) {
            if (file_exists($options['ssl_cer'])) {
                curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLCERT, $options['ssl_cer']);
            } else {
                throw new InvalidArgumentException("Certificate files that do not exist. --- [ssl_cer]");
            }
        }

        // Certificate key settings
        if (!empty($options['ssl_key'])) {
            if (file_exists($options['ssl_key'])) {
                curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLKEY, $options['ssl_key']);
            } else {
                throw new InvalidArgumentException("Certificate files that do not exist. --- [ssl_key]");
            }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        list($content) = [curl_exec($curl), curl_close($curl)];

        // Clean up CURL cache files
        if (!empty(self::$cache_curl)) {
            foreach (self::$cache_curl as $key => $file) {
                Tools::delCache($file);
                unset(self::$cache_curl[$key]);
            }
        }

        return $content;
    }

    /**
     * POST data filtering
     *
     * @param array $data Data to be processed
     * @param boolean $build Whether to compile data
     * @return array|string
     * @throws LocalCacheException
     */
    private static function _buildHttpData($data, $build = true)
    {

        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_object($value) && $value instanceof \CURLFile) {
                $build = false;
            } elseif (is_object($value) && isset($value->datatype) && $value->datatype === 'MY_CURL_FILE') {
                $build      = false;
                $mycurl     = new MyCurlFile((array) $value);
                $data[$key] = $mycurl->get();
                array_push(self::$cache_curl, $mycurl->tempname);
            } elseif (is_string($value) && class_exists('CURLFile', false) && stripos($value, '@') === 0) {
                if (($filename = realpath(trim($value, '@'))) && file_exists($filename)) {
                    $build      = false;
                    $data[$key] = self::createCurlFile($filename);
                }
            }
        }

        return $build ? http_build_query($data) : $data;
    }

    /**
     * Write file
     *
     * @param string $name file name
     * @param string $content file content
     * @return string
     * @throws LocalCacheException
     */
    public static function pushFile($name, $content)
    {

        if (is_callable(self::$cache_callable['put'])) {
            return call_user_func_array(self::$cache_callable['put'], func_get_args());
        }

        $file = self::_getCacheName($name);

        if (!file_put_contents($file, $content)) {
            throw new LocalCacheException('local file write error.', '0');
        }

        return $file;
    }

    /**
     * Cache configuration and storage
     *
     * @param string $name Cache name
     * @param string $value Cache content
     * @param int $expired Cache time (0 permanent cache)
     * @return string
     * @throws LocalCacheException
     */
    public static function setCache($name, $value = '', $expired = 3600)
    {

        if (is_callable(self::$cache_callable['set'])) {
            return call_user_func_array(self::$cache_callable['set'], func_get_args());
        }

        $file = self::_getCacheName($name);
        $data = ['name' => $name, 'value' => $value, 'expired' => time() + intval($expired)];

        if (!file_put_contents($file, serialize($data))) {
            throw new LocalCacheException('local cache error.', '0');
        }

        return $file;
    }

    /**
     * Get cached content
     *
     * @param string $name Cache name
     * @return null|mixed
     */
    public static function getCache($name)
    {

        if (is_callable(self::$cache_callable['get'])) {
            return call_user_func_array(self::$cache_callable['get'], func_get_args());
        }

        $file = self::_getCacheName($name);

        if (file_exists($file) && ($content = file_get_contents($file))) {
            $data = unserialize($content);
            if (isset($data['expired']) && (intval($data['expired']) === 0 || intval($data['expired']) >= time())) {
                return $data['value'];
            }
            self::delCache($name);
        }

        return null;
    }

    /**
     * Remove cache file
     *
     * @param string $name cache file
     * @return boolean
     */
    public static function delCache($name)
    {

        if (is_callable(self::$cache_callable['del'])) {
            return call_user_func_array(self::$cache_callable['del'], func_get_args());
        }

        $file = self::_getCacheName($name);

        return file_exists($file) ? unlink($file) : true;
    }

    /**
     * Application cache directory
     *
     * @param string $name
     * @return string
     */
    private static function _getCacheName($name)
    {

        if (empty(self::$cache_path)) {
            self::$cache_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR;
        }

        self::$cache_path = rtrim(self::$cache_path, '/\\') . DIRECTORY_SEPARATOR;
        file_exists(self::$cache_path) || mkdir(self::$cache_path, 0755, true);

        return self::$cache_path . $name;
    }

}
