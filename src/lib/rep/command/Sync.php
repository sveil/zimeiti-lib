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

namespace sveil\lib\rep\command;

use sveil\console\Command;
use sveil\console\Input;
use sveil\console\Output;

/**
 * File comparison and synchronization support
 *
 * Class Sync
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command
 */
class Sync extends Command
{

    /**
     * Base URL
     * @var string
     */
    protected $uri;

    /**
     * Current Admin version number
     * @var string
     */
    protected $version;

    /**
     * Specify update module
     * @var array
     */
    protected $modules = [];

    /**
     * Sync constructor
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->version = config('app.zimeiti_ver');
        if (empty($this->version)) {
            $this->version = 'v1';
        }

        $this->uri = "https://{$this->version}.thinkadmin.top";
        parent::__construct($name);
    }

    /**
     * Perform update operation
     *
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $files = [];
        $output->comment("=== 准备从代码仓库下载更新{$this->version}版本文件 ===");
        foreach ($this->getDiff() as $file) {
            if (in_array($file['type'], ['add', 'del', 'mod'])) {
                foreach ($this->modules as $module) {
                    if (stripos($file['name'], $module) === 0) {
                        $files[] = $file;
                    }
                }

            }
        }

        if (empty($files)) {
            $output->info('--- 本地文件与线上文件一致，无需更新文件');
        } else {
            foreach ($files as $file) {
                $this->syncFile($file, $output);
            }
        }

        $output->comment("=== 从代码仓库下载{$this->version}版本同步更新成功 ===");
    }

    /**
     * Get the current system file list
     * @return array
     */
    public function build()
    {
        return $this->tree([
            'sveil', 'config/log.php', 'config/cookie.php', 'config/template.php',
            'application/admin', 'application/wechat', 'application/service',
            'public/static/plugs', 'public/static/theme', 'public/static/admin.js', 'public/static/login.js',
        ]);
    }

    /**
     * Get file information list
     *
     * @param array $paths Directory to be scanned
     * @param array $ignores Ignore scanned files
     * @param array $maps Scan result list
     * @return array
     */
    public function tree(array $paths, array $ignores = [], array $maps = [])
    {
        $root = str_replace('\\', '/', env('root_path'));
        foreach ($paths as $key => $dir) {
            $paths[$key] = str_replace('\\', '/', $dir);
            $maps        = array_merge($maps, $this->scanDir("{$root}{$paths[$key]}", $root));
        }
        // Clear ignored files
        foreach ($maps as $key => $map) {
            foreach ($ignores as $ingore) {
                if (stripos($map['name'], $ingore) === 0) {
                    unset($maps[$key]);
                }

            }
        }

        return ['paths' => $paths, 'ignores' => $ignores, 'list' => $maps];
    }

    /**
     * Synchronize all difference files
     */
    public function sync()
    {
        foreach ($this->getDiff() as $file) {
            $this->syncFile($file, new Output());
        }
    }

    /**
     * Synchronize specified files
     *
     * @param array $file
     * @param Output $output
     */
    private function syncFile($file, $output)
    {
        if (in_array($file['type'], ['add', 'mod'])) {
            if ($this->runDown(encode($file['name']))) {
                $output->writeln("--- 下载 {$file['name']} 更新成功");
            } else {
                $output->error("--- 下载 {$file['name']} 更新失败");
            }
        } elseif (in_array($file['type'], ['del'])) {
            $real = realpath(env('root_path') . $file['name']);
            if (is_file($real) && unlink($real)) {
                $this->delEmptyDir(dirname($real));
                $output->writeln("--- 删除 {$file['name']} 文件成功");
            } else {
                $output->error("--- 删除 {$file['name']} 文件失败");
            }
        }
    }

    /**
     * Clean the specified empty directory
     *
     * @param string $dir
     */
    private function delEmptyDir($dir)
    {
        if (is_dir($dir) && count(scandir($dir)) === 2) {
            if (rmdir($dir)) {
                $this->delEmptyDir(dirname($dir));
            }

        }
    }

    /**
     * Comparison of two two-dimensional arrays
     *
     * @param array $serve Online file list information
     * @param array $local Local file list information
     * @return array
     */
    private function contrast(array $serve = [], array $local = [])
    {
        // Data flattening
        list($_serve, $_local, $_new) = [[], [], []];

        foreach ($serve as $t) {
            $_serve[$t['name']] = $t;
        }

        foreach ($local as $t) {
            $_local[$t['name']] = $t;
        }

        unset($serve, $local);

        // Online data difference calculation
        foreach ($_serve as $t) {
            if (isset($_local[$t['name']])) {
                array_push($_new, [
                    'type' => $t['hash'] === $_local[$t['name']]['hash'] ? null : 'mod', 'name' => $t['name'],
                ]);
            } else {
                array_push($_new, ['type' => 'add', 'name' => $t['name']]);
            }
        }

        // Local data incremental calculation
        foreach ($_local as $t) {
            if (!isset($_serve[$t['name']])) {
                array_push($_new, ['type' => 'del', 'name' => $t['name']]);
            }
        }

        unset($_serve, $_local);
        usort($_new, function ($a, $b) {
            return $a['name'] !== $b['name'] ? ($a['name'] > $b['name'] ? 1 : -1) : 0;
        });
        return $_new;
    }

    /**
     * Download the updated file content
     *
     * @param string $encode
     * @return boolean|integer
     */
    private function runDown($encode)
    {
        $result = json_decode(http_get("{$this->uri}?s=admin/api.update/read/{$encode}"), true);
        if (empty($result['code'])) {
            return false;
        }

        $pathname = env('root_path') . decode($encode);
        file_exists(dirname($pathname)) || mkdir(dirname($pathname), 0755, true);
        return file_put_contents($pathname, base64_decode($result['data']['content']));
    }

    /**
     * Get file difference data
     *
     * @return array
     */
    private function getDiff()
    {
        $result = json_decode(http_get("{$this->uri}?s=/admin/api.update/tree"), true);
        if (empty($result['code'])) {
            return [];
        }

        $new = $this->tree($result['data']['paths'], $result['data']['ignores']);
        return $this->contrast($result['data']['list'], $new['list']);
    }

    /**
     * Get list of catalog files
     *
     * @param string $dir Directory to be scanned
     * @param string $root Application root
     * @param array $data Scan results
     * @return array
     */
    private function scanDir($dir, $root = '', $data = [])
    {
        if (file_exists($dir) && is_file($dir)) {
            return [$this->getFileInfo($dir, $root)];
        }
        if (file_exists($dir)) {
            foreach (scandir($dir) as $sub) {
                if (strpos($sub, '.') !== 0) {
                    if (is_dir($temp = "{$dir}/{$sub}")) {
                        $data = array_merge($data, $this->scanDir($temp, $root));
                    } else {
                        array_push($data, $this->getFileInfo($temp, $root));
                    }

                }
            }
        }

        return $data;
    }

    /**
     * Get specific file information
     *
     * @param string $file Absolute file name
     * @param string $root
     * @return array
     */
    private function getFileInfo($file, $root)
    {

        return [
            'hash' => md5(preg_replace('/\s{1,}/', '', file_get_contents($file))),
            'name' => str_replace($root, '', str_replace('\\', '/', realpath($file))),
        ];

    }

}
