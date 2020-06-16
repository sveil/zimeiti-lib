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
 * Class Wechat
 * WeChat module
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\xsync
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
