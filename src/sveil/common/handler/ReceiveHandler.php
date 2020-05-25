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

namespace app\common\handler;

use sveil\service\Wechat;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\facade\Log;

/**
 * Class ReceiveHandler
 * WeChat push message processing
 * @author Richard <richard@sveil.com>
 * @package app\wechat\controller
 */
class ReceiveHandler
{
    /**
     * Event initialization
     * @param string $appid
     * @return string
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws Exception
     */
    public static function handler($appid)
    {
        try {
            $wechat = Wechat::WeChatReceive($appid);
        } catch (\Exception $e) {
            return "Wechat message handling failed, {$e->getMessage()}";
        }

        // Verify WeChat configuration information
        $config = Db::name('WechatServiceConfig')->where(['authorizer_appid' => $appid])->find();

        if (empty($config) || empty($config['appuri'])) {
            Log::error(($message = "微信{$appid}授权配置验证无效"));
            return $message;
        }

        try {
            list($data, $openid) = [$wechat->getReceive(), $wechat->getOpenid()];

            if (isset($data['EventKey']) && is_object($data['EventKey'])) {
                $data['EventKey'] = (array) $data['EventKey'];
            }

            $input = ['openid' => $openid, 'appid' => $appid, 'receive' => serialize($data), 'encrypt' => intval($wechat->isEncrypt())];

            if (is_string($result = http_post($config['appuri'], $input, ['timeout' => 30]))) {
                if (is_array($json = json_decode($result, true))) {
                    return $wechat->reply($json, true, $wechat->isEncrypt());
                } else {
                    return $result;
                }
            }
        } catch (\Exception $e) {
            Log::error("微信{$appid}接口调用异常，{$e->getMessage()}");
        }

        return 'success';
    }
}
