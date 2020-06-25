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
use sveil\lib\model\Cash as CashModel;
use sveil\lib\Service;

/**
 * Class Cash
 * Cash db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Cash extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = CashModel::withJoin([
            'uuid' => ['create_at', 'is_disabled'],
            'user' => ['id', 'name'],
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
        $arr = CashModel::withJoin([
            'uuid' => ['create_at', 'is_disabled'],
            'user' => ['id', 'name'],
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
        return CashModel::withJoin([
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
        return CashModel::create([
            'user_id'    => User::getIdByName($row['user']),
            'balance'    => $row['balance'],
            'bank_name'  => $row['bank_name'],
            'bank_no'    => $row['bank_no'],
            'payee_name' => $row['payee_name'],
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
        $cash = new CashModel;
        $arr  = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['user_id']    = User::getIdByName($v['user']);
            $arr[$k]['balance']    = $v['balance'];
            $arr[$k]['bank_name']  = $v['bank_name'];
            $arr[$k]['bank_no']    = $v['bank_no'];
            $arr[$k]['payee_name'] = $v['payee_name'];
        }

        return $cash->saveAll($arr);
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
        return UuidModel::where('tb_name', 'cash')->update(['is_disabled' => 2]);
    }
}
