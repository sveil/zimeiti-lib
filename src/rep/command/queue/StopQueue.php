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

namespace sveil\rep\command\queue;

use sveil\service\Process;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * Smoothly stop all processes of the task
 *
 * Class StopQueue
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\queue
 */
class StopQueue extends Command
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->setName('xtask:stop')->setDescription('Smooth stop of all task processes');
    }

    /**
     * Stop all task execution
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {

        $process = Process::instance();
        $command = $process->think('xtask:');
        if (count($result = $process->query($command)) < 1) {
            $output->writeln("There is no task process to finish");
        } else {
            foreach ($result as $item) {
                $process->close($item['pid']);
                $output->writeln("Sending end process {$item['pid']} signal succeeded");
            }
        }

    }

}
