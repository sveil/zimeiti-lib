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

namespace sveil\lib\rep\wechat\wemini;

use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WeChat;

/**
 * Applet image processing
 *
 * Class Image
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Image extends WeChat
{

    /**
     * This interface provides the ability to intelligently crop pictures based on applets
     *
     * @param string $img_url To detect the image url, pass this without passing the img parameter.
     * @param string $img The media file identifier in form-data, with information such as filename, filelength, content-type, etc.
     * It is not necessary to wear img_url
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function aiCrop($img_url, $img)
    {

        $url = "https://api.weixin.qq.com/cv/img/aicrop?access_token=ACCESS_TOCKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['img_url' => $img_url, 'img' => $img], true);
    }

    /**
     * This interface provides an API for barcode / QR code recognition based on applets
     *
     * @param string $img_url To detect the image url, pass this without passing the img parameter.
     * @param string $img The media file identifier in form-data, with information such as filename, filelength, content-type, etc.
     * It is not necessary to wear img_url
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function scanQRCode($img_url, $img)
    {

        $url = "https://api.weixin.qq.com/cv/img/qrcode?img_url=ENCODE_URL&access_token=ACCESS_TOCKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['img_url' => $img_url, 'img' => $img], true);
    }

    /**
     * This interface provides high-definition pictures based on small programs
     *
     * @param string $img_url To detect the image url, pass this without passing the img parameter.
     * @param string $img The media file identifier in form-data, with information such as filename, filelength, content-type, etc.
     * It is not necessary to wear img_url
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function superresolution($img_url, $img)
    {

        $url = "https://api.weixin.qq.com/cv/img/qrcode?img_url=ENCODE_URL&access_token=ACCESS_TOCKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['img_url' => $img_url, 'img' => $img], true);
    }

}
