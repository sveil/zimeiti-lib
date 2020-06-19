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
 * Class Emoji
 * Handling Emoji
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\common
 */
class Emoji
{
    /**
     * Emoji graphics converted to string
     * @param string $content
     * @return string
     */
    public static function encode($content)
    {
        return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($maps) {
            return addslashes($maps[0]);
        }, json_encode($content)));
    }

    /**
     * Emoji string converted to graphics
     * @param string $content
     * @return string
     */
    public static function decode($content)
    {
        return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, json_encode($content)));
    }

    /**
     * Emoji string cleanup
     * @param string $content
     * @return string
     */
    public static function clear($content)
    {
        return preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $content);
    }
}
