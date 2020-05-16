<?php

// +----------------------------------------------------------------------
// | Library for Sveil
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/boy12371/think-lib
// | github：https://github.com/boy12371/think-lib
// +----------------------------------------------------------------------

namespace sveil\common;

/**
 * String tool
 *
 * Class Strings
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 * @method string arrStr($arr) static Array to string
 */
class Strings
{

    /**
     * Array to string
     *
     * @param array $arr
     * @return string
     */
    public static function arrStr($arr)
    {

        $result = str_replace('array (', '[', var_export($arr, true));
        $result = str_replace(')', ']', $result);

        return $result;
    }

}
