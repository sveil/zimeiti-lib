<?php

// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-lib
// | github：https://github.com/sveil/zimeiti-lib
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\wemini;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Applets shopping guide assistant
 *
 * Class Guide
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Guide extends WeChat
{

    /**
     * Service number add shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addGuideAcct($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/addguideacct?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Service number delete shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delGuideAcct($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/delguideacct?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Service guide to get shopping guide information
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideAcct($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguideacct?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Obtain sensitive word information and automatic reply information of service number
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideAcctConfig()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguideacctconfig?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, [], true);
    }

    /**
     * Service number pull shopping guide list
     *
     * @param integer $page
     * @param integer $num
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideAcctList($page = 0, $num = 10)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguideacctconfig?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['page' => $page, 'num' => $num], true);
    }

    /**
     * Get shopping guide chat history
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideBuyerChatRecord($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguideacct?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Get quick reply information for shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideConfig($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguideconfig?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Generate QR code for shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function guideCreateQrCode($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/guidecreateqrcode?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Push show Wechat a path menu
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function pushShowWxaPathMenu($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/pushshowwxapathmenu?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Set sensitive words and automatic reply for service number
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setGuideAcctConfig($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/setguideacctconfig?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Set up quick response to shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setGuideConfig($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/setguideconfig?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Update shopping guide nickname or avatar
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateGuideAcct($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/setguideconfig?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Add display label information
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addGuideBuyerDisplayTag($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/addguidebuyerdisplaytag?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Add queryable tags for fans
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addGuideBuyerTag($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/addguidebuyertag?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Add tag optional value
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addGuideTagOption($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/addguidetagoption?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Remove fans tag
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delGuideBuyerTag($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/delguidebuyertag?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Query display tag information
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideBuyerDisplayTag($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidebuyerdisplaytag?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Query fans tags
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideBuyerTag($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidebuyertag?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Query label optional value information
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideTagOption()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidetagoption?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, [], true);
    }

    /**
     * New queryable tag type, support to create 4 new types of queryable tags
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function newGuideTagOption($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/newguidetagoption?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Filter fans based on tag value
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryGuideBuyerByTag($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/queryguidebuyerbytag?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Add fans for service account shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addGuideBuyerRelation($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/addguidebuyerrelation?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Remove fans from shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delGuideBuyerRelation($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/delguidebuyerrelation?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Query the binding relationship between a certain fan and shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideBuyerRelation($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidebuyerrelation?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Query the binding relationship between the fan and the shopping guide through the fan information
     *
     * @param string $openid
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideBuyerRelationByBuyer($openid)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidebuyerrelation?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['openid' => $openid], true);
    }

    /**
     * Pull the list of fans of shopping guide
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideBuyerRelationList($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidebuyerrelationlist?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Move fans from one shopping guide to another
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function rebindGuideAcctForBuyer($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/rebindguideacctforbuyer?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Update fan nickname
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateGuideBuyerRelation($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/updateguidebuyerrelation?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Delete applet card material
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delGuideCardMaterial($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/delguidecardmaterial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Delete picture material
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delGuideImageMaterial($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/delguideimagematerial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Delete text material
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delGuideWordMaterial($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/delguidewordmaterial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Get Mini Program Card Material Information
     *
     * @param integer $type
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideCardMaterial($type = 0)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidecardmaterial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['type' => $type], true);
    }

    /**
     * Get image material information
     *
     * @param integer $type Operation type
     * @param integer $start Pagination query, starting position
     * @param integer $num Pagination query, query number
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideImageMaterial($type = 0, $start = 0, $num = 10)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguideimagematerial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['type' => $type, 'start' => $start, 'num' => $num], true);
    }

    /**
     * Get text material information
     *
     * @param integer $type Operation type
     * @param integer $start Pagination query, starting position
     * @param integer $num Pagination query, query number
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGuideWordMaterial($type = 0, $start = 0, $num = 10)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/getguidewordmaterial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['type' => $type, 'start' => $start, 'num' => $num], true);
    }

    /**
     * Add Mini Program Card Material
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setGuideCardMaterial($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/setguidecardmaterial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Add picture material
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setGuideImageMaterial($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/setguideimagematerial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

    /**
     * Add text material for service number
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setGuideWordMaterial($data)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/guide/setguidewordmaterial?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

}
