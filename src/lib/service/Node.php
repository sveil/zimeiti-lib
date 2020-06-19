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

namespace sveil\lib\service;

use sveil\lib\Service;

/**
 * Class Node
 * Application node service management
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Node extends Service
{
    /**
     * Hump ​​to underline rule
     *
     * @param string $name
     * @return string
     */
    public function nameTolower($name)
    {
        $dots = [];

        foreach (explode('.', strtr($name, '/', '.')) as $dot) {
            $dots[] = trim(preg_replace("/[A-Z]/", "_\\0", $dot), "_");
        }

        return strtolower(join('.', $dots));
    }

    /**
     * Get the current node content
     *
     * @param string $type
     * @return string
     */
    public function getCurrent($type = '')
    {

        $prefix = $this->request->module();
        $middle = '\\' . $this->nameTolower($this->app->request->controller());
        $suffix = ($type === 'controller') ? '' : ('\\' . $this->app->request->action());

        return strtolower(strtr($prefix . $middle . $suffix, '\\', '/'));
    }

    /**
     * Check and complete node content
     *
     * @param string $node
     * @return string
     */
    public function fullnode($node)
    {
        if (empty($node)) {
            return $this->getCurrent();
        }

        if (count($attrs = explode('/', $node)) === 1) {
            return strtolower($this->getCurrent('controller') . "/{$node}");
        } else {
            $attrs[1] = $this->nameTolower($attrs[1]);
            return strtolower(join('/', $attrs));
        }
    }

    /**
     * Controller method scan processing
     *
     * @param boolean $force
     * @return array
     * @throws \ReflectionException
     */
    public function getMethods($force = false)
    {
        static $data = [];

        if (empty($force)) {
            if (count($data) > 0) {
                return $data;
            }

            $data = $this->app->cache->get('system_auth_node');
            if (is_array($data) && count($data) > 0) {
                return $data;
            }

        } else {
            $data = [];
        }

        $ignore = get_class_methods('\sveil\Controller');

        foreach ($this->scanDirectory($this->app->getAppPath()) as $file) {
            if (preg_match("|/(\w+)/controller/(.+)\.php$|i", $file, $matches)) {
                list(, $application, $baseclass) = $matches;
                $namespace                       = $this->app->env->get('APP_NAMESPACE');
                $class                           = new \ReflectionClass(strtr("{$namespace}/{$application}/controller/{$baseclass}", '/', '\\'));
                // $application = $application==='admin' ? config('admin_module') : $application;
                $prefix        = strtr("{$application}/" . $this->nameTolower($baseclass), '\\', '/');
                $data[$prefix] = $this->parseComment($class->getDocComment(), $baseclass);
                foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if (is_array($ignore) && in_array($method->getName(), $ignore)) {
                        continue;
                    }
                    $data["{$prefix}/{$method->getName()}"] = $this->parseComment($method->getDocComment(), $method->getName());
                }
            }
        }

        $this->app->cache->set('system_auth_node', $data);

        return $data;
    }

    /**
     * Parse hard node attributes
     *
     * @param string $comment
     * @param string $default
     * @return array
     */
    private function parseComment($comment, $default = '')
    {

        $text  = strtr($comment, "\n", ' ');
        $title = preg_replace('/^\/\*\s*\*\s*\*\s*(.*?)\s*\*.*?$/', '$1', $text);

        return [
            'title'   => $title ? $title : $default,
            'isauth'  => intval(preg_match('/@auth\s*true/i', $text)),
            'ismenu'  => intval(preg_match('/@menu\s*true/i', $text)),
            'islogin' => intval(preg_match('/@login\s*true/i', $text)),
        ];
    }

    /**
     * Get a list of all PHP files
     *
     * @param string $path Scan directory
     * @param array $data Extra data
     * @param string $ext Has file suffix
     * @return array
     */
    private function scanDirectory($path, $data = [], $ext = 'php')
    {

        foreach (glob("{$path}*") as $item) {
            if (is_dir($item)) {
                $data = array_merge($data, $this->scanDirectory("{$item}/"));
            } elseif (is_file($item) && pathinfo($item, PATHINFO_EXTENSION) === $ext) {
                $data[] = strtr($item, '\\', '/');
            }
        }

        return $data;
    }

}
