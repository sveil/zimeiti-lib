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

use sveil\rep\AliPay;

/**
 * Alipay App Payment Gateway
 *
 * Class App
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\alipay
 */
class App extends AliPay
{

    /**
     * App constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->options->set('method', 'alipay.trade.app.pay');
        $this->params->set('product_code', 'QUICK_MSECURITY_PAY');
    }

    /**
     * Create data operation
     *
     * @param array $options
     * @return string
     */
    public function apply($options)
    {
        $this->applyData($options);
        return http_build_query($this->options->get());
    }

}
