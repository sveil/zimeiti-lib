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
 * Class Crypt
 * Data encryption and decryption tools
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\common
 */
class Crypt
{
    /**
     * UTF8 serial encryption
     * @param string $string
     * @return string
     */
    public static function encode($string)
    {
        list($chars, $length) = ['', strlen($content = iconv('UTF-8', 'GBK//TRANSLIT', $string))];

        for ($i = 0; $i < $length; $i++) {
            $chars .= str_pad(base_convert(ord($content[$i]), 10, 36), 2, 0, 0);
        }

        return $chars;
    }

    /**
     * UTF8 string decryption
     * @param string $encode
     * @return string
     */
    public static function decode($encode)
    {
        $chars = '';

        foreach (str_split($encode, 2) as $char) {
            $chars .= chr(intval(base_convert($char, 36, 10)));
        }

        return iconv('GBK//TRANSLIT', 'UTF-8', $chars);
    }

    /**
     * Static call method processing
     * @param string $name
     * @param string $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        if (stripos($name, 'emoji') === 0) {
            $method = str_replace('emoji', '', strtolower($name));
            if (in_array($method, ['encode', 'decode', 'clear'])) {
                return Emoji::$method($args[0]);
            }
        }
    }
}
