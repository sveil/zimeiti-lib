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

namespace sveil\service;

use sveil\Service;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

/**
 * WeChat fans information
 *
 * Class Fans
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Fans extends Service
{

    /**
     * Add or update fan information
     *
     * @param array $user Fans information
     * @param string $appid Wechat APPID
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    public static function set(array $user, $appid = '')
    {

        if (!empty($user['subscribe_time'])) {
            $user['subscribe_at'] = date('Y-m-d H:i:s', $user['subscribe_time']);
        }

        if (isset($user['tagid_list']) && is_array($user['tagid_list'])) {
            $user['tagid_list'] = is_array($user['tagid_list']) ? join(',', $user['tagid_list']) : '';
        }

        if ($appid !== '') {
            $user['appid'] = $appid;
        }

        unset($user['privilege'], $user['groupid']);

        return data_save('WechatFans', $user, 'openid');
    }

    /**
     * Get fans information
     *
     * @param string $openid
     * @return array|null
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public static function get($openid)
    {
        return Db::name('WechatFans')->where(['openid' => $openid])->find();
    }

}
