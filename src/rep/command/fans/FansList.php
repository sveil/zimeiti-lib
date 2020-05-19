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
 * Fans list instruction management
 *
 * Class FansList
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\fans
 */
class FansList extends Fans
{

    /**
     * Configure the entrance
     */
    protected function configure()
    {
        $this->module = ['list'];
        $this->setName('xfans:list')->setDescription('[同步]微信粉丝的资料数据');
    }

}
