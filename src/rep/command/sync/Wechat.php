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
 * WeChat module
 *
 * Class Wechat
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\sync
 */
class Wechat extends Sync
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->modules = ['apps/wechat/'];
        $this->setName('xsync:wechat')->setDescription('[同步]覆盖本地Wechat模块代码');
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
        if (file_exists("{$root}/apps/wechat/sync.lock")) {
            $this->output->error("--- Wechat 模块已经被锁定，不能继续更新");
        } else {
            parent::execute($input, $output);
        }
    }

}
