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

namespace sveil\rep\command\queue;

use sveil\service\Process;
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
