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

namespace sveil\rep\wechat\wepay;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WePay;

/**
 * Wechat Red package support
 *
 * Class Redpack
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wepay
 */
class Redpack extends WePay
{

    /**
     * create common red package
     *
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
     *
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
     *
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
