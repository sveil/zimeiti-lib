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

namespace sveil\rep\command\fans;

use sveil\rep\command\Fans;

/**
 * Fans Tag Directive
 *
 * Class FansTags
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\fans
 */
class FansTags extends Fans
{

    /**
     * Configure the entrance
     */
    protected function configure()
    {
        $this->module = ['tags'];
        $this->setName('xfans:tags')->setDescription('[同步]粉丝的标签记录数据');
    }

}
