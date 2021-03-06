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
use sveil\lib\model\User as UserModel;
use sveil\lib\Service;

/**
 * Class User
 * User db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class User extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = UserModel::withJoin([
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
        $arr = UserModel::withJoin([
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
        return UserModel::withJoin([
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
        return UserModel::create([
            'name'     => $row['name'],
            'pwd'      => $row['pwd'],
            'email'    => $row['email'],
            'mobile'   => $row['mobile'],
            'nickname' => $row['nickname'],
            'gender'   => $row['gender'],
            'reg_ip'   => $row['reg_ip'],
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
        $user = new UserModel;
        $arr  = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['name']     = $v['name'];
            $arr[$k]['pwd']      = $v['pwd'];
            $arr[$k]['email']    = $v['email'];
            $arr[$k]['mobile']   = $v['mobile'];
            $arr[$k]['nickname'] = $v['nickname'];
            $arr[$k]['gender']   = $v['gender'];
            $arr[$k]['reg_ip']   = $v['reg_ip'];
        }

        return $user->saveAll($arr);
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
        return UuidModel::where('tb_name', 'user')->update(['is_disabled' => 2]);
    }
}
