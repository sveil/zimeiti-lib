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

namespace sveil\lib\command\fans;

use sveil\lib\command\Fans;

/**
 * Class FansList
 * Fans list instruction management
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\fans
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
