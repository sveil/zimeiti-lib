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

namespace sveil\rep\wechat\wemini;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * WeChat Applet Address Management
 *
 * Class Poi
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Poi extends WeChat
{

    /**
     * Add a place
     *
     * @param string $related_name Business Qualification Subject
     * @param string $related_credential Business qualification certificate number
     * @param string $related_address Business qualification address
     * @param string $related_proof_material Temporary material mediaid for related certification material photos
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addBearByPoi($related_name, $related_credential, $related_address, $related_proof_material)
    {

        $url = 'https://api.weixin.qq.com/wxa/addnearbypoi?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $data = [
            'related_name'    => $related_name, 'related_credential'        => $related_credential,
            'related_address' => $related_address, 'related_proof_material' => $related_proof_material,
        ];

        return $this->callPostApi($url, $data, true);
    }

    /**
     * View a list of places
     *
     * @param integer $page Start page id (counting from 1)
     * @param integer $page_rows Number of impressions per page (up to 1000)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getNearByPoiList($page = 1, $page_rows = 1000)
    {

        $url = "https://api.weixin.qq.com/wxa/getnearbypoilist?page={$page}&page_rows={$page_rows}&access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callGetApi($url);
    }

    /**
     * Delete place
     *
     * @param string $poi_id Nearby place ID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delNearByPoiList($poi_id)
    {

        $url = "https://api.weixin.qq.com/wxa/delnearbypoi?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['poi_id' => $poi_id], true);
    }

    /**
     * Show / unshow nearby applets
     *
     * @param string $poi_id Nearby place ID
     * @param string $status 0: cancel display; 1: display
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setNearByPoiShowStatus($poi_id, $status)
    {

        $url = "https://api.weixin.qq.com/wxa/setnearbypoishowstatus?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['poi_id' => $poi_id, 'status' => $status], true);
    }

}
