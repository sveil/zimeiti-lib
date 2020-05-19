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
 * Fans Blacklist Directive
 *
 * Class FansBlack
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\fans
 */
class FansBlack extends Fans
{

    /**
     * Configure the entrance
     */
    protected function configure()
    {
        $this->module = ['black'];
        $this->setName('xfans:black')->setDescription('[同步]微信黑名单粉丝数据');
    }

}
