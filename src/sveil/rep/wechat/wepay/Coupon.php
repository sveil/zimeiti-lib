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

namespace sveil\rep\wechat\wepay;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WePay;

/**
 * Class Coupon
 * WeChat merchant vouchers
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wepay
 */
class Coupon extends WePay
{
    /**
     * Create vouchers
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
