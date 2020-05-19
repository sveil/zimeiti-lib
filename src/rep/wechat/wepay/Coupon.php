<?php

// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-lib
// | github：https://github.com/sveil/zimeiti-lib
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\wepay;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WePay;

/**
 * WeChat merchant vouchers
 *
 * Class Coupon
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wepay
 */
class Coupon extends WePay
{

    /**
     * Create vouchers
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create(array $options)
    {

        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/send_coupon";

        return $this->callPostApi($url, $options, true);
    }

    /**
     * Query voucher batch
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryStock(array $options)
    {

        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/query_coupon_stock";

        return $this->callPostApi($url, $options, false);
    }

    /**
     * Check voucher information
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryInfo(array $options)
    {

        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/query_coupon_stock";

        return $this->callPostApi($url, $options, false);
    }

}
