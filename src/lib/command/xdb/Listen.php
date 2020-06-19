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
use sveil\db\exception\DataNotFoundException;
use sveil\db\exception\ModelNotFoundException;
use sveil\Exception;
use sveil\exception\DbException;
use sveil\exception\PDOException;
use sveil\facade\Log;
use sveil\lib\service\Process;

/**
 * Class Listen
 * Start the main process of the listening task
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\xdb
 */
class Listen extends Command
{
    /**
     * Current task service
     * @var ProcessService
     */
    protected $process;

    /**
     * Binding data table
     * @var string
     */
    protected $table = 'Queue';

    /**
     * Configuration specific information
     */
    protected function configure()
    {
        $this->setName('xdb:listen')->setDescription('Start datebase listening main process');
    }

    /**
     * Execution process daemon monitoring
     * @param Input $input
     * @param Output $output
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    protected function execute(Input $input, Output $output)
    {
        set_time_limit(0);

        if (($process = Process::instance())->iswin() && function_exists('cli_set_process_title')) {
            cli_set_process_title("Database {$process->version()} Queue Listen");
        }

        $output->writeln('============ LISTENING ============');

        while (true) {
            sleep(10);
            $count = Db::name($this->table)->count();
            Log::error(__METHOD__ . " Queue count is [ {$count} ]");
        }
    }
}
