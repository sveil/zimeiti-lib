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
use sveil\lib\model\Photo as PhotoModel;
use sveil\lib\Service;

/**
 * Class Photo
 * Photo db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Photo extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = PhotoModel::withJoin([
            'uuid'   => ['create_at', 'is_disabled'],
            'vclass' => ['title', 'key', 'value'],
            'area'   => ['title', 'key', 'value'],
            'lang'   => ['title', 'key', 'value'],
            'year'   => ['title', 'key', 'value'],
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
        $arr = PhotoModel::withJoin([
            'uuid'   => ['create_at', 'is_disabled'],
            'vclass' => ['title', 'key', 'value'],
            'area'   => ['title', 'key', 'value'],
            'lang'   => ['title', 'key', 'value'],
            'year'   => ['title', 'key', 'value'],
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
        return PhotoModel::withJoin([
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
        return PhotoModel::create([
            'vclass_option_id' => Option::getIdByVclass($row['vclass']),
            'area_option_id'   => Option::getIdByArea($row['area']),
            'lang_option_id'   => Option::getIdByLang($row['lang']),
            'year_option_id'   => Option::getIdByYear($row['year']),
            'title'            => $row['title'],
            'letter'           => $row['letter'],
            'color'            => $row['color'],
            'total'            => $row['total'],
            'isend'            => $row['isend'],
            'level'            => $row['level'],
            'copyright'        => $row['copyright'],
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
        $vod = new PhotoModel;
        $arr = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['vclass_option_id'] = Option::getIdByVclass($v['vclass']);
            $arr[$k]['area_option_id']   = Option::getIdByArea($v['area']);
            $arr[$k]['lang_option_id']   = Option::getIdByLang($v['lang']);
            $arr[$k]['year_option_id']   = Option::getIdByYear($v['year']);
            $arr[$k]['title']            = $v['title'];
            $arr[$k]['letter']           = $v['letter'];
            $arr[$k]['color']            = $v['color'];
            $arr[$k]['total']            = $v['total'];
            $arr[$k]['isend']            = $v['isend'];
            $arr[$k]['level']            = $v['level'];
            $arr[$k]['copyright']        = $v['copyright'];
        }

        return $vod->saveAll($arr);
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
        return UuidModel::where('tb_name', 'vod')->update(['is_disabled' => 2]);
    }
}
