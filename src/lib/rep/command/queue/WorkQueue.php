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
use sveil\Console;
use sveil\console\Command;
use sveil\console\Input;
use sveil\console\input\Argument;
use sveil\console\Output;
use sveil\Db;
use sveil\Exception;
use sveil\exception\PDOException;

/**
 * Start an independent execution process
 *
 * Class WorkQueue
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command\queue
 */
class WorkQueue extends Command
{

    /**
     * Current task ID
     * @var integer
     */
    protected $id;

    /**
     * Binding data table
     * @var string
     */
    protected $table = 'SystemQueue';

    /**
     * Configuration specific information
     */
    protected function configure()
    {

        $this->setName('xtask:_work')->setDescription('Create a process to execute a task');
        $this->addArgument('id', Argument::OPTIONAL, 'TaskNumber');
        $this->addArgument('sp', Argument::OPTIONAL, 'Separator');

    }

    /**
     * Task execution
     *
     * @param Input $input
     * @param Output $output
     * @throws Exception
     * @throws PDOException
     */
    protected function execute(Input $input, Output $output)
    {

        $this->id = trim($input->getArgument('id'));

        if (empty($this->id)) {
            $this->output->error('Task number needs to be specified for task execution');
        } else {
            try {
                $queue = Db::name('SystemQueue')->where(['id' => $this->id, 'status' => '1'])->find();
                if (empty($queue)) {
                    // No processing is done here (the task may already be executed elsewhere)
                    $this->output->warning("The or status of task {$this->id} is abnormal");
                } else {
                    // Lock task status
                    Db::name('SystemQueue')->where(['id' => $queue['id']])->update(['status' => '2', 'start_at' => date('Y-m-d H:i:s')]);
                    // Set Process Title
                    if (($process = Process::instance())->iswin() && function_exists('cli_set_process_title')) {
                        cli_set_process_title("ThinkAdmin {$process->version()} Queue - {$queue['title']}");
                    }
                    // Content of task
                    if (class_exists($queue['preload'])) {
                        // Custom file, support return message (support abnormal end, abnormal code can choose 3 | 4 to set task status)
                        if (method_exists($class = new $queue['preload'], 'execute')) {
                            $data = json_decode($queue['data'], true);
                            if (isset($class->jobid)) {
                                $class->jobid = $this->id;
                            }
                            if (isset($class->title)) {
                                $class->title = $queue['title'];
                            }
                            $this->update('3', $class->execute($input, $output, is_array($data) ? $data : []));
                        } else {
                            throw new Exception("Task processing class {$queue['preload']} not defined execute");
                        }
                    } else {
                        // User-defined instructions, do not support return message (support abnormal end, exception code can choose 3 | 4 set task status)
                        $attr = explode(' ', trim(preg_replace('|\s+|', ' ', $queue['preload'])));
                        $this->update('3', Console::call(array_shift($attr), $attr)->fetch());
                    }
                }
            } catch (\Exception $e) {
                if (in_array($e->getCode(), ['3', '4'])) {
                    $this->update($e->getCode(), $e->getMessage());
                } else {
                    $this->update('4', $e->getMessage());
                }
            }

        }

    }

    /**
     * Modify the current task status
     *
     * @param mixed $status Task status
     * @param mixed $message Message content
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    protected function update($status, $message)
    {

        $desc   = explode("\n", trim(is_string($message) ? $message : ''));
        $result = Db::name('SystemQueue')->where(['id' => $this->id])->update([
            'status' => $status, 'end_at' => date('Y-m-d H:i:s'), 'desc' => $desc[0],
        ]);
        $this->output->writeln(is_string($message) ? $message : '');

        return $result !== false;
    }

}
