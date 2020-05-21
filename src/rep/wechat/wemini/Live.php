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
 * Applet live broadcast interface
 *
 * Class Live
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Live extends WeChat
{

    /**
     * Get live room list
     *
     * @param integer $start Start pulling room
     * @param integer $limit Maximum number of pulls at a time
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getLiveList($start = 0, $limit = 10)
    {

        $url = 'http://api.weixin.qq.com/wxa/business/getliveinfo?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['start' => $start, 'limit' => $limit], true);
    }

    /**
     * Get playback source video
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getLiveInfo($data = [])
    {

        $url = 'http://api.weixin.qq.com/wxa/business/getliveinfo?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, $data, true);
    }

}
