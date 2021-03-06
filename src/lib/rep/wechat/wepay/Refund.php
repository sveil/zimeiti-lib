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
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WePay;

/**
 * Class Refund
 * WeChat merchant refund
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wepay
 */
class Refund extends WePay
{
    /**
     * Create refund order
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create(array $options)
    {
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

        return $this->callPostApi($url, $options, true);
    }

    /**
     * Check refund
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function query(array $options)
    {
        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';

        return $this->callPostApi($url, $options);
    }

    /**
     * Get a refund notice
     * @return array
     * @throws InvalidResponseException
     */
    public function getNotify()
    {
        $data = Tools::xml2arr(file_get_contents("php://input"));

        if (!isset($data['return_code']) || $data['return_code'] !== 'SUCCESS') {
            throw new InvalidResponseException('获取退款通知XML失败！');
        }

        if (!class_exists('Prpcrypt', false)) {
            include dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'Prpcrypt.php';
        }

        $pc    = new \Prpcrypt(md5($this->config->get('mch_key')));
        $array = $pc->decrypt(base64_decode($data['req_info']));

        if (intval($array[0]) > 0) {
            throw new InvalidResponseException($array[1], $array[0]);
        }

        $data['decode'] = $array[1];

        return $data;
    }
}
