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
use sveil\lib\model\Cache as CacheModel;
use sveil\lib\Service;

/**
 * Class Cache
 * Cache db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Cache extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = CacheModel::withJoin([
            'uuid' => ['create_at', 'is_disabled'],
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
        $arr = CacheModel::withJoin([
            'uuid' => ['create_at', 'is_disabled'],
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
        return CacheModel::withJoin([
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
        return CacheModel::create([
            'title' => $row['title'],
            'key'   => $row['key'],
            'value' => $row['value'],
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
        $cache = new CacheModel;
        $arr   = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['title'] = $v['title'];
            $arr[$k]['key']   = $v['key'];
            $arr[$k]['value'] = $v['value'];
        }

        return $cache->saveAll($arr);
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
        return UuidModel::where('tb_name', 'cache')->update(['is_disabled' => 2]);
    }
}
