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

use sveil\Db;
use sveil\Exception;
use sveil\exception\PDOException;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\service\Wechat;

/**
 * Class WechatHandler
 * WeChat web page authorization interface
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\common\handler
 */
class WechatHandler
{
    /**
     * Current WeChat APPID
     * @var string
     */
    protected $appid;

    /**
     * Current WeChat configuration
     * @var array
     */
    protected $config;

    /**
     * Wrong information
     * @var string
     */
    protected $message;

    /**
     * Wechat constructor
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
        $this->appid  = isset($config['authorizer_appid']) ? $config['authorizer_appid'] : '';
    }

    /**
     * Check WeChat configuration service initialization status
     * @return boolean
     * @throws Exception
     */
    private function checkInit()
    {
        if (!empty($this->config)) {
            return true;
        }

        throw new Exception('Wechat Please bind Wechat first');
    }

    /**
     * Get current Weopen configuration
     * @return array|boolean
     * @throws Exception
     */
    public function getConfig()
    {
        $this->checkInit();
        $info = Db::name('WechatServiceConfig')->where(['authorizer_appid' => $this->appid])->find();

        if (empty($info)) {
            return false;
        }

        if (isset($info['id'])) {
            unset($info['id']);
        }

        return $info;
    }

    /**
     * Set the WeChat interface notification URL
     * @param string $notifyUri Connection notification URL
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    public function setApiNotifyUri($notifyUri)
    {
        $this->checkInit();

        if (empty($notifyUri)) {
            throw new Exception('请传入微信通知URL');
        }

        list($where, $data) = [['authorizer_appid' => $this->appid], ['appuri' => $notifyUri]];

        return Db::name('WechatServiceConfig')->where($where)->update($data) !== false;
    }

    /**
     * Update interface Appkey (return new Appkey successfully)
     * @return bool|string
     * @throws Exception
     * @throws PDOException
     */
    public function updateApiAppkey()
    {
        $this->checkInit();
        $data = ['appkey' => md5(uniqid())];
        Db::name('WechatServiceConfig')->where(['authorizer_appid' => $this->appid])->update($data);

        return $data['appkey'];
    }

    /**
     * Get the configuration parameters of WeOpen
     * @param string $name parameter name
     * @return array|string
     * @throws Exception
     */
    public function config($name = null)
    {
        $this->checkInit();

        return Wechat::WeChatScript($this->appid)->config->get($name);
    }

    /**
     * WeChat web authorization
     * @param string $sessid Current session id (available with session_id ())
     * @param string $selfUrl Current session URL (need to include the full URL)
     * @param int $fullMode Web authorization mode (0 silent mode, 1 advanced authorization)
     * @return array|bool
     * @throws Exception
     */
    public function oauth($sessid, $selfUrl, $fullMode = 0)
    {
        $this->checkInit();
        $fans   = cache("{$this->appid}_{$sessid}_fans");
        $openid = cache("{$this->appid}_{$sessid}_openid");

        if (!empty($openid) && (empty($fullMode) || !empty($fans))) {
            return ['openid' => $openid, 'fans' => $fans, 'url' => ''];
        }

        $service = Wechat::service();
        $mode    = empty($fullMode) ? 'snsapi_base' : 'snsapi_userinfo';
        $url     = url('@service/api.push/oauth', '', true, true);
        $params  = ['mode' => $fullMode, 'sessid' => $sessid, 'enurl' => encode($selfUrl)];
        $authurl = $service->getOauthRedirect($this->appid, $url . '?' . http_build_query($params), $mode);

        return ['openid' => $openid, 'fans' => $fans, 'url' => $authurl];
    }

    /**
     * WeChat web JS signature
     * @param string $url Current session URL (need to include the full URL)
     * @return array|boolean
     * @throws InvalidResponseException
     * @throws LocalCacheException
     * @throws Exception
     */
    public function jsSign($url)
    {
        $this->checkInit();

        return Wechat::WeChatScript($this->appid)->getJsSign($url);
    }
}
