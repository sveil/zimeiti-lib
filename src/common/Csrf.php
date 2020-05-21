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

use think\exception\HttpResponseException;

/**
 * Form CSRF form token
 *
 * Class Csrf
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Csrf
{

    /**
     * Get current CSRF value
     *
     * @return string
     */
    public static function getToken()
    {
        return request()->header('User-Token-Csrf', input('_csrf_', ''));
    }

    /**
     * Check form CSRF verification
     *
     * @return boolean
     */
    public static function checkFormToken()
    {

        $token = self::getToken();
        $cache = session($token, '', 'csrf');

        if (empty($cache['node'])) {
            return false;
        }

        if (empty($cache['time'])) {
            return false;
        }

        if (empty($cache['token'])) {
            return false;
        }

        if ($cache['token'] !== $token) {
            return false;
        }

        if ($cache['time'] + 600 < time()) {
            return false;
        }

        if ($cache['node'] !== Node::current()) {
            return false;
        }

        return true;
    }

    /**
     * Clean up form CSRF information
     *
     * @param string $name
     */
    public static function clearFormToken($name = null)
    {
        is_null($name) ? session(null, 'csrf') : session($name, null, 'csrf');
    }

    /**
     * Generate form CSRF information
     *
     * @param null|string $node
     * @return array
     */
    public static function buildFormToken($node = null)
    {

        if (is_null($node)) {
            $node = Node::current();
        }

        list($token, $time) = [uniqid() . rand(10000, 9999), time()];
        session($token, ['node' => $node, 'token' => $token, 'time' => $time], 'csrf');

        foreach (session('', '', 'csrf') as $key => $item) {
            if (isset($item['time'])) {
                if ($item['time'] + 600 < $time) {
                    self::clearFormToken($key);
                }

            }
        }

        return ['token' => $token, 'node' => $node, 'time' => $time];
    }

    /**
     * Back to view content
     *
     * @param string $tpl template name
     * @param array $vars template variables
     * @param string $node CSRF authorized node
     */
    public static function fetchTemplate($tpl = '', $vars = [], $node = null)
    {

        throw new HttpResponseException(view($tpl, $vars, 200, function ($html) use ($node) {
            return preg_replace_callback('/<\/form>/i', function () use ($node) {
                $csrf = self::buildFormToken($node);
                return "<input type='hidden' name='_csrf_' value='{$csrf['token']}'></form>";
            }, $html);
        }));

    }

}
