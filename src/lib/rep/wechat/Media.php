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

namespace sveil\lib\rep\wechat;

use sveil\lib\common\Tools;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WeChat;

/**
 * WeChat material management
 *
 * Class Media
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Media extends WeChat
{

    /**
     * Add temporary material
     *
     * @param string $filename file name
     * @param string $type Media file type(image|voice|video|thumb)
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function add($filename, $type = 'image')
    {
        if (!in_array($type, ['image', 'voice', 'video', 'thumb'])) {
            throw new InvalidResponseException('Invalid Media Type.', '0');
        }
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type={$type}";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, ['media' => Tools::createCurlFile($filename)], false);
    }

    /**
     * Get temporary material
     *
     * @param string $media_id
     * @param string $outType Return handler
     * @return array|string
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function get($media_id, $outType = null)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id={$media_id}";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $result = Tools::get($url);
        if (is_array($json = json_decode($result, true))) {
            if (!$this->isTry && isset($json['errcode']) && in_array($json['errcode'], ['40014', '40001', '41001', '42001'])) {
                [$this->delAccessToken(), $this->isTry = true];
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }
            return Tools::json2arr($result);
        }
        return is_null($outType) ? $result : $outType($result);
    }

    /**
     * New graphic material
     *
     * @param array $data file name
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function addNews($data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, $data);
    }

    /**
     * Update graphic material
     *
     * @param string $media_id The id of the graphic message to be modified
     * @param int $index Position of the article to be updated in the graphic message（This field is meaningful when there are multiple
     * text messages）, The first article is 0
     * @param array $news Article content
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function updateNews($media_id, $index, $news)
    {
        $data = ['media_id' => $media_id, 'index' => $index, 'articles' => $news];
        $url  = "https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, $data);
    }

    /**
     * Upload URL of the picture in the graphic message to get the URL
     *
     * @param string $filename
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function uploadImg($filename)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, ['media' => Tools::createCurlFile($filename)], false);
    }

    /**
     * Add other types of permanent material
     *
     * @param string $filename file name
     * @param string $type Media file type(image|voice|video|thumb)
     * @param array $description Contains descriptive information of the material
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function addMaterial($filename, $type = 'image', $description = [])
    {
        if (!in_array($type, ['image', 'voice', 'video', 'thumb'])) {
            throw new InvalidResponseException('Invalid Media Type.', '0');
        }
        $url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=ACCESS_TOKEN&type={$type}";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, ['media' => Tools::createCurlFile($filename), 'description' => Tools::arr2json($description)], false);
    }

    /**
     * Get permanent material
     *
     * @param string $media_id
     * @param null|string $outType Output type
     * @return array|string
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function getMaterial($media_id, $outType = null)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $result = Tools::post($url, ['media_id' => $media_id]);
        if (is_array($json = json_decode($result, true))) {
            if (!$this->isTry && isset($json['errcode']) && in_array($json['errcode'], ['40014', '40001', '41001', '42001'])) {
                [$this->delAccessToken(), $this->isTry = true];
                return call_user_func_array([$this, $this->currentMethod['method']], $this->currentMethod['arguments']);
            }
            return Tools::json2arr($result);
        }
        return is_null($outType) ? $result : $outType($result);
    }

    /**
     * Delete permanent material
     *
     * @param string $media_id
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function delMaterial($media_id)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, ['media_id' => $media_id]);
    }

    /**
     * Get the total number of materials
     *
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function getMaterialCount()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpGetForJson($url);
    }

    /**
     * Get material list
     *
     * @param string $type
     * @param int $offset
     * @param int $count
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function batchGetMaterial($type = 'image', $offset = 0, $count = 20)
    {
        if (!in_array($type, ['image', 'voice', 'video', 'news'])) {
            throw new InvalidResponseException('Invalid Media Type.', '0');
        }
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpPostForJson($url, ['type' => $type, 'offset' => $offset, 'count' => $count]);
    }

}
