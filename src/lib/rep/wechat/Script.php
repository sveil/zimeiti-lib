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
 * WeChat front-end support
 *
 * Class Script
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Script extends WeChat
{

    /**
     * Remove JSAPI authorization TICKET
     *
     * @param string $type TICKET type(wx_card|jsapi)
     * @param string $appid Force to specify a valid APPID
     * @return void
     */
    public function delTicket($type = 'jsapi', $appid = null)
    {

        is_null($appid) && $appid = $this->config->get('appid');
        $cache_name               = "{$appid}_ticket_{$type}";

        Tools::delCache($cache_name);
    }

    /**
     * Get JSAPI_TICKET interface
     *
     * @param string $type TICKET type(wx_card|jsapi)
     * @param string $appid Force to specify a valid APPID
     * @return string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getTicket($type = 'jsapi', $appid = null)
    {

        is_null($appid) && $appid = $this->config->get('appid');
        $cache_name               = "{$appid}_ticket_{$type}";
        $ticket                   = Tools::getCache($cache_name);

        if (empty($ticket)) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type={$type}";
            $this->registerApi($url, __FUNCTION__, func_get_args());
            $result = $this->httpGetForJson($url);
            if (empty($result['ticket'])) {
                throw new InvalidResponseException('Invalid Resoponse Ticket.', '0');
            }
            $ticket = $result['ticket'];
            Tools::setCache($cache_name, $ticket, 5000);
        }

        return $ticket;
    }

    /**
     * Get JsApi usage signature
     *
     * @param string $url URL of web page
     * @param string $appid Used for multiple appid(Nullable)
     * @param string $ticket Mandatory designation ticket
     * @return array
     * @throws LocalCacheException
     * @throws InvalidResponseException
     */
    public function getJsSign($url, $appid = null, $ticket = null)
    {

        list($url)                  = explode('#', $url);
        is_null($ticket) && $ticket = $this->getTicket('jsapi');
        is_null($appid) && $appid   = $this->config->get('appid');
        $data                       = ["url" => $url, "timestamp" => '' . time(), "jsapi_ticket" => $ticket, "noncestr" => Tools::createNoncestr(16)];

        return [
            'debug'     => false,
            "appId"     => $appid,
            "nonceStr"  => $data['noncestr'],
            "timestamp" => $data['timestamp'],
            "signature" => $this->getSignature($data, 'sha1'),
            'jsApiList' => [
                'updateAppMessageShareData', 'updateTimelineShareData', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone',
                'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice',
                'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'translateVoice', 'getNetworkType', 'openLocation', 'getLocation',
                'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem',
                'closeWindow', 'scanQRCode', 'chooseWXPay', 'openProductSpecificView', 'addCard', 'chooseCard', 'openCard',
            ],
        ];
    }

    /**
     * Data generation signature
     *
     * @param array $data Signature array
     * @param string $method Signature method
     * @param array $params Signature parameters
     * @return bool|string Signature value
     */
    protected function getSignature($data, $method = "sha1", $params = [])
    {

        ksort($data);

        if (!function_exists($method)) {
            return false;
        }

        foreach ($data as $k => $v) {
            array_push($params, "{$k}={$v}");
        }

        return $method(join('&', $params));
    }

}
