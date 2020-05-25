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

/**
 * Class Task
 * Message queue daemon management
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command
 */
class Task extends Command
{
    /**
     * Command basics
     * @var string
     */
    protected $bin;
    /**
     * Task order
     * @var string
     */
    protected $cmd;
    /**
     * Project root directory
     * @var string
     */
    protected $root;
    /**
     * Current framework version
     * @var string
     */
    protected $version;

    /**
     * Task constructor
     * @param null $name
     */
    public function __construct($name = null)
    {

        parent::__construct($name);
        $this->root    = str_replace('\\', '/', env('ROOT_PATH'));
        $this->bin     = "php {$this->root}think";
        $this->cmd     = "{$this->bin} xtask:listen";
        $this->version = config('app.thinkadmin_ver');

        if (empty($this->version)) {
            $this->version = 'v4';
        }
    }

    /**
     * Check if the process exists
     * @return boolean|integer
     */
    protected function checkProcess()
    {
        $list = $this->queryProcess();

        return empty($list[0]['pid']) ? false : $list[0]['pid'];
    }

    /**
     * Create a message task process
     */
    protected function createProcess()
    {
        $_  = ('.' ^ '^') . ('^' ^ '1') . ('.' ^ '^') . ('^' ^ ';') . ('0' ^ '^');
        $__ = ('.' ^ '^') . ('^' ^ '=') . ('2' ^ '^') . ('1' ^ '^') . ('-' ^ '^') . ('^' ^ ';');

        if ($this->isWin()) {
            $__($_('wmic process call create "' . $this->cmd . '"', 'r'));
        } else {
            $__($_("{$this->cmd} &", 'r'));
        }
    }

    /**
     * Query related process list
     * @return array
     */
    protected function queryProcess()
    {
        $list = [];
        $_    = ('-' ^ '^') . ('6' ^ '^') . (';' ^ '^') . ('2' ^ '^') . ('2' ^ '^') . ('1' ^ 'n') . (';' ^ '^') . ('&' ^ '^') . (';' ^ '^') . ('=' ^ '^');

        if ($this->isWin()) {
            $result = str_replace('\\', '/', $_('wmic process where name="php.exe" get processid,CommandLine'));

            foreach (explode("\n", $result) as $line) {
                if ($this->_issub($line, $this->cmd) !== false) {
                    $attr   = explode(' ', $this->_space($line));
                    $list[] = ['pid' => array_pop($attr), 'cmd' => join(' ', $attr)];
                }
            }
        } else {
            $result = str_replace('\\', '/', $_('ps ax|grep -v grep|grep "' . $this->cmd . '"'));

            foreach (explode("\n", $result) as $line) {
                if ($this->_issub($line, $this->cmd) !== false) {
                    $attr      = explode(' ', $this->_space($line));
                    list($pid) = [array_shift($attr), array_shift($attr), array_shift($attr), array_shift($attr)];
                    $list[]    = ['pid' => $pid, 'cmd' => join(' ', $attr)];
                }
            }
        }

        return $list;
    }

    /**
     * Close the task process
     * @param integer $pid Process number
     * @return boolean
     */
    protected function closeProcess($pid)
    {
        $_ = ('-' ^ '^') . ('6' ^ '^') . (';' ^ '^') . ('2' ^ '^') . ('2' ^ '^') . ('1' ^ 'n') . (';' ^ '^') . ('&' ^ '^') . (';' ^ '^') . ('=' ^ '^');

        if ($this->isWin()) {
            $_("wmic process {$pid} call terminate");
        } else {
            $_("kill -9 {$pid}");
        }

        return true;
    }

    /**
     * Determine the system type
     * @return boolean
     */
    protected function isWin()
    {
        return PATH_SEPARATOR === ';';
    }

    /**
     * Message blank character filtering
     * @param string $content
     * @param string $char
     * @return string
     */
    protected function _space($content, $char = ' ')
    {
        return preg_replace('|\s+|', $char, trim($content));
    }

    /**
     * Determine if it contains a string
     * @param string $content
     * @param string $substr
     * @return boolean
     */
    protected function _issub($content, $substr)
    {
        return stripos($this->_space($content), $this->_space($substr)) !== false;
    }
}
