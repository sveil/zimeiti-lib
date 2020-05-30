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

namespace sveil\lib\rep\command\queue;

use sveil\lib\service\Process;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * View the status of the main process monitored by the task
 *
 * Class StateQueue
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\queue
 */
class StateQueue extends Command
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->setName('xtask:state')->setDescription('Check listening main process status');
    }

    /**
     * Instruction execution status
     *
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {

        $process = Process::instance();
        $command = $process->think('xtask:listen');

        if (count($result = $process->query($command)) > 0) {
            $output->info("Listening for main process {$result[0]['pid']} running");
        } else {
            $output->error("The Listening main process is not running");
        }

    }

}
