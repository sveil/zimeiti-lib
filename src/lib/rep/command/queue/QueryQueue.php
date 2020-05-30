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
 * Query the PID of the process being executed
 *
 * Class QueryQueue
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\queue
 */
class QueryQueue extends Command
{

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->setName('xtask:query')->setDescription('Query all running task processes');
    }

    /**
     * Perform related process queries
     *
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $process = Process::instance();
        $result  = $process->query($process->think("xtask:"));
        if (count($result) > 0) {
            foreach ($result as $item) {
                $output->writeln("{$item['pid']}\t{$item['cmd']}");
            }
        } else {
            $output->writeln('No related task process found');
        }
    }

}
