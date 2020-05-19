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

namespace sveil\rep\alipay;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\AliPay;

/**
 * Alipay credit card payment
 *
 * Class Pos
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\alipay
 */
class Pos extends AliPay
{

    /**
     * Pos constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->options->set('method', 'alipay.trade.pay');
        $this->params->set('product_code', 'FACE_TO_FACE_PAYMENT');
    }

    /**
     * Create data operation
     *
     * @param array $options
     * @return mixed
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function apply($options)
    {
        return $this->getResult($options);
    }

}
