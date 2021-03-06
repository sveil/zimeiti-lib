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

use sveil\lib\exception\InvalidArgumentException;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\AliPay;

/**
 * Class Transfer
 * Alipay transfer to account
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\alipay
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
