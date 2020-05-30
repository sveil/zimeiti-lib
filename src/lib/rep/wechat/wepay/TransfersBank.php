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

namespace sveil\lib\rep\wechat\wepay;

use sveil\lib\common\Tools;
use sveil\lib\exception\InvalidArgumentException;
use sveil\lib\exception\InvalidDecryptException;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WePay;

/**
 * WeChat merchant transfers money to bank card
 *
 * Class TransfersBank
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wepay
 */
class TransfersBank extends WePay
{

    /**
     * Corporate payment to bank card
     *
     * @param array $options
     * @return array
     * @throws InvalidDecryptException
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create(array $options)
    {

        if (!isset($options['partner_trade_no'])) {
            throw new InvalidArgumentException('Missing Options -- [partner_trade_no]');
        }

        if (!isset($options['enc_bank_no'])) {
            throw new InvalidArgumentException('Missing Options -- [enc_bank_no]');
        }

        if (!isset($options['enc_true_name'])) {
            throw new InvalidArgumentException('Missing Options -- [enc_true_name]');
        }

        if (!isset($options['bank_code'])) {
            throw new InvalidArgumentException('Missing Options -- [bank_code]');
        }

        if (!isset($options['amount'])) {
            throw new InvalidArgumentException('Missing Options -- [amount]');
        }

        $this->params->offsetUnset('appid');

        return $this->callPostApi('https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank', [
            'amount'           => $options['amount'],
            'bank_code'        => $options['bank_code'],
            'partner_trade_no' => $options['partner_trade_no'],
            'enc_bank_no'      => $this->rsaEncode($options['enc_bank_no']),
            'enc_true_name'    => $this->rsaEncode($options['enc_true_name']),
            'desc'             => isset($options['desc']) ? $options['desc'] : '',
        ], true, 'MD5', false);
    }

    /**
     * Merchant enterprise payment to bank card operation for results query
     *
     * @param string $partnerTradeNo Merchant order number, need to be unique
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function query($partnerTradeNo)
    {

        $this->params->offsetUnset('appid');
        $url = 'https://api.mch.weixin.qq.com/mmpaysptrans/query_bank';

        return $this->callPostApi($url, ['partner_trade_no' => $partnerTradeNo], true, 'MD5', false);
    }

    /**
     * RSA encryption processing
     *
     * @param string $string
     * @param string $encrypted
     * @return string
     * @throws InvalidDecryptException
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    private function rsaEncode($string, $encrypted = '')
    {

        $search    = ['-----BEGIN RSA PUBLIC KEY-----', '-----END RSA PUBLIC KEY-----', "\n", "\r"];
        $pkc1      = str_replace($search, '', $this->getRsaContent());
        $publicKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL . wordwrap('MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8A' . $pkc1, 64, PHP_EOL, true) .
            PHP_EOL . '-----END PUBLIC KEY-----';

        if (!openssl_public_encrypt("{$string}", $encrypted, $publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new InvalidDecryptException('Rsa Encrypt Error.');
        }

        return base64_encode($encrypted);
    }

    /**
     * Get the contents of the signed file
     *
     * @return string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    private function getRsaContent()
    {

        $cacheKey = "pub_ras_key_" . $this->config->get('mch_id');

        if (($pub_key = Tools::getCache($cacheKey))) {
            return $pub_key;
        }

        $data = $this->callPostApi('https://fraud.mch.weixin.qq.com/risk/getpublickey', [], true, 'MD5');

        if (!isset($data['return_code']) || $data['return_code'] !== 'SUCCESS' || $data['result_code'] !== 'SUCCESS') {
            $error = 'ResultError:' . $data['return_msg'];
            $error .= isset($data['err_code_des']) ? ' - ' . $data['err_code_des'] : '';
            throw new InvalidResponseException($error, 20000, $data);
        }

        Tools::setCache($cacheKey, $data['pub_key'], 600);

        return $data['pub_key'];
    }

}
