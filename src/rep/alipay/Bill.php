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
 * Alipay bill download
 *
 * Class Bill
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\alipay
 */
class Bill extends AliPay
{

    /**
     * Bill constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);
        $this->options->set('method', 'alipay.data.dataservice.bill.downloadurl.query');
    }

    /**
     * Create data operation
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
