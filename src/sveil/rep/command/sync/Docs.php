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

namespace sveil\rep\command\sync;

use sveil\console\Input;
use sveil\console\Output;
use sveil\rep\command\Sync;

/**
 * Class Docs
 * Script module
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\sync
 */
class Docs extends Sync
{
    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->modules = ['apps/docs/', 'sveil'];
        $this->setName('xsync:docs')->setDescription('[同步]覆盖本地Docs模块代码');
    }

    /**
     * Perform update operation
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $root = str_replace('\\', '/', env('root_path'));

        if (file_exists("{$root}/apps/docs/sync.lock")) {
            $this->output->error("--- Docs 模块已经被锁定，不能继续更新");
        } else {
            parent::execute($input, $output);
        }
    }
}
