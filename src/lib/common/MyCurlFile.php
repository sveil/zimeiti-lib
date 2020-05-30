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

namespace sveil\lib\common;

use sveil\lib\exception\LocalCacheException;

/**
 * Custom CURL file class
 *
 * Class MyCurlFile
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class MyCurlFile extends \stdClass
{

    /**
     * Current data type
     * @var string
     */
    public $datatype = 'MY_CURL_FILE';

    /**
     * MyCurlFile constructor
     *
     * @param string|array $filename
     * @param string $mimetype
     * @param string $postname
     * @throws LocalCacheException
     */
    public function __construct($filename, $mimetype = '', $postname = '')
    {

        if (is_array($filename)) {
            foreach ($filename as $k => $v) {
                $this->{$k} = $v;
            }
        } else {
            $this->mimetype  = $mimetype;
            $this->postname  = $postname;
            $this->extension = pathinfo($filename, PATHINFO_EXTENSION);

            if (empty($this->extension)) {
                $this->extension = 'tmp';
            }

            if (empty($this->mimetype)) {
                $this->mimetype = Tools::getExtMine($this->extension);
            }

            if (empty($this->postname)) {
                $this->postname = pathinfo($filename, PATHINFO_BASENAME);
            }

            $this->content  = base64_encode(file_get_contents($filename));
            $this->tempname = md5($this->content) . ".{$this->extension}";
        }

    }

    /**
     * Get file information
     * @return \CURLFile|string
     * @throws LocalCacheException
     */
    public function get()
    {

        $this->filename = Tools::pushFile($this->tempname, base64_decode($this->content));

        if (class_exists('CURLFile')) {
            return new \CURLFile($this->filename, $this->mimetype, $this->postname);
        }

        return "@{$this->tempname};filename={$this->postname};type={$this->mimetype}";
    }

    /**
     * Class destroy processing
     */
    public function __destruct()
    {
        // Tools::delCache($this->tempname);
    }

}
