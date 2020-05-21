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
