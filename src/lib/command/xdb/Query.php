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

namespace sveil\lib\command\xdb;

use sveil\console\Command;
use sveil\console\Input;
use sveil\console\Output;
use sveil\lib\service\Process;

/**
 * Class Query
 * Query the PID of the process being executed
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\xdb
 */
class Query extends Command
{
    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->setName('xdb:query')->setDescription('Query all running task processes');
    }

    /**
     * Perform related process queries
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $process = Process::instance();
        $result  = $process->query($process->sveil("xdb:"));

        if (count($result) > 0) {
            foreach ($result as $item) {
                $output->writeln("{$item['pid']}\t{$item['cmd']}");
            }
        } else {
            $output->writeln('No related task process found');
        }
    }
}
