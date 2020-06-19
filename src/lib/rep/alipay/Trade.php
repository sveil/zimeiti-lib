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
 * Class Trade
 * Alipay standard interface
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\alipay
 */
class Trade extends AliPay
{
    /**
     * Set transaction interface address
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->options->set('method', $method);

        return $this;
    }

    /**
     * Get transaction interface address
     * @return string
     */
    public function getMethod()
    {
        return $this->options->get('method');
    }

    /**
     * Set interface common parameters
     * @param array $option
     * @return Trade
     */
    public function setOption($option = [])
    {
        foreach ($option as $key => $vo) {
            $this->options->set($key, $vo);
        }

        return $this;
    }

    /**
     * Get interface common parameters
     * @return array|string|null
     */
    public function getOption()
    {
        return $this->options->get();
    }

    /**
     * Execute via interface
     * @param array $options
     * @return array|boolean|mixed
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function apply($options)
    {
        return $this->getResult($options);
    }
}
