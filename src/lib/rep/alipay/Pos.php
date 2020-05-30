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

namespace sveil\lib\rep\alipay;

use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\AliPay;

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