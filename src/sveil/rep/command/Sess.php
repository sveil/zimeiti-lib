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

namespace sveil\rep\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * Class Sess
 * Clean up session files
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command
 */
class Sess extends Command
{
    /**
     * Command attribute configuration
     */
    protected function configure()
    {
        $this->setName('xclean:session')->setDescription('Clean up invalid session files');
    }

    /**
     * Perform a cleanup operation
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $output->comment('Start cleaning up invalid session files');

        foreach (glob(config('session.path') . 'sess_*') as $file) {
            list($fileatime, $filesize) = [fileatime($file), filesize($file)];

            if ($filesize < 1 || $fileatime < time() - 3600) {
                $output->info('Remove session file -> [ ' . date('Y-m-d H:i:s', $fileatime) . ' ] ' . basename($file) . " {$filesize}");
                @unlink($file);
            }
        }

        $output->comment('Cleaning up invalid session files complete');
    }
}
