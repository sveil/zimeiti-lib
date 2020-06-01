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

namespace sveil\lib\service;

use sveil\lib\common\Http;
use sveil\lib\Service;
use sveil\Exception;
use sveil\exception\PDOException;

/**
 * Xiaomi Message Service
 *
 * Class Michat
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Michat extends Service
{
    const URI                 = 'https://mimc.chat.xiaomi.net';
    const BIZ_TYPE_PING       = 'PING';
    const BIZ_TYPE_POND       = 'PONG';
    const BIZ_TYPE_TEXT       = 'TEXT';
    const BIZ_TYPE_PIC_FILE   = 'PIC_FILE';
    const BIZ_TYPE_BIN_FILE   = 'BIN_FILE';
    const BIZ_TYPE_AUDIO_FILE = 'AUDIO_FILE';
    const MSG_TYPE_BASE64     = 'base64';

    /**
     * Push message content to specified account
     * @param string $from source
     * @param string $to Message target
     * @param string $message Message content
     * @return bool|string
     * @throws Exception
     * @throws PDOException
     */
    public static function push($from, $to, $message)
    {
        return self::post('/api/push/p2p/', [
            'appId'        => sysconf('michat_appid'),
            'appKey'       => sysconf('michat_appkey'),
            'appSecret'    => sysconf('michat_appsecert'),
            'fromAccount'  => $from,
            'fromResource' => $from,
            'toAccount'    => $to,
            'msg'          => base64_encode($message),
            'msgType'      => 'base64',
            'bizType'      => '',
            'isStore'      => false,
        ]);
    }

    /**
     * POST submit message data
     * @param string $api interface address
     * @param array $data Interface data
     * @return bool|string
     * @throws Exception
     */
    private static function post($api, array $data)
    {
        $result = json_decode(Http::request('post', self::URI . $api, [
            'data'    => json_encode($data, JSON_UNESCAPED_UNICODE),
            'headers' => ['Content-Type: application/json'],
        ]), true);

        if (isset($result['code']) && intval($result['code']) === 200) {
            return $result['data'];
        } else {
            throw new Exception($result['message'], $result['code']);
        }
    }
}
