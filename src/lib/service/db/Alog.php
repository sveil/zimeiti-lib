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

namespace sveil\lib\service\db;

use sveil\Exception;
use sveil\exception\PDOException;
use sveil\lib\model\Alog as AlogModel;
use sveil\lib\Service;
use sveil\lib\service\db\Option;

/**
 * Class Alog
 * Alog db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Alog extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = AlogModel::withJoin([
            'uuid'   => ['create_at', 'is_disabled'],
            'user'   => ['id', 'name'],
            'method' => ['title', 'key', 'value'],
        ])->select();

        foreach ($arr as $k => $v) {
            $arr[$k]['create_at']   = $v->uuid->create_at;
            $arr[$k]['is_disabled'] = $v->uuid->is_disabled;
        }

        return $arr;
    }

    /**
     * select object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function select()
    {
        $arr = AlogModel::withJoin([
            'uuid'   => ['create_at', 'is_disabled'],
            'user'   => ['id', 'name'],
            'method' => ['title', 'key', 'value'],
        ])->where('uuid.is_disabled', 0)->select();

        foreach ($arr as $k => $v) {
            $arr[$k]['create_at']   = $v->uuid->create_at;
            $arr[$k]['is_disabled'] = $v->uuid->is_disabled;
        }

        return $arr;
    }

    /**
     * count object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function count()
    {
        return AlogModel::withJoin([
            'uuid' => ['is_disabled'],
        ])->where('uuid.is_disabled', 0)->count();
    }

    /**
     * add object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function add($row, $replace = false)
    {
        return AlogModel::create([
            'user_id'          => User::getIdByName($row['user']),
            'action_option_id' => Option::getIdByAction($row['action']),
        ], true, $replace);
    }

    /**
     * addAll object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function addAll($rows)
    {
        $alog = new AlogModel;
        $arr  = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['user_id']          = User::getIdByName($v['user']);
            $arr[$k]['action_option_id'] = Option::getIdByAction($v['action']);
        }

        return $alog->saveAll($arr);
    }

    /**
     * delete object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function delete($id)
    {
        return UuidModel::where('id', $id)->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);
    }

    /**
     * clear object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function clear()
    {
        return UuidModel::where('tb_name', 'alog')->update(['is_disabled' => 2]);
    }
}
