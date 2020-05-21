<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
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
