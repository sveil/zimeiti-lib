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
use sveil\Db;
use sveil\lib\service\Process;

/**
 * Class Start
 * Check and create monitoring main process
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\xdb
 */
class Start extends Command
{
    /**
     * Binding data table
     * @var string
     */
    protected $table = 'Queue';

    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->setName('xdb:start')->setDescription('Create daemons to listening main process');
    }

    /**
     * Start operation
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        Db::name($this->table)->count();
        $process = Process::instance();
        $command = $process->sveil("xdb:listen");

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
