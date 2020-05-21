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

use sveil\Data;
use sveil\Db;
use sveil\File;
use sveil\service\Admin;
use sveil\service\Node;
use sveil\service\System;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

if (!function_exists('isRead')) {
    /**
     * Check the directory readable or create directory
     *
     * @param string $dir Directory to be checked
     * @return boolean
     */
    function isRead($dir)
    {
        return File::checkRead($dir);
    }
}

if (!function_exists('isWrite')) {
    /**
     * Check that the directory can be written or created
     *
     * @param string $dir Directory to be checked
     * @return boolean
     */
    function isWrite($dir)
    {
        return File::checkWrite($dir);
    }
}

if (!function_exists('readFiles')) {
    /**
     * Read the entire file into a string
     *
     * @param string $filename
     * @return string|false
     */
    function readFiles($filename)
    {
        return File::readFiles($filename);
    }
}

if (!function_exists('arrFiles')) {
    /**
     * Save array to file
     *
     * @param string $filename
     * @param string $arr
     * @return number|false
     */
    function arrFiles($filename, $arr)
    {
        return File::arrFiles($filename, $arr);
    }
}

if (!function_exists('mysqlClientVersion')) {
    /**
     * Get the mysql client version
     *
     * @return string
     */
    function mysqlClientVersion()
    {
        return DB::mysqlClientVersion();
    }
}

if (!function_exists('auth')) {
    /**
     * Node access permission check
     *
     * @param string $node Node to check
     * @return boolean
     * @throws ReflectionException
     */
    function auth($node)
    {
        return Admin::instance()->check($node);
    }
}

if (!function_exists('sysdata')) {
    /**
     * JSON data reading and storage
     * @param string $name Data name
     * @param mixed $value Data content
     * @return mixed
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    function sysdata($name, $value = null)
    {

        if (is_null($value)) {
            return System::instance()->getData($name);
        } else {
            return System::instance()->setData($name, $value);
        }

    }
}

if (!function_exists('sysoplog')) {
    /**
     * Write to system log
     *
     * @param string $action Log behavior
     * @param string $content Log content
     * @return boolean
     */
    function sysoplog($action, $content)
    {
        return System::instance()->setOplog($action, $content);
    }
}

if (!function_exists('sysqueue')) {
    /**
     * Create asynchronous processing task
     * @param string $title task name
     * @param string $loade execution content
     * @param integer $later Delay execution time
     * @param array $data task additional data
     * @param integer $double task multi progress
     * @return boolean
     * @throws Exception
     */
    function sysqueue($title, $loade, $later = 0, $data = [], $double = 1)
    {

        $map = [['title', 'eq', $title], ['status', 'in', [1, 2]]];

        if (empty($double) && Db::name('SystemQueue')->where($map)->count() > 0) {
            throw new Exception('该任务已经创建，请耐心等待处理完成！');
        }

        $result = Db::name('SystemQueue')->insert([
            'title'  => $title, 'preload'            => $loade,
            'data'   => json_encode($data, JSON_UNESCAPED_UNICODE),
            'time'   => $later > 0 ? time() + $later : time(),
            'double' => intval($double), 'create_at' => date('Y-m-d H:i:s'),
        ]);

        return $result !== false;
    }
}

if (!function_exists('local_image')) {
    /**
     * Download remote file to local
     *
     * @param string $url remote image URL
     * @param boolean $force Whether to force re-download
     * @param integer $expire force local storage time
     * @return string
     */
    function localImage($url, $force = false, $expire = 0)
    {

        $result = File::down($url, $force, $expire);

        if (isset($result['url'])) {
            return $result['url'];
        } else {
            return $url;
        }

    }
}

if (!function_exists('base64_image')) {
    /**
     * base64 image upload interface
     * @param string $content image base64 content
     * @param string $dirname image storage directory
     * @return string
     */
    function base64Image($content, $dirname = 'base64/')
    {

        try {
            if (preg_match('|^data:image/(.*?);base64,|i', $content)) {
                list($ext, $base) = explode('|||', preg_replace('|^data:image/(.*?);base64,|i', '$1|||', $content));
                $info             = File::save($dirname . md5($base) . '.' . (empty($ext) ? 'tmp' : $ext), base64_decode($base));
                return $info['url'];
            } else {
                return $content;
            }
        } catch (\Exception $e) {
            return $content;
        }

    }
}

if (!function_exists('getNodes')) {
    /**
     * Get all node path
     *
     * @return string
     */
    function getNodes()
    {

        $arr = [];

        foreach (Node::instance()->getMethods() as $node => $method) {
            array_push($arr, $node);
        }

        return $arr;
    }
}

if (!function_exists('getPaths')) {
    /**
     * Get what is not in the database node path
     *
     * @return string
     */
    function getPaths()
    {
        return array_diff(getNodes(), Db::name('Auths')->column('path'));
    }
}

if (!function_exists('fixArr')) {
    /**
     * Recursively traverse according to ID and update array
     *
     * @param array $arr Array
     * @param int $id Node ID
     * @return array
     */
    function fixArr($arr, $id = 0)
    {

        foreach ($arr as $key => $value) {
            if (!is_array($value)) {
                echo "Key: $key; Value: $value<br />\n";
            } else {
                fixArr($value);
            }
        }

        return $arr;
    }
}

if (!function_exists('getAuths')) {
    /**
     * Get all lists of permission tables
     *
     * @param int $id Node ID
     * @return string
     */
    function getAuths($id = 0)
    {

        $arr = Db::name('Auths')->column('id, pid, title');

        return Data::id2arr($arr, $id);
    }
}
