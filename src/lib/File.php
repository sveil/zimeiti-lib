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

namespace sveil\lib;

use sveil\lib\common\Options;
use sveil\lib\rep\storage\Local;
use sveil\lib\rep\storage\Oss;
use sveil\lib\rep\storage\Qiniu;
use sveil\Exception;
use sveil\exception\PDOException;
use sveil\facade\Log;

/**
 * File management base class
 *
 * Class File
 * @author Richard <richard@sveil.com>
 * @package sveil
 * @method object instance($name) static Set file driver name
 * @method string name($url, $ext = '', $pre = '', $fun = 'md5') static Get file relative name
 * @method bool checkRead($dir) static Check the directory readable or create directory
 * @method bool checkWrite($dir) static Check that the directory can be written or created
 * @method bool isRead($dir) static Recursively determine whether all content in the directory is readable
 * @method bool isWrite($dir) static Recursively determine whether all content in the directory is writable
 * @method string|false readFile($filename) static Read the entire file into a string
 */
class File
{

    const DEVER_LOCAL   = 'local';
    const DERVER_QINIU  = 'qiniu';
    const DERVER_ALIOSS = 'oss';

    /**
     * Current configuration object
     * @var Options
     */
    public static $config;

    /**
     * Object buffer
     * @var array
     */
    protected static $object = [];

    /**
     * File storage parameters
     * @var array
     */
    protected static $params = [
        'const' => [
            'storage_type' => '文件存储类型',
        ],
        'local' => [
            'storage_local_exts' => '文件上传允许类型后缀',
        ],
        'oss'   => [
            'storage_oss_domain'   => '文件访问域名',
            'storage_oss_keyid'    => '接口授权AppId',
            'storage_oss_secret'   => '接口授权AppSecret',
            'storage_oss_bucket'   => '文件存储空间名称',
            'storage_oss_is_https' => '文件HTTP访问协议',
            'storage_oss_endpoint' => '文件存储节点域名',
        ],
        'qiniu' => [
            'storage_qiniu_region'     => '文件存储节点',
            'storage_qiniu_domain'     => '文件访问域名',
            'storage_qiniu_bucket'     => '文件存储空间名称',
            'storage_qiniu_is_https'   => '文件HTTP访问协议',
            'storage_qiniu_access_key' => '接口授权AccessKey',
            'storage_qiniu_secret_key' => '接口授权SecretKey',
        ],
    ];

    /**
     * Static magic method
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {

        if (method_exists($class = self::instance(self::$config->get('storage_type')), $name)) {
            return call_user_func_array([$class, $name], $arguments);
        }

        throw new Exception("File driver method not exists: " . get_class($class) . "->{$name}");
    }

    /**
     * Set file driver name
     *
     * @param string $name
     * @return Local|Qiniu|Oss
     * @throws Exception
     */
    public static function instance($name)
    {

        if (isset(self::$object[$class = ucfirst(strtolower($name))])) {
            return self::$object[$class];
        }

        if (class_exists($object = __NAMESPACE__ . "\\driver\\{$class}")) {
            return self::$object[$class] = new $object;
        }

        throw new Exception("File driver [{$class}] does not exist.");
    }

    /**
     * Obtain the file MINE according to the file suffix
     *
     * @param array $ext File extension
     * @param array $mine File extension MINE info
     * @return string
     */
    public static function mine($ext, $mine = [])
    {

        $mines = self::mines();

        foreach (is_string($ext) ? explode(',', $ext) : $ext as $e) {
            $mine[] = isset($mines[strtolower($e)]) ? $mines[strtolower($e)] : 'application/octet-stream';
        }

        return join(',', array_unique($mine));
    }

    /**
     * Get all file extension mine
     *
     * @return mixed
     */
    public static function mines()
    {

        static $mimes = [];

        if (count($mimes) > 0) {
            return $mimes;
        }

        return $mimes = include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . '_mime.php';
    }

    /**
     * Get file relative name
     *
     * @param string $url File link
     * @param string $ext File extension
     * @param string $pre File prefix（If any value needs to end with /）
     * @param string $fun File name generation method
     * @return string
     */
    public static function name($url, $ext = '', $pre = '', $fun = 'md5')
    {

        empty($ext) && $ext = pathinfo($url, 4);
        empty($ext) || $ext = trim($ext, '.\\/');
        empty($pre) || $pre = trim($pre, '.\\/');
        $splits             = array_merge([$pre], str_split($fun($url), 16));

        return date("Ymd") . '/' . trim(join('/', $splits), '/') . '.' . strtolower($ext ? $ext : 'tmp');
    }

    /**
     * 下载文件到本地
     * @param string $url 文件URL地址
     * @param boolean $force 是否强制下载
     * @param integer $expire 文件保留时间
     * @return array
     */
    public static function down($url, $force = false, $expire = 0)
    {

        try {
            $file = self::instance('local');
            $name = self::name($url, '', 'down/');
            if (empty($force) && $file->has($name)) {
                if ($expire < 1 || filemtime($file->path($name)) + $expire > time()) {
                    return $file->info($name);
                }
            }
            return $file->save($name, file_get_contents($url));
        } catch (\Exception $e) {
            Log::error(__METHOD__ . " File download failed [ {$url} ] {$e->getMessage()}");
            return ['url' => $url, 'hash' => md5($url), 'key' => $url, 'file' => $url];
        }

    }

    /**
     * 文件储存初始化
     * @param array $data
     * @throws Exception
     * @throws PDOException
     */
    public static function init($data = [])
    {

        if (empty(self::$config) && function_exists('sysconf')) {
            foreach (self::$params as $arr) {
                foreach (array_keys($arr) as $key) {
                    $data[$key] = sysconf($key);
                }
            }
        }

        self::$config = new Options($data);

    }

    /**
     * Determine whether the directory exists, Determine if it is readable if it exists, create a directory if it does not exist
     * @param string $dir
     * @return bool
     */
    public static function checkRead($dir)
    {

        if (is_dir($dir)) {
            if (is_readable($dir)) {
                return true;
            } else {
                return false;
            }
        } else {
            try {
                return mkdir($dir, 0755, true);
            } catch (\Throwable $th) {
                return false;
            }
        }

    }

    /**
     * Determine whether the directory exists, Judge if it exists, Create a directory if it does not exist
     *
     * @param string $dir
     * @return bool
     */
    public static function checkWrite($dir)
    {

        if (is_dir($dir)) {
            if (is_writable($dir)) {
                return true;
            } else {
                return false;
            }
        } else {
            return mkdir($dir, 0755, true);
        }

    }

    /**
     * Recursively determine whether all content in the directory is readable
     *
     * @param string $dir
     * @return bool
     */
    public static function isRead($dir)
    {

        if (is_dir($dir)) {
            if (is_readable($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (!self::isRead($dir . "/" . $object)) {
                            return false;
                        } else {
                            continue;
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        } elseif (file_exists($dir)) {
            return (is_readable($dir));
        }

    }

    /**
     * Recursively determine whether all content in the directory is writable
     *
     * @param string $dir
     * @return bool
     */
    public static function isWrite($dir)
    {

        if (is_dir($dir)) {
            if (is_writable($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (!self::isWrite($dir . "/" . $object)) {
                            return false;
                        } else {
                            continue;
                        }
                    }
                }
                return true;
            } else {
                return false;
            }
        } elseif (file_exists($dir)) {
            return (is_writable($dir));
        }

    }

    /**
     * Read the entire file into a string
     *
     * @param string $filename
     * @return string|false
     */
    public static function readFiles($filename)
    {

        if (function_exists('file_get_contents')) {
            return @file_get_contents($filename, false, null, -1);
        } else {
            Log::error(__METHOD__ . " Function @file_get_contents is not exists!");
            return false;
        }

    }

    /**
     * Write string to file
     *
     * @param string $filename
     * @param string $data
     * @return number|false
     */
    public static function writeFiles($filename, $data = '')
    {

        $dir = dirname($filename);

        if (!is_dir($dir)) {
            mkdirss($dir);
        }

        if (function_exists('file_put_contents')) {
            return @file_put_contents($filename, $data);
        } else {
            Log::error(__METHOD__ . " Function @file_put_contents is not exists!");
            return false;
        }

    }

    /**
     * Save array to file
     *
     * @param string $filename
     * @param string $arr
     * @return number|false
     */
    public static function arrFiles($filename, $arr = '')
    {

        if (is_array($arr)) {
            $con = Strings::arrStr($arr, true);
        } else {
            $con = $arr;
        }

        $con = "<?php\nreturn $con;\n?>";

        return self::writeFiles($filename, $con);
    }

}

try {
    // 初始化存储
    File::init();
    // Log::info(__METHOD__ . ' File storage initialization success');
} catch (\Exception $e) {
    Log::error(__METHOD__ . " File storage initialization exception. [{$e->getMessage()}]");
}
