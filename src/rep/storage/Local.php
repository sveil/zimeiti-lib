<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
// +----------------------------------------------------------------------

namespace sveil\rep\storage;

use sveil\File;

/**
 * Local file storage
 *
 * Class Local
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\storage
 */
class Local extends File
{

    /**
     * Check if the file already exists
     *
     * @param string $name file name
     * @param boolean $safe Safe Mode
     * @return boolean
     */
    public function has($name, $safe = false)
    {
        return file_exists($this->path($name, $safe));
    }

    /**
     * Read file content from Key
     *
     * @param string $name file name
     * @param boolean $safe Safe Mode
     * @return string
     */
    public function get($name, $safe = false)
    {
        if (!$this->has($name, $safe)) {
            return '';
        }

        return file_get_contents($this->path($name, $safe));
    }

    /**
     * Get the current URL of a file
     *
     * @param string $name file name
     * @param boolean $safe Safe Mode
     * @return boolean|string|null
     */
    public function url($name, $safe = false)
    {
        if ($safe) {
            return null;
        }

        return $this->has($name) ? $this->base($name) : false;
    }

    /**
     * Obtain the local upload target address according to the configuration
     *
     * @return string
     */
    public function upload()
    {
        return url('@') . '?s=admin/api.plugs/upload';
    }

    /**
     * Get server URL prefix
     *
     * @param string $name file name
     * @param boolean $safe Safe Mode
     * @return string|null
     */
    public function base($name = '', $safe = false)
    {
        if ($safe) {
            return null;
        }

        $root = rtrim(dirname(request()->basefile(true)), '\\/');
        return "{$root}/" . config('upload_dir') . "/{$name}";
    }

    /**
     * Get file path
     *
     * @param string $name file name
     * @param boolean $safe Safe Mode
     * @return string
     */
    public function path($name, $safe = false)
    {
        $path = $safe ? 'safefile' : config('upload_dir');
        return str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] . "/{$path}/{$name}");
    }

    /**
     * File is stored locally
     *
     * @param string $name file name
     * @param string $content document content
     * @param boolean $safe Safe Mode
     * @return array|null
     */
    public function save($name, $content, $safe = false)
    {
        try {
            $file = $this->path($name, $safe);
            file_exists(dirname($file)) || mkdir(dirname($file), 0755, true);
            if (file_put_contents($file, $content)) {
                return $this->info($name, $safe);
            }

        } catch (\Exception $e) {
            Log::error(__METHOD__ . " 本地文件存储失败，{$e->getMessage()}");
        }
        return null;
    }

    /**
     * Get file information
     *
     * @param string $name file name
     * @param boolean $safe Safe Mode
     * @return array|null
     */
    public function info($name, $safe = false)
    {
        if ($this->has($name, $safe) && is_string($file = $this->path($name, $safe))) {
            return ['file' => $file, 'hash' => md5_file($file), 'url' => $this->base($name), 'key' => config('upload_dir') . "/{$name}"];
        } else {
            return null;
        }
    }

    /**
     * Delete Files
     *
     * @param string $name file name
     * @param boolean $safe Safe Mode
     * @return boolean|null
     */
    public function remove($name, $safe = false)
    {
        if ($this->has($name, $safe) && is_string($file = $this->path($name, $safe))) {
            return @unlink($file);
        } else {
            return true;
        }
    }

}
