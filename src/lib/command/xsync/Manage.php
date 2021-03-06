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

namespace sveil\lib\command\xsync;

use sveil\console\Input;
use sveil\console\Output;
use sveil\lib\command\Sync;

/**
 * Class Manage
 * Management Module
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\xsync
 */
class Manage extends Sync
{
    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->modules = ['apps/manage/', 'sveil'];
        $this->setName('xsync:manage')->setDescription('[同步]覆盖本地Manage模块代码');
    }

    /**
     * Perform update operation
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $root = str_replace('\\', '/', env('root_path'));

        if (file_exists("{$root}/apps/manage/sync.lock")) {
            $this->output->error("--- Manage 模块已经被锁定，不能继续更新");
        } else {
            parent::execute($input, $output);
        }
    }
}
