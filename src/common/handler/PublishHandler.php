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

namespace app\common\handler;

use sveil\exception\InvalidDecryptException;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\service\Wechat;

/**
 * usage platform test goes live
 *
 * Class PublishHandler
 * @author Richard <richard@sveil.com>
 * @package app\common\handler
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
