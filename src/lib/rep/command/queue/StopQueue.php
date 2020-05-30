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
use sveil\think\console\Command;
use sveil\think\console\Input;
use sveil\think\console\Output;

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
