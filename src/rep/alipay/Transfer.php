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

use sveil\exception\InvalidArgumentException;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\AliPay;

/**
 * Alipay transfer to account
 *
 * Class Transfer
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\alipay
 */
class Transfer extends AliPay
{

    /**
     * Old: Transfer to designated Alipay account
     * @param array $options
     * @return mixed
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function apply($options)
    {
        $this->options->set('method', 'alipay.fund.trans.toaccount.transfer');
        return $this->getResult($options);
    }

    /**
     * New: Transfer to designated Alipay account
     * @param array $options
     * @return array|bool
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function create($options = [])
    {
        $this->setAppCertSnAndRootCertSn();
        $this->options->set('method', 'alipay.fund.trans.uni.transfer');
        return $this->getResult($options);
    }

    /**
     * New: Transfer business document query interface
     * @param array $options
     * @return array|bool
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryResult($options = [])
    {
        $this->setAppCertSnAndRootCertSn();
        $this->options->set('method', 'alipay.fund.trans.common.query');
        return $this->getResult($options);

    }

    /**
     * New: Alipay fund account asset query interface
     * @param array $options
     * @return array|bool
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryAccount($options = [])
    {
        $this->setAppCertSnAndRootCertSn();
        $this->options->set('method', 'alipay.fund.account.query');
        return $this->getResult($options);
    }

    /**
     * New: Set gateway application public key certificate SN, Alipay root certificate SN
     */
    protected function setAppCertSnAndRootCertSn()
    {
        if (!$this->config->get('app_cert')) {
            throw new InvalidArgumentException("Missing Config -- [app_cert]");
        }
        if (!$this->config->get('root_cert')) {
            throw new InvalidArgumentException("Missing Config -- [root_cert]");
        }
        $this->options->set('app_cert_sn', $this->getCertSN($this->config->get('app_cert')));
        $this->options->set('alipay_root_cert_sn', $this->getRootCertSN($this->config->get('root_cert')));
        if (!$this->options->get('app_cert_sn')) {
            throw new InvalidArgumentException("Missing options -- [app_cert_sn]");
        }
        if (!$this->options->get('alipay_root_cert_sn')) {
            throw new InvalidArgumentException("Missing options -- [alipay_root_cert_sn]");
        }
    }

}
