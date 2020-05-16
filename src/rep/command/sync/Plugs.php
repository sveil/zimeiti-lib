<?php

// +----------------------------------------------------------------------
// | Library for Sveil
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/boy12371/think-lib
// | github：https://github.com/boy12371/think-lib
// +----------------------------------------------------------------------

namespace sveil\rep\command\sync;

use sveil\rep\command\Sync;
use think\console\Input;
use think\console\Output;

/**
 * Plug-in module
 *
 * Class Plugs
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\sync
 */
class Plugs extends Sync
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->modules = ['public/static/'];
        $this->setName('xsync:plugs')->setDescription('[同步]覆盖本地Plugs插件代码');
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

        if (file_exists("{$root}/public/static/sync.lock")) {
            $this->output->error("--- Plugs 资源已经被锁定，不能继续更新");
        } else {
            parent::execute($input, $output);
        }

    }

}
