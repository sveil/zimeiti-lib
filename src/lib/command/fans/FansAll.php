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
 * Class FansAll
 * Synchronize all fans commands
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\fans
 */
class FansAll extends Fans
{
    /**
     * Configure the entrance
     */
    protected function configure()
    {
        $this->module = ['list', 'black', 'tags'];
        $this->setName('xfans:all')->setDescription('[同步]所有微信粉丝的数据');
    }
}
