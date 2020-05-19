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

namespace sveil\rep\command\sync;

use sveil\rep\command\Sync;
use think\console\Input;
use think\console\Output;

/**
 * Management Module
 *
 * Class Manage
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\sync
 */
class Manage extends Sync
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->modules = ['apps/manage/', 'think'];
        $this->setName('xsync:manage')->setDescription('[同步]覆盖本地Manage模块代码');
    }

    /**
     * Perform update operation
     *
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
