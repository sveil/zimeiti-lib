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

namespace sveil\lib\rep\command;

use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\service\Fans as FansService;
use sveil\lib\service\Wechat;
use sveil\console\Command;
use sveil\console\Input;
use sveil\console\Output;
use sveil\Db;
use sveil\Exception;
use sveil\exception\PDOException;

/**
 * WeChat fans management
 *
 * Class Fans
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\command
 */
class Fans extends Command
{

    /**
     * Modules to be processed
     * @var array
     */
    protected $module = ['list', 'tags', 'black'];

    /**
     * Execute instruction
     *
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {

        foreach ($this->module as $m) {
            if (method_exists($this, $fun = "_{$m}")) {
                $this->$fun();
            }
        }

    }

    /**
     * Sync WeChat fan list
     *
     * @param string $next
     * @param integer $done
     * @throws InvalidResponseException
     * @throws LocalCacheException
     * @throws Exception
     * @throws PDOException
     */
    protected function _list($next = '', $done = 0)
    {
        $appid  = Wechat::getAppid();
        $wechat = Wechat::WeChatUser();
        $this->output->comment('开始同步微信粉丝数据 ...');

        while (!is_null($next) && is_array($result = $wechat->getUserList($next)) && !empty($result['data']['openid'])) {
            foreach (array_chunk($result['data']['openid'], 100) as $chunk) {
                if (is_array($list = $wechat->getBatchUserInfo($chunk)) && !empty($list['user_info_list'])) {
                    foreach ($list['user_info_list'] as $user) {
                        $indexString = str_pad(++$done, strlen($result['total']), '0', STR_PAD_LEFT);
                        $this->output->writeln("({$indexString}/{$result['total']}) 正在更新粉丝 {$user['openid']} {$user['nickname']}");
                        FansService::set($user, $appid);
                    }
                }
            }
            $next = $result['total'] > $done ? $result['next_openid'] : null;
        }

        $this->output->comment('微信粉丝数据同步完成');
        $this->output->newLine();
    }

    /**
     * Sync fans blacklist
     *
     * @param string $next
     * @param integer $done
     * @throws InvalidResponseException
     * @throws LocalCacheException
     * @throws Exception
     * @throws PDOException
     */
    public function _black($next = '', $done = 0)
    {
        $wechat = Wechat::WeChatUser();
        $this->output->comment('开始同步微信黑名单数据 ...');

        while (!is_null($next) && is_array($result = $wechat->getBlackList($next)) && !empty($result['data']['openid'])) {
            $done += $result['count'];
            foreach (array_chunk($result['data']['openid'], 100) as $chunk) {
                Db::name('WechatFans')->where(['is_black' => '0'])->whereIn('openid', $chunk)->update(['is_black' => '1']);
            }
            $this->output->writeln("--> 共计同步微信黑名单{$result['total']}人");
            $next = $result['total'] > $done ? $result['next_openid'] : null;
        }

        $this->output->comment('微信黑名单数据同步完成');
        $this->output->newLine();
    }

    /**
     * Sync fans tag list
     *
     * @param integer $index
     * @throws InvalidResponseException
     * @throws LocalCacheException
     * @throws Exception
     * @throws PDOException
     */
    public function _tags($index = 0)
    {

        $appid  = Wechat::getAppid();
        $wechat = Wechat::WeChatTags();
        $this->output->comment('同步微信粉丝标签数据...');

        if (is_array($list = $wechat->getTags()) && !empty($list['tags'])) {
            $count = count($list['tags']);
            foreach ($list['tags'] as &$tag) {
                $tag['appid'] = $appid;
                $indexString  = str_pad(++$index, strlen($count), '0', STR_PAD_LEFT);
                $this->output->writeln("({$indexString}/{$count}) 更新粉丝标签 {$tag['name']}");
            }
            Db::name('WechatFansTags')->where(['appid' => $appid])->delete();
            Db::name('WechatFansTags')->insertAll($list['tags']);
        }

        $this->output->comment('微信粉丝标签数据同步完成');
        $this->output->newLine();
    }

}
