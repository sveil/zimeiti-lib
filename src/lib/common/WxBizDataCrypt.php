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
 * Class WXBizDataCrypt
 * Sample code for decrypting encrypted data of WeChat applet users
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\common
 */
class WXBizDataCrypt
{
    private $appid;
    private $sessionKey;

    /**
     * Config constructor
     * @param $sessionKey string The session key obtained by the user after login in the applet
     * @param $appid string Applet appid
     */
    public function __construct($appid, $sessionKey)
    {
        $this->appid      = $appid;
        $this->sessionKey = $sessionKey;
        include_once __DIR__ . DIRECTORY_SEPARATOR . "errorCode.php";
    }

    /**
     * Verify the authenticity of the data and obtain the decrypted plaintext
     * @param $encryptedData string Encrypted user data
     * @param $iv string Initial vector returned with user data
     * @param $data string Decrypted original text
     * @return int Success 0, failure to return the corresponding error code
     */
    public function decryptData($encryptedData, $iv, &$data)
    {
        if (strlen($this->sessionKey) != 24) {
            return \ErrorCode::$IllegalAesKey;
        }

        $aesKey = base64_decode($this->sessionKey);

        if (strlen($iv) != 24) {
            return \ErrorCode::$IllegalIv;
        }

        $aesIV     = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result    = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj   = json_decode($result);

        if ($dataObj == null) {
            return \ErrorCode::$IllegalBuffer;
        }

        if ($dataObj->watermark->appid != $this->appid) {
            return \ErrorCode::$IllegalBuffer;
        }

        $data = $result;

        return \ErrorCode::$OK;
    }
}
