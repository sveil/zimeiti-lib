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

namespace sveil\lib\common\handler;

use sveil\lib\exception\InvalidDecryptException;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\service\Wechat;

/**
 * Class PublishHandler
 * usage platform test goes live
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\common\handler
 */
class PublishHandler
{
    /**
     * Current WeChat APPID
     * @var string
     */
    protected static $appid;

    /**
     * Event initialization
     *
     * @param string $appid
     * @return string
     * @throws InvalidDecryptException
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public static function handler($appid)
    {

        try {
            $wechat = Wechat::WeChatReceive($appid);
        } catch (\Exception $e) {
            return "Wechat message handling failed, {$e->getMessage()}";
        }

        // Perform the corresponding type of operation separately
        switch (strtolower($wechat->getMsgType())) {
            case 'text':
                $receive = $wechat->getReceive();
                if ($receive['Content'] === 'TESTCOMPONENT_MSG_TYPE_TEXT') {
                    return $wechat->text('TESTCOMPONENT_MSG_TYPE_TEXT_callback')->reply([], true);
                } else {
                    $key = str_replace("QUERY_AUTH_CODE:", '', $receive['Content']);
                    Wechat::service()->getQueryAuthorizerInfo($key);
                    return $wechat->text("{$key}_from_api")->reply([], true);
                }
            case 'event':
                $receive = $wechat->getReceive();
                return $wechat->text("{$receive['Event']}from_callback")->reply([], true);
            default:
                return 'success';
        }

    }
}
