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

use sveil\common\Crypt;
use sveil\common\Data;
use sveil\common\Emoji;
use sveil\common\Http;
use sveil\common\Strings;
use sveil\service\Token;
use think\Console;
use think\Db;
use think\db\Query;
use think\Exception;
use think\exception\PDOException;
use think\facade\Cache;
use think\facade\Env;
use think\facade\Lang;
use think\facade\Middleware;
use think\Request;

Env::set(['doc_path' => Env::get('root_path') . 'docs' . DIRECTORY_SEPARATOR]);

if (!function_exists('p')) {
    /**
     * Print output data to file
     * @param mixed $data Output data
     * @param boolean $force Forced replacement
     * @param string|null $file File name
     */
    function p($data, $force = false, $file = null)
    {
        if (is_null($file)) {
            $file = env('runtime_path') . date('Ymd') . '.txt';
        }

        $str = (is_string($data) ? $data : ((is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true))) . PHP_EOL;
        $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Date format standard output
     * @param string $datetime Enter date
     * @param string $format Output format
     * @return false|string
     */
    function formatDatetime($datetime, $format = 'Y年m月d日 H:i:s')
    {
        if (empty($datetime)) {
            return '-';
        }

        if (is_numeric($datetime)) {
            return date($format, $datetime);
        } else {
            return date($format, strtotime($datetime));
        }
    }
}

if (!function_exists('sysconf')) {
    /**
     * Equipment or configuration system parameters
     * @param string $name parameter name
     * @param boolean $value null|value
     * @return string|boolean
     * @throws Exception
     * @throws PDOException
     */
    function sysconf($name, $value = null)
    {
        static $data       = [];
        list($field, $raw) = explode('|', "{$name}|");
        $key               = md5(config('database.hostname') . '#' . config('database.database'));

        if ($value !== null) {
            Cache::tag('system')->rm("_sysconfig_{$key}");
            list($row, $data) = [['name' => $field, 'value' => $value], []];
            return Data::save('SystemConfig', $row, 'name');
        }

        if (empty($data)) {
            $data = Cache::tag('system')->get("_sysconfig_{$key}", []);

            if (empty($data)) {
                $data = Db::name('SystemConfig')->column('name,value');
                Cache::tag('system')->set("_sysconfig_{$key}", $data, 60);
            }
        }

        if (isset($data[$field])) {
            if (strtolower($raw) === 'raw') {
                return $data[$field];
            } else {
                return htmlspecialchars($data[$field]);
            }
        } else {
            return '';
        }
    }
}

if (!function_exists('systoken')) {
    /**
     * Generate CSRF-TOKEN parameters
     * @param string $node
     * @return string
     */
    function systoken($node = null)
    {
        $csrf = Token::instance()->buildFormToken($node);

        return $csrf['token'];
    }
}

if (!function_exists('httpGet')) {
    /**
     * Simulate network requests with get
     * @param string $url HTTP Request URL
     * @param array $query GET Request parameter
     * @param array $options CURL parameter
     * @return boolean|string
     */
    function httpGet($url, $query = [], $options = [])
    {
        return Http::get($url, $query, $options);
    }
}

if (!function_exists('httpPost')) {
    /**
     * Simulate network requests with post
     * @param string $url HTTP Request URL
     * @param array $data POST Request Data
     * @param array $options CURL parameter
     * @return boolean|string
     */
    function httpPost($url, $data, $options = [])
    {
        return Http::post($url, $data, $options);
    }
}

if (!function_exists('dataSave')) {
    /**
     * Data incremental storage
     * @param Query|string $dbQuery Data query object
     * @param array $data Data to be saved or updated
     * @param string $key primary key restrictions by condition
     * @param array $where Other where conditions
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    function dataSave($dbQuery, $data, $key = 'id', $where = [])
    {
        return Data::save($dbQuery, $data, $key, $where);
    }
}

if (!function_exists('dataBatchSave')) {
    /**
     * Update data in bulk
     * @param Query|string $dbQuery Data query object
     * @param array $data Data to be updated(Two-dimensional array)
     * @param string $key primary key restrictions by condition
     * @param array $where Other where conditions
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    function dataBatchSave($dbQuery, $data, $key = 'id', $where = [])
    {
        return Data::batchSave($dbQuery, $data, $key, $where);
    }
}

if (!function_exists('encode')) {
    /**
     * Encrypt UTF8 string
     * @param string $content
     * @return string
     */
    function encode($content)
    {
        return Crypt::encode($content);
    }
}

if (!function_exists('decode')) {
    /**
     * Decrypt UTF8 string
     * @param string $content
     * @return string
     */
    function decode($content)
    {
        return Crypt::decode($content);
    }
}

if (!function_exists('emojiEncode')) {
    /**
     * Coding emoji
     * @param string $content
     * @return string
     */
    function emojiEncode($content)
    {
        return Emoji::encode($content);
    }
}

if (!function_exists('emojiDecode')) {
    /**
     * Parse emoji
     * @param string $content
     * @return string
     */
    function emojiDecode($content)
    {
        return Emoji::decode($content);
    }
}

if (!function_exists('emojiClear')) {
    /**
     * Clear emoji
     * @param string $content
     * @return string
     */
    function emojiClear($content)
    {
        return Emoji::clear($content);
    }
}

if (PHP_SAPI !== 'cli') {
    // Register cross-domain middle key
    Middleware::add(function (Request $request, \Closure $next, $header = []) {
        if (($origin = $request->header('origin', '*')) !== '*') {
            $header['Access-Control-Allow-Origin']   = $origin;
            $header['Access-Control-Allow-Methods']  = 'GET,POST,PATCH,PUT,DELETE';
            $header['Access-Control-Allow-Headers']  = 'Authorization,Content-Type,If-Match,If-Modified-Since,If-None-Match,If-Unmodified-Since,X-Requested-With';
            $header['Access-Control-Expose-Headers'] = 'User-Token-Csrf';
        }

        if ($request->isOptions()) {
            return response()->code(204)->header($header);
        } else {
            return $next($request)->header($header);
        }
    });
}

// Common instructions for the registration system
if (class_exists('think\Console')) {
    Console::addDefaultCommands([
        // Register to clean up invalid sessions
        'sveil\command\Sess',
        // Register System Task Instructions
        'sveil\queue\WorkQueue',
        'sveil\queue\StopQueue',
        'sveil\queue\StateQueue',
        'sveil\queue\StartQueue',
        'sveil\queue\QueryQueue',
        'sveil\queue\ListenQueue',
        // Register System Update Instructions
        'sveil\command\sync\Admin',
        'sveil\command\sync\Plugs',
        'sveil\command\sync\Config',
        'sveil\command\sync\Wechat',
        'sveil\command\sync\Service',
    ]);
}

// Load the corresponding language pack
$root = rtrim(str_replace('\\', '/', Env::get('root_path')), '/');
Lang::load($root . '/lang/zh-cn.php', 'zh-cn');
Lang::load($root . '/lang/en-us.php', 'en-us');

// Dynamically load module configuration
if (function_exists('think\__include_file')) {
    $apps = rtrim(str_replace('\\', '/', Env::get('app_path')), '/');
    foreach (glob("{$apps}/*/sys.php") as $file) {
        \think\__include_file($file);
    }
}

if (!function_exists('format_status')) {
    /**
     * Status format standard output
     * @param string $datetime Enter date
     * @return string
     */
    function formatStatus($status)
    {
        if ($status === 0) {
            return '禁止';
        }

        if (empty($status)) {
            return '-';
        }

        return '启用';
    }
}

if (!function_exists('arrStr')) {
    /**
     * Array to string
     * @param array $arr
     * @return string
     */
    function arrStr($arr)
    {
        return Strings::arrStr($arr);
    }
}

if (!function_exists('jumpLogin')) {
    /**
     * Jump login window
     * @return string
     */
    function jumpLogin()
    {
        return json([
            'code' => 0,
            'info' => '对不起，已经无法再安装视微系统了。',
            'url'  => url('@' . config('admin_module') . '/login'),
        ]);
    }
}
