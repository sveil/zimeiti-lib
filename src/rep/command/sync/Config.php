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
 * Application configuration module
 *
 * Class Config
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\sync
 */
class Config extends Sync
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->modules = ['config/'];
        $this->setName('xsync:config')->setDescription('[同步]覆盖本地Config应用配置');
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
        if (file_exists("{$root}/config/sync.lock")) {
            $this->output->error("--- Config 配置已经被锁定，不能继续更新");
        } else {
            parent::execute($input, $output);
        }
    }

}
