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
use sveil\think\Db;
use sveil\think\db\exception\DataNotFoundException;
use sveil\think\db\exception\ModelNotFoundException;
use sveil\think\Exception;
use sveil\think\exception\DbException;
use sveil\think\exception\PDOException;

/**
 * Start the main process of the listening task
 *
 * Class ListenQueue
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\queue
 */
class ListenQueue extends Command
{

    /**
     * Current task service
     * @var ProcessService
     */
    protected $process;

    /**
     * Configuration specific information
     */
    protected function configure()
    {
        $this->setName('xtask:listen')->setDescription('Start task listening main process');
    }

    /**
     * Execution process daemon monitoring
     *
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
        Db::name('SystemQueue')->count();

        if (($process = Process::instance())->iswin() && function_exists('cli_set_process_title')) {
            cli_set_process_title("ThinkAdmin {$process->version()} Queue Listen");
        }

        $output->writeln('============ LISTENING ============');

        while (true) {
            $map = [['status', 'eq', '1'], ['time', '<=', time()]];
            foreach (Db::name('SystemQueue')->where($map)->order('time asc')->select() as $vo) {
                try {
                    $command = $process->think("xtask:_work {$vo['id']} -");
                    if (count($process->query($command)) > 0) {
                        $this->output->writeln("Already in progress -> [{$vo['id']}] {$vo['title']}");
                    } else {
                        $process->create($command);
                        $this->output->writeln("Created new process -> [{$vo['id']}] {$vo['title']}");
                    }
                } catch (\Exception $e) {
                    Db::name('SystemQueue')->where(['id' => $vo['id']])->update(['status' => '4', 'desc' => $e->getMessage()]);
                    $output->error("Execution failed -> [{$vo['id']}] {$vo['title']}，{$e->getMessage()}");
                }
            }
            sleep(1);
        }

    }

}