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

use OSS\Core\OssException;
use OSS\Model\CorsConfig;
use OSS\Model\CorsRule;
use OSS\OssClient;
use sveil\Exception;
use sveil\facade\Log;
use sveil\facade\Request;
use sveil\File;

/**
 * Class Oss
 * Alibaba Cloud file storage
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\storage
 */
class Oss extends File
{
    /**
     * Check if the file already exists
     * @param string $name file name
     * @return boolean
     * @throws OssException
     */
    public function has($name)
    {
        $bucket = self::$config->get('storage_oss_bucket');

        return $this->getOssClient()->doesObjectExist($bucket, $name);
    }

    /**
     * Read file content from Key
     * @param string $name file name
     * @return string
     * @throws OssException
     */
    public function get($name)
    {
        $bucket = self::$config->get('storage_oss_bucket');

        return $this->getOssClient()->getObject($bucket, $name);
    }

    /**
     * Get the current URL of a file
     * @param string $name file name
     * @return boolean|string
     * @throws OssException
     * @throws Exception
     */
    public function url($name)
    {
        return $this->has($name) ? $this->base($name) : false;
    }

    /**
     * Get AliOSS upload address
     * @return string
     */
    public function upload()
    {
        $protocol = Request::isSsl() ? 'https' : 'http';

        return "{$protocol}://" . self::$config->get('storage_oss_domain');
    }

    /**
     * Obtain Alibaba Cloud object storage URL prefix
     * @param string $name file name
     * @return string
     * @throws Exception
     */
    public function base($name = '')
    {
        $domain = self::$config->get('storage_oss_domain');

        switch (strtolower(self::$config->get('storage_oss_is_https'))) {
            case 'https':
                return "https://{$domain}/{$name}";
            case 'http':
                return "http://{$domain}/{$name}";
            case 'auto':
                return "//{$domain}/{$name}";
            default:
                throw new Exception('未设置阿里云文件地址协议');
        }
    }

    /**
     * Alibaba Cloud OSS save file
     * @param string $name file name
     * @param string $content document content
     * @return array|null
     */
    public function save($name, $content)
    {
        try {
            $bucket = self::$config->get('storage_oss_bucket');
            $result = $this->getOssClient()->putObject($bucket, $name, $content);

            return ['file' => $name, 'hash' => $result['content-md5'], 'key' => $name, 'url' => $this->base($name)];
        } catch (\Exception $e) {
            Log::error("阿里云OSS文件上传失败，{$e->getMessage()}");
            return null;
        }
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
     * @throws OssException
     * @throws Exception
     */
    public function info($name)
    {
        $bucket = self::$config->get('storage_oss_bucket');
        $result = $this->getOssClient()->getObjectMeta($bucket, $name);

        if (empty($result) || !isset($result['content-md5'])) {
            return null;
        }

        return ['file' => $name, 'hash' => $result['content-md5'], 'url' => $this->base($name), 'key' => $name];
    }

    /**
     * Delete Files
     * @param string $name file name
     * @return boolean
     */
    public function remove($name)
    {
        try {
            $bucket = self::$config->get('storage_oss_bucket');
            $this->getOssClient()->deleteObject($bucket, $name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create OSS space name
     * @param string $bucket OSS space name
     * @return string Returns the newly created domain name
     * @throws OssException
     */
    public function setBucket($bucket)
    {
        $client = $this->getOssClient();
        // Space and permission handling
        $aclType = OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE;

        if ($client->doesBucketExist($bucket)) {
            $result = $client->getBucketMeta($bucket);

            if ($client->getBucketAcl($bucket) !== $aclType) {
                $client->putBucketAcl($bucket, $aclType);
            }
        } else {
            $result = $client->createBucket($bucket, $aclType);
        }

        // CORS cross-domain processing
        $corsRule = new CorsRule();
        $corsRule->addAllowedHeader('*');
        $corsRule->addAllowedOrigin('*');
        $corsRule->addAllowedMethod('GET');
        $corsRule->addAllowedMethod('POST');
        $corsRule->setMaxAgeSeconds(36000);
        $corsConfig = new CorsConfig();
        $corsConfig->addRule($corsRule);
        $client->putBucketCors($bucket, $corsConfig);

        return pathinfo($result['oss-request-url'], PATHINFO_BASENAME);
    }

    /**
     * Get OssClient object
     * @return OssClient
     * @throws OssException
     */
    private function getOssClient()
    {
        $keyid    = self::$config->get('storage_oss_keyid');
        $secret   = self::$config->get('storage_oss_secret');
        $endpoint = 'http://' . self::$config->get('storage_oss_endpoint');

        return new OssClient($keyid, $secret, $endpoint);
    }
}
