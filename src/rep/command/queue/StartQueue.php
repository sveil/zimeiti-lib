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

namespace sveil\rep\command\queue;

use sveil\service\Process;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * Check and create monitoring main process
 *
 * Class StartQueue
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\queue
 */
class StartQueue extends Command
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->setName('xtask:start')->setDescription('Create daemons to listening main process');
    }

    /**
     * Start operation
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {

        Db::name('SystemQueue')->count();
        $process = Process::instance();
        $command = $process->think("xtask:listen");

        if (count($result = $process->query($command)) > 0) {
            $output->info("Listening main process {$result['0']['pid']} has started");
        } else {
            $process->create($command);
            sleep(1);
            if (count($result = $process->query($command)) > 0) {
                $output->info("Listening main process {$result['0']['pid']} started successfully");
            } else {
                $output->error('Failed to create listening main process');
            }
        }

    }

}
