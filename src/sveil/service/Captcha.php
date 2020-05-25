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

namespace sveil\service;

use sveil\Service;

/**
 * Class Captcha
 * Graphic verification code service
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Captcha extends Service
{
    private $code; // Captcha
    private $uniqid; // Unique serial number
    private $charset = 'ABCDEFGHKMNPRSTUVWXYZ23456789'; // Random factor
    private $codelen = 4; // Verification code length
    private $width   = 130; // width
    private $height  = 50; // height
    private $img; // Graphics resource handle
    private $font; // Specified font
    private $fontsize = 20; // Specify font size
    private $fontcolor; // Specify font color

    /**
     * Service initialization
     * @param array $config
     * @return static
     */
    public function initialize($config = [])
    {
        // Dynamic configuration properties
        foreach ($config as $k => $v) {
            if (isset($this->$k)) {
                $this->$k = $v;
            }
        }

        // Generate verification code serial number
        $this->uniqid = uniqid('captcha') . mt_rand(1000, 9999);
        // Generate verification code string
        $length = strlen($this->charset) - 1;

        for ($i = 0; $i < $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $length)];
        }

        // Set the font file path
        $this->font = str_replace('\\', '/', env('root_path')) . '/public/static/font/font.ttf';
        // Cache verification code string
        $this->app->cache->set($this->uniqid, $this->code, 360);

        // Returns the current object
        return $this;
    }

    /**
     * Get verification code value
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get picture content
     * @return string
     */
    public function getData()
    {
        return "data:image/png;base64,{$this->createImage()}";
    }

    /**
     * Get verification code number
     * @return string
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * Get verification code data
     * @return array
     */
    public function getAttrs()
    {
        return [
            'code'   => $this->getCode(),
            'data'   => $this->getData(),
            'uniqid' => $this->getUniqid(),
        ];
    }

    /**
     * Check the verification code is correct
     * @param string $code Value to be verified
     * @param string $uniqid Verification code number
     * @return boolean
     */
    public function check($code, $uniqid = null)
    {
        $_uni = is_string($uniqid) ? $uniqid : input('uniqid', '-');
        $_val = $this->app->cache->get($_uni, '');

        if (is_string($_val) && strtolower($_val) === strtolower($code)) {
            $this->app->cache->rm($_uni);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Output graphic verification code
     * @return string
     */
    public function __toString()
    {
        return $this->getData();
    }

    /**
     * Create captcha image
     * @return string
     */
    private function createImage()
    {
        // Generate background
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color     = imagecolorallocate($this->img, mt_rand(220, 255), mt_rand(220, 255), mt_rand(220, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);

        // Generate lines
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 50), mt_rand(0, 50), mt_rand(0, 50));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }

        // Generate snowflakes
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }

        // Generate text
        $_x = $this->width / $this->codelen;

        for ($i = 0; $i < $this->codelen; $i++) {
            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));

            if (function_exists('imagettftext')) {
                imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
            } else {
                imagestring($this->img, 15, $_x * $i + mt_rand(0, 25), mt_rand(15, 20), $this->code[$i], $this->fontcolor);
            }
        }

        ob_start();
        imagepng($this->img);
        $data = ob_get_contents();
        ob_end_clean();
        imagedestroy($this->img);

        return base64_encode($data);
    }
}
