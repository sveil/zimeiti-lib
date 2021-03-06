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

namespace sveil\lib\command\xtask;

use sveil\console\Command;
use sveil\console\Input;
use sveil\console\Output;
use sveil\Db;
use sveil\Exception;
use sveil\exception\PDOException;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\service\Fans as FansService;
use sveil\lib\service\Wechat;

/**
 * Class Fans
 * WeChat fans management
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\command\xtask
 */
class Fans extends Command
{
    /**
     * APPID of current operation
     * @var string
     */
    protected $appid;

    /**
     * Configuration specific information
     */
    protected function configure()
    {
        $this->setName('xtask:fans')->setDescription('Start task fans queue process');
    }

    /**
     * Perform tasks
     * @param Input $input
     * @param Output $output
     * @param array $data
     * @throws InvalidResponseException
     * @throws LocalCacheException
     * @throws Exception
     * @throws PDOException
     */
    public function execute(Input $input, Output $output, array $data = [])
    {
        $appid  = Wechat::getAppid();
        $wechat = Wechat::WeChatUser();
        // Get remote fans
        list($next, $done) = ['', 0];

        while (!is_null($next) && is_array($result = $wechat->getUserList($next)) && !empty($result['data']['openid'])) {
            $done += $result['count'];

            foreach (array_chunk($result['data']['openid'], 100) as $chunk) {
                if (is_array($list = $wechat->getBatchUserInfo($chunk)) && !empty($list['user_info_list'])) {
                    foreach ($list['user_info_list'] as $user) {
                        FansService::set($user, $appid);
                    }
                }
            }

            $next = $result['total'] > $done ? $result['next_openid'] : null;
        }

        // Sync fans blacklist
        list($next, $done) = ['', 0];

        while (!is_null($next) && is_array($result = $wechat->getBlackList($next)) && !empty($result['data']['openid'])) {
            $done += $result['count'];

            foreach (array_chunk($result['data']['openid'], 100) as $chunk) {
                Db::name('WechatFans')->where(['is_black' => '0'])->whereIn('openid', $chunk)->update(['is_black' => '1']);
            }

            $next = $result['total'] > $done ? $result['next_openid'] : null;
        }

        // Sync fan tags
        if (is_array($list = Wechat::WeChatTags()->getTags()) && !empty($list['tags'])) {
            foreach ($list['tags'] as &$tag) {
                $tag['appid'] = $appid;
            }

            Db::name('WechatFansTags')->where('1=1')->delete();
            Db::name('WechatFansTags')->insertAll($list['tags']);
        }
    }
}
