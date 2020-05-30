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

namespace sveil\lib\rep\command\fans;

use sveil\lib\rep\command\Fans;

/**
 * Synchronize all fans commands
 *
 * Class FansAll
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\fans
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
