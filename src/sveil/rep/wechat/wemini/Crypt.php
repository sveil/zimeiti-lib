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

namespace sveil\rep\wechat\wemini;

use sveil\common\Tools;
use sveil\exception\InvalidDecryptException;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Class Crypt
 * Data encryption
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Crypt extends WeChat
{
    /**
     * Data signature verification
     * @param string $iv
     * @param string $sessionKey
     * @param string $encryptedData
     * @return bool
     */
    public function decode($iv, $sessionKey, $encryptedData)
    {
        require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'WxBizDataCrypt.php';
        $pc      = new \WXBizDataCrypt($this->config->get('appid'), $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            return json_decode($data, true);
        }

        return false;
    }

    /**
     * Login credential verification
     * @param string $code Code obtained during login
     * @return array
     * @throws LocalCacheException
     */
    public function session($code)
    {
        $appid  = $this->config->get('appid');
        $secret = $this->config->get('appsecret');
        $url    = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";

        return json_decode(Tools::get($url), true);
    }

    /**
     * Exchange user information
     * @param string $code User login credentials (valid for five minutes)
     * @param string $iv Initial vector of encryption algorithm
     * @param string $encryptedData Encrypted data ( encryptedData )
     * @return array
     * @throws InvalidDecryptException
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function userInfo($code, $iv, $encryptedData)
    {
        $result = $this->session($code);

        if (empty($result['session_key'])) {
            throw new InvalidResponseException('Code 换取 SessionKey 失败', 403);
        }

        $userinfo = $this->decode($iv, $result['session_key'], $encryptedData);

        if (empty($userinfo)) {
            throw new InvalidDecryptException('用户信息解析失败', 403);
        }

        return array_merge($result, $userinfo);
    }

    /**
     * After the user completes the payment, obtain the user's UnionId
     * @param string $openid Payment user unique identification
     * @param null|string $transaction_id WeChat payment order number
     * @param null|string $mch_id The merchant number assigned by WeChat Pay is used in conjunction with the merchant order number
     * @param null|string $out_trade_no WeChat payment merchant order number, used in conjunction with the merchant number
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getPaidUnionId($openid, $transaction_id = null, $mch_id = null, $out_trade_no = null)
    {
        $url = "https://api.weixin.qq.com/wxa/getpaidunionid?access_token=ACCESS_TOKEN&openid={$openid}";

        if (is_null($mch_id)) {
            $url .= "&mch_id={$mch_id}";
        }

        if (is_null($out_trade_no)) {
            $url .= "&out_trade_no={$out_trade_no}";
        }

        if (is_null($transaction_id)) {
            $url .= "&transaction_id={$transaction_id}";
        }

        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callGetApi($url);
    }
}
