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

use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WePay;

/**
 * Class Redpack
 * Wechat Red package support
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wepay
 */
class Redpack extends WePay
{
    /**
     * create common red package
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create(array $options)
    {
        $this->params->offsetUnset('appid');
        $this->params->set('wxappid', $this->config->get('appid'));
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";

        return $this->callPostApi($url, $options, true, 'MD5', false);
    }

    /**
     * create fission red package
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function groups(array $options)
    {
        $this->params->offsetUnset('appid');
        $this->params->set('wxappid', $this->config->get('appid'));
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack";

        return $this->callPostApi($url, $options, true, 'MD5', false);
    }

    /**
     * Query red package records
     * @param string $mchBillno Merchant order number created by the merchant
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function query($mchBillno)
    {
        $this->params->offsetUnset('wxappid');
        $this->params->set('appid', $this->config->get('appid'));
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";

        return $this->callPostApi($url, ['mch_billno' => $mchBillno, 'bill_type' => 'MCHT'], true, 'MD5', false);
    }
}
