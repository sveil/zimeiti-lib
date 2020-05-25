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

namespace sveil\rep\storage;

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use sveil\File;
use think\Exception;
use think\facade\Log;
use think\facade\Request;

/**
 * Class Qiniu
 * Seven Niu Cloud File Storage
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\storage
 */
class Qiniu extends File
{
    /**
     * Check if the file already exists
     * @param string $name file name
     * @return boolean
     * @throws Exception
     */
    public function has($name)
    {
        return is_array($this->info($name));
    }

    /**
     * Read file content from Key
     * @param string $name file name
     * @return string
     * @throws Exception
     */
    public function get($name)
    {
        return file_get_contents($this->getAuth()->privateDownloadUrl($this->base($name)));
    }

    /**
     * Get the current URL of a file
     * @param string $name file name
     * @return boolean|string|null
     * @throws Exception
     */
    public function url($name)
    {
        return $this->has($name) ? $this->base($name) : false;
    }

    /**
     * Calculate the target address of the file upload of Seven Niuyun according to the request
     * @param boolean $client
     * @return string
     * @throws Exception
     */
    public function upload($client = false)
    {
        $protocol = Request::isSsl() ? 'https' : 'http';

        switch (self::$config->get('storage_qiniu_region')) {
            case '华东':
                return $client ? "{$protocol}://up.qiniup.com" : "{$protocol}://upload.qiniup.com";
            case '华北':
                return $client ? "{$protocol}://up-z1.qiniup.com" : "{$protocol}://upload-z1.qiniup.com";
            case '北美':
                return $client ? "{$protocol}://up-na0.qiniup.com" : "{$protocol}://upload-na0.qiniup.com";
            case '华南':
                return $client ? "{$protocol}://up-z2.qiniup.com" : "{$protocol}://upload-z2.qiniup.com";
            case "东南亚":
                return $client ? "{$protocol}://up-as0.qiniup.com" : "{$protocol}://upload-as0.qiniup.com";
            default:
                throw new Exception('未配置七牛云存储区域');
        }
    }

    /**
     * Get Seven Niuyun URL prefix
     * @param string $name file name
     * @return string
     * @throws Exception
     */
    public function base($name = '')
    {
        $domain = self::$config->get('storage_qiniu_domain');

        switch (strtolower(self::$config->get('storage_qiniu_is_https'))) {
            case 'https':
                return "https://{$domain}/{$name}";
            case 'http':
                return "http://{$domain}/{$name}";
            case 'auto':
                return "//{$domain}/{$name}";
            default:
                throw new Exception('未配置七牛云URL前缀');
        }
    }

    /**
     * Seven Niu cloud storage file
     * @param string $name file name
     * @param string $content document content
     * @return array|null
     * @throws Exception
     */
    public function save($name, $content)
    {
        $bucket          = self::$config->get('storage_qiniu_bucket');
        $token           = $this->getAuth()->uploadToken($bucket);
        list($ret, $err) = (new UploadManager())->put($token, $name, $content);

        if ($err !== null) {
            Log::error(__METHOD__ . " 七牛云文件上传失败，{$err->message()}");
        }

        return $this->info($name);
    }

    /**
     * Get file path
     * @param string $name file name
     * @return string
     */
    public function path($name)
    {
        return $name;
    }

    /**
     * Get file information
     * @param string $name file name
     * @return array|null
     * @throws Exception
     */
    public function info($name)
    {
        $manager         = new BucketManager($this->getAuth());
        $bucket          = self::$config->get('storage_qiniu_bucket');
        list($ret, $err) = $manager->stat($bucket, $name);

        if ($err !== null) {
            return null;
        }

        return ['file' => $name, 'hash' => $ret['hash'], 'url' => $this->base($name), 'key' => $name];
    }

    /**
     * Delete Files
     * @param string $name file name
     * @return boolean
     */
    public function remove($name)
    {
        $bucket = self::$config->get('storage_qiniu_bucket');
        $err    = (new BucketManager($this->getAuth()))->delete($bucket, $name);

        return empty($err);
    }

    /**
     * Get space list
     * @return string
     * @throws \Exception
     */
    public function getBucketList()
    {
        list($list, $err) = (new BucketManager($this->getAuth()))->buckets(true);

        if (!empty($err)) {
            throw new \Exception($err);
        }

        foreach ($list as &$bucket) {
            list($domain, $err) = $this->getDomainList($bucket);
            if (empty($err)) {
                $bucket = ['bucket' => $bucket, 'domain' => $domain];
            } else {
                throw new \Exception($err);
            }
        }

        return $list;
    }

    /**
     * Get a list of domain names bound to the space
     * @param string $bucket Space name
     * @return array
     */
    public function getDomainList($bucket)
    {
        return (new BucketManager($this->getAuth()))->domains($bucket);
    }

    /**
     * Get interface Auth object
     * @return Auth
     */
    private function getAuth()
    {
        return new Auth(
            self::$config->get('storage_qiniu_access_key'),
            self::$config->get('storage_qiniu_secret_key')
        );
    }

    /**
     * Generate file upload TOKEN
     * @param null|string $key Specify save name
     * @param integer $expires Specify token validity time
     * @return string
     * @throws Exception
     */
    public function buildUploadToken($key = null, $expires = 3600)
    {
        $location = $this->base();
        $bucket   = self::$config->get('storage_qiniu_bucket');
        $policy   = ['returnBody' => '{"uploaded":true,"filename":"$(key)","url":"' . $location . '$(key)"}'];

        return $this->getAuth()->uploadToken($bucket, $key, $expires, $policy, true);
    }
}
