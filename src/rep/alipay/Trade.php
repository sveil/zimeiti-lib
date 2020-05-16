<?php

// +----------------------------------------------------------------------
// | Library for Sveil
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/boy12371/think-lib
// | github：https://github.com/boy12371/think-lib
// +----------------------------------------------------------------------

namespace sveil\rep\alipay;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\AliPay;

/**
 * Alipay standard interface
 *
 * Class Trade
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\alipay
 */
class Trade extends AliPay
{

    /**
     * Set transaction interface address
     *
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
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->options->get('method');
    }

    /**
     * Set interface common parameters
     *
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
     *
     * @return array|string|null
     */
    public function getOption()
    {
        return $this->options->get();
    }

    /**
     * Execute via interface
     *
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
