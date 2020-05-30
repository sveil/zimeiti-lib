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
 * PKCS7 algorithm - encryption and decryption
 *
 * Class PKCS7Encoder
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class PKCS7Encoder
{

    public static $blockSize = 32;

    /**
     * Padding the plaintext that needs to be encrypted
     *
     * @param string $text Plain text requiring padding
     * @return string Fill in plain text strings
     */
    public function encode($text)
    {

        $amount_to_pad = PKCS7Encoder::$blockSize - (strlen($text) % PKCS7Encoder::$blockSize);

        if ($amount_to_pad == 0) {
            $amount_to_pad = PKCS7Encoder::$blockSize;
        }

        list($pad_chr, $tmp) = [chr($amount_to_pad), ''];

        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }

        return $text . $tmp;
    }

    /**
     * Fill-in and delete the decrypted plaintext
     *
     * @param string $text Decrypted plaintext
     * @return string Plaintext with padding removed
     */
    public function decode($text)
    {
        $pad = ord(substr($text, -1));

        if ($pad < 1 || $pad > PKCS7Encoder::$blockSize) {
            $pad = 0;
        }

        return substr($text, 0, strlen($text) - $pad);
    }

}
