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
use sveil\lib\model\Famer as FamerModel;
use sveil\lib\Service;

/**
 * Class Famer
 * Famer db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Famer extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = FamerModel::withJoin([
            'uuid'     => ['create_at', 'is_disabled'],
            'blood'    => ['title', 'key', 'value'],
            'origin'   => ['title', 'key', 'value'],
            'starsign' => ['title', 'key', 'value'],
            'area'     => ['title', 'key', 'value'],
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
        $arr = FamerModel::withJoin([
            'uuid'     => ['create_at', 'is_disabled'],
            'blood'    => ['title', 'key', 'value'],
            'origin'   => ['title', 'key', 'value'],
            'starsign' => ['title', 'key', 'value'],
            'area'     => ['title', 'key', 'value'],
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
        return FamerModel::withJoin([
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
        return FamerModel::create([
            'blood_option_id'    => Option::getIdByBlood($row['blood']),
            'origin_option_id'   => Option::getIdByOrigin($row['origin']),
            'starsign_option_id' => Option::getIdByStarsign($row['starsign']),
            'area_option_id'     => Option::getIdByArea($row['area']),
            'name'               => $row['name'],
            'letter'             => $row['letter'],
            'color'              => $row['color'],
            'gender'             => $row['gender'],
            'birth'              => $row['birth'],
            'level'              => $row['level'],
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
        $famer = new FamerModel;
        $arr   = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['blood_option_id']    = Option::getIdByBlood($v['blood']);
            $arr[$k]['origin_option_id']   = Option::getIdByOrigin($v['origin']);
            $arr[$k]['starsign_option_id'] = Option::getIdByStarsign($v['starsign']);
            $arr[$k]['area_option_id']     = Option::getIdByArea($v['area']);
            $arr[$k]['name']               = $v['name'];
            $arr[$k]['letter']             = $v['letter'];
            $arr[$k]['color']              = $v['color'];
            $arr[$k]['gender']             = $v['gender'];
            $arr[$k]['birth']              = $v['birth'];
            $arr[$k]['level']              = $v['level'];
        }

        return $famer->saveAll($arr);
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
        return UuidModel::where('tb_name', 'famer')->update(['is_disabled' => 2]);
    }
}
