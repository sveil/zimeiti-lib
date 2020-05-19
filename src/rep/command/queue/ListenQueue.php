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
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

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
