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

use sveil\common\Tools;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WePay;

/**
 * WeChat merchant bills and comments
 *
 * Class Bill
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wepay
 */
class Bill extends WePay
{

    /**
     * Download statement
     *
     * @param array $options Mute parameter
     * @param null|string $outType Output type
     * @return bool|string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function download(array $options, $outType = null)
    {

        $this->params->set('sign_type', 'MD5');
        $params         = $this->params->merge($options);
        $params['sign'] = $this->getPaySign($params, 'MD5');
        $result         = Tools::post('https://api.mch.weixin.qq.com/pay/downloadbill', Tools::arr2xml($params));

        if (($jsonData = Tools::xml2arr($result))) {
            if ($jsonData['return_code'] !== 'SUCCESS') {
                throw new InvalidResponseException($jsonData['return_msg'], '0');
            }
        }

        return is_null($outType) ? $result : $outType($result);
    }

    /**
     * Pull order evaluation data
     *
     * @param array $options
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function comment(array $options)
    {

        $url = 'https://api.mch.weixin.qq.com/billcommentsp/batchquerycomment';

        return $this->callPostApi($url, $options, true);
    }

}
