<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\wepay;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WePay;

/**
 * WeChat merchants transfer money to change
 *
 * Class Transfers
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wepay
 */
class Transfers extends WePay
{

    /**
     * Corporate payment to change
     *
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
     *
     * @param string $partnerTradeNo 商户调用企业付款API时使用的商户订单号
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
