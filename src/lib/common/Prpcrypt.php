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
 * WeOpen message - encryption and decryption
 *
 * Class Prpcrypt
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Prpcrypt
{

    public $key;

    /**
     * Prpcrypt constructor
     *
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = base64_decode("{$key}=");
    }

    /**
     * Encrypt plaintext
     *
     * @param string $text Plaintext that needs encryption
     * @param string $appid WeOpen APPID
     * @return array
     */
    public function encrypt($text, $appid)
    {
        try {
            $random     = $this->getRandomStr();
            $iv         = substr($this->key, 0, 16);
            $pkcEncoder = new PKCS7Encoder();
            $text       = $pkcEncoder->encode($random . pack("N", strlen($text)) . $text . $appid);
            $encrypted  = openssl_encrypt($text, 'AES-256-CBC', substr($this->key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
            return [ErrorCode::$OK, $encrypted];
        } catch (Exception $e) {
            return [ErrorCode::$EncryptAESError, null];
        }
    }

    /**
     * Decrypt the ciphertext
     *
     * @param string $encrypted Ciphertext to be decrypted
     * @return array
     */
    public function decrypt($encrypted)
    {

        try {
            $iv        = substr($this->key, 0, 16);
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', substr($this->key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
        } catch (Exception $e) {
            return [ErrorCode::$DecryptAESError, null];
        }

        try {
            $pkcEncoder = new PKCS7Encoder();
            $result     = $pkcEncoder->decode($decrypted);
            if (strlen($result) < 16) {
                return [ErrorCode::$DecryptAESError, null];
            }
            $content  = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len  = $len_list[1];
            return [0, substr($content, 4, $xml_len), substr($content, $xml_len + 4)];
        } catch (Exception $e) {
            return [ErrorCode::$IllegalBuffer, null];
        }

    }

    /**
     * Randomly generate 16-bit character strings
     *
     * @param string $str
     * @return string The generated string
     */
    public function getRandomStr($str = "")
    {

        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max     = strlen($str_pol) - 1;

        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }

        return $str;
    }

}
