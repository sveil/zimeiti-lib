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
 * Class Transfers
 * WeChat merchants transfer money to change
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wepay
 */
class Transfers extends WePay
{
    /**
     * Corporate payment to change
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create(array $options)
    {
        $this->params->offsetUnset('appid');
        $this->params->offsetUnset('mch_id');
        $this->params->set('mchid', $this->config->get('mch_id'));
        $this->params->set('mch_appid', $this->config->get('appid'));
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

        return $this->callPostApi($url, $options, true, 'MD5', false);
    }

    /**
     * Check the corporate payment to change
     * @param string $partnerTradeNo The merchant order number used by the merchant when calling the enterprise payment API
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function query($partnerTradeNo)
    {
        $this->params->offsetUnset('mchid');
        $this->params->offsetUnset('mch_appid');
        $this->params->set('appid', $this->config->get('appid'));
        $this->params->set('mch_id', $this->config->get('mch_id'));
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo';

        return $this->callPostApi($url, ['partner_trade_no' => $partnerTradeNo], true, 'MD5', false);
    }
}
