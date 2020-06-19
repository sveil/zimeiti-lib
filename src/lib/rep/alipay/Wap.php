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

use sveil\lib\rep\AliPay;

/**
 * Class Wap
 * Mobile WAP website payment support
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\alipay
 */
class Wap extends AliPay
{
    /**
     * Wap constructor
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->options->set('method', 'alipay.trade.wap.pay');
        $this->params->set('product_code', 'QUICK_WAP_WAY');
    }

    /**
     * Create data operation
     * @param array $options
     * @return string
     */
    public function apply($options)
    {
        parent::applyData($options);

        return $this->buildPayHtml();
    }
}
