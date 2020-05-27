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

namespace sveil\common;

use sveil\facade\Request;

/**
 * Class Node
 * Controller Node Manager
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Node
{
    /**
     * Ignore the prefix of the control name
     * @var array
     */
    private static $ignoreController = ['api.', 'wap.', 'web.'];
    /**
     * Ignore control method name
     * @var array
     */
    private static $ignoreAction = ['_', 'redirect', 'assign', 'callback', 'initialize', 'success', 'error', 'fetch'];

    /**
     * Get standard access node
     * @param string $node
     * @return string
     */
    public static function get($node = null)
    {
        if (empty($node)) {
            return self::current();
        }

        if (count(explode('/', $node)) === 1) {
            $node = Request::module() . '/' . Request::controller() . '/' . $node;
        }

        return self::parseString(trim($node));
    }

    /**
     * Get current access node
     * @return string
     */
    public static function current()
    {
        return self::parseString(Request::module() . '/' . Request::controller() . '/' . Request::action());
    }

    /**
     * Get node list
     * @param string $dir Controller root path
     * @param array $nodes Extra data
     * @return array
     * @throws \ReflectionException
     */
    public static function getTree($dir, $nodes = [])
    {
        self::eachController($dir, function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $action = strtolower($method->getName());
                foreach (self::$ignoreAction as $ignore) {
                    if (stripos($action, $ignore) === 0) {
                        continue 2;
                    }
                }
                $nodes[] = $prenode . $action;
            };
        });

        return $nodes;
    }

    /**
     * Get a list of controller nodes
     * @param string $dir Controller root path
     * @param array $nodes Extra data
     * @return array
     * @throws \ReflectionException
     */
    public static function getClassTreeNode($dir, $nodes = [])
    {
        self::eachController($dir, function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            list($node, $comment) = [trim($prenode, '/'), $reflection->getDocComment()];
            $nodes[$node]         = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $comment));

            if (stripos($nodes[$node], '@') !== false) {
                $nodes[$node] = '';
            }
        });

        return $nodes;
    }

    /**
     * Get method node list
     * @param string $dir Controller root path
     * @param array $nodes Extra data
     * @return array
     * @throws \ReflectionException
     */
    public static function getMethodTreeNode($dir, $nodes = [])
    {
        self::eachController($dir, function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $action = strtolower($method->getName());

                foreach (self::$ignoreAction as $ignore) {
                    if (stripos($action, $ignore) === 0) {
                        continue 2;
                    }
                }

                $node         = $prenode . $action;
                $nodes[$node] = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $method->getDocComment()));

                if (stripos($nodes[$node], '@') !== false) {
                    $nodes[$node] = '';
                }
            }
        });

        return $nodes;
    }

    /**
     * Controller scan callback
     * @param string $dir
     * @param callable $callable
     * @throws \ReflectionException
     */
    public static function eachController($dir, $callable)
    {
        foreach (Node::scanDir($dir) as $file) {
            if (!preg_match("|/(\w+)/controller/(.+)\.php$|", strtr($file, '\\', '/'), $matches)) {
                continue;
            }

            list($module, $controller) = [$matches[1], strtr($matches[2], '/', '.')];

            foreach (self::$ignoreController as $ignore) {
                if (stripos($controller, $ignore) === 0) {
                    continue 2;
                }
            }

            if (class_exists($class = substr(strtr(env('app_namespace') . $matches[0], '/', '\\'), 0, -4))) {
                call_user_func($callable, new \ReflectionClass($class), Node::parseString("{$module}/{$controller}/"));
            }
        }
    }

    /**
     * Hump ​​to underline rule
     * @param string $node Node name
     * @return string
     */
    public static function parseString($node)
    {
        if (count($nodes = explode('/', $node)) > 1) {
            $dots = [];

            foreach (explode('.', $nodes[1]) as $dot) {
                $dots[] = trim(preg_replace("/[A-Z]/", "_\\0", $dot), "_");
            }

            $nodes[1] = join('.', $dots);
        }

        return strtolower(join('/', $nodes));
    }

    /**
     * Get all PHP files
     * @param string $dir Directory
     * @param array $data Extra data
     * @param string $ext File extension
     * @return array
     */
    public static function scanDir($dir, $data = [], $ext = 'php')
    {
        foreach (scandir($dir) as $curr) {
            if (strpos($curr, '.') !== 0) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $curr);

                if (is_dir($path)) {
                    $data = array_merge($data, self::scanDir($path));
                } elseif (pathinfo($path, PATHINFO_EXTENSION) === $ext) {
                    $data[] = $path;
                }
            }
        }

        return $data;
    }

    /**
     * Recursively count directory size
     * @param string $path path
     * @return integer
     */
    public static function totalDirSize($path)
    {
        list($total, $path) = [0, realpath($path)];

        if (!file_exists($path)) {
            return $total;
        }

        if (!is_dir($path)) {
            return filesize($path);
        }

        if ($handle = opendir($path)) {
            while ($file = readdir($handle)) {
                if (!in_array($file, ['.', '..'])) {
                    $temp = $path . DIRECTORY_SEPARATOR . $file;
                    $total += (is_dir($temp) ? self::totalDirSize($temp) : filesize($temp));
                }
            }

            if (is_resource($handle)) {
                closedir($handle);
            }
        }

        return $total;
    }
}
