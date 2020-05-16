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

namespace sveil\service;

use sveil\Service;

/**
 * System Process Management Service
 *
 * Class Process
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Process extends Service
{

    /**
     * Create and get Think instruction content
     *
     * @param string $args Specify parameters
     * @return string
     */
    public function think($args = '')
    {
        $root = $this->app->getRootPath();
        return trim("php {$root}think {$args}");
    }

    /**
     * Get current application version
     *
     * @return string
     */
    public function version()
    {
        return $this->app->config->get('app.thinkadmin_ver', 'v4');
    }

    /**
     * Create asynchronous process
     *
     * @param string $command Task command
     * @return $this
     */
    public function create($command)
    {
        if ($this->iswin()) {
            $this->exec(__DIR__ . "/bin/console.exe {$command}");
        } else {
            $this->exec("{$command} > /dev/null &");
        }
        return $this;
    }

    /**
     * Query related process list
     *
     * @param string $command Task command
     * @return array
     */
    public function query($command)
    {

        $list = [];

        if ($this->iswin()) {
            $lines = $this->exec('wmic process where name="php.exe" get processid,CommandLine', true);
            foreach ($lines as $line) {
                if ($this->_issub($line, $command) !== false) {
                    $attr   = explode(' ', $this->_space($line));
                    $list[] = ['pid' => array_pop($attr), 'cmd' => join(' ', $attr)];
                }
            }
        } else {
            $lines = $this->exec("ps ax|grep -v grep|grep \"{$command}\"", true);
            foreach ($lines as $line) {
                if ($this->_issub($line, $command) !== false) {
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
     *
     * @param integer $pid Process number
     * @return $this
     */
    public function close($pid)
    {

        if ($this->iswin()) {
            $this->exec("wmic process {$pid} call terminate");
        } else {
            $this->exec("kill -9 {$pid}");
        }

        return $this;
    }

    /**
     * Execute instructions immediately
     *
     * @param string $command Execute command
     * @param boolean $outarr Return type
     * @return string|array
     */
    public function exec($command, $outarr = false)
    {

        exec($command, $output);

        return $outarr ? $output : join("\n", $output);
    }

    /**
     * Determine the system type
     *
     * @return boolean
     */
    public function iswin()
    {
        return PATH_SEPARATOR === ';';
    }

    /**
     * Message blank character filtering
     *
     * @param string $content
     * @param string $tochar
     * @return string
     */
    private function _space($content, $tochar = ' ')
    {
        return preg_replace('|\s+|', $tochar, strtr(trim($content), '\\', '/'));
    }

    /**
     * Determine if it contains a string
     *
     * @param string $content
     * @param string $substr
     * @return boolean
     */
    private function _issub($content, $substr)
    {
        return stripos($this->_space($content), $this->_space($substr)) !== false;
    }

}
