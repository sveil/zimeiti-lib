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
 * Class FansBlack
 * Fans Blacklist Directive
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\fans
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
