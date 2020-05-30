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
 * Member management
 *
 * Class Tester
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Tester extends WeChat
{

    /**
     * 1. Bind WeChat users as applet experiencers
     *
     * @param string $testid Wechat number
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function bindTester($testid)
    {

        $url = 'https://api.weixin.qq.com/wxa/bind_tester?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['wechatid' => $testid], true);
    }

    /**
     * 2. Experiencers who unbind applets
     *
     * @param string $testid Wechat number
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function unbindTester($testid)
    {

        $url = 'https://api.weixin.qq.com/wxa/unbind_tester?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['wechatid' => $testid], true);
    }

    /**
     * 3. Get a list of experiencers
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTesterList()
    {

        $url = 'https://api.weixin.qq.com/wxa/memberauth?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['action' => 'get_experiencer'], true);
    }

}
