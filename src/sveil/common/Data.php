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

namespace sveil\common;

use think\Db;
use think\db\Query;
use think\Exception;
use think\exception\PDOException;

/**
 * Class Data
 * Data processing tools
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Data
{

    private static $res = [];

    /**
     * Data incremental storage
     * @param Query|string $dbQuery Data query object
     * @param array $data Data to be saved or updated
     * @param string $key Conditional primary key restrictions
     * @param array $where Other where conditions
     * @return boolean|integer
     * @throws Exception
     * @throws PDOException
     */
    public static function save($dbQuery, $data, $key = 'id', $where = [])
    {
        $db                  = is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery;
        list($table, $value) = [$db->getTable(), isset($data[$key]) ? $data[$key] : null];
        $map                 = isset($where[$key]) ? [] : (is_string($value) ? [[$key, 'in', explode(',', $value)]] : [$key => $value]);

        if (is_array($info = Db::table($table)->master()->where($where)->where($map)->find()) && !empty($info)) {
            if (Db::table($table)->strict(false)->where($where)->where($map)->update($data) !== false) {
                return isset($info[$key]) ? $info[$key] : true;
            } else {
                return false;
            }
        } else {
            return Db::table($table)->strict(false)->insertGetId($data);
        }
    }

    /**
     * Update data in bulk
     * @param Query|string $dbQuery Data query object
     * @param array $data Data to be updated (Two-dimensional array)
     * @param string $key Conditional primary key restrictions
     * @param array $where Other where conditions
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    public static function batchSave($dbQuery, $data, $key = 'id', $where = [])
    {
        list($case, $input) = [[], []];

        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                $case[$key][] = "WHEN '{$row[$key]}' THEN '{$value}'";
            }
        }

        if (isset($case[$key])) {
            unset($case[$key]);
        }

        $db = is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery;

        foreach ($case as $key => $value) {
            $input[$key] = $db->raw("CASE `{$key}` " . join(' ', $value) . ' END');
        }

        return $db->whereIn($key, array_unique(array_column($data, $key)))->where($where)->update($input) !== false;
    }

    /**
     * One-dimensional data encoding to generate data tree
     * @param array $list data list
     * @param string $id parent id key
     * @param string $pid id key
     * @param string $son define sub data key
     * @return array
     */
    public static function arr2tree($list, $id = 'id', $pid = 'pid', $son = 'sub')
    {
        list($tree, $map) = [[], []];

        foreach ($list as $item) {
            $map[$item[$id]] = $item;
        }

        foreach ($list as $item) {
            if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                $map[$item[$pid]][$son][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }

        unset($map);

        return $tree;
    }

    /**
     * One-dimensional data encoding to generate data tree
     * @param array $list data list
     * @param string $id id key
     * @param string $pid parent id key
     * @param string $path
     * @param string $ppath
     * @return array
     */
    public static function arr2table(array $list, $id = 'id', $pid = 'pid', $path = 'path', $ppath = '')
    {
        $tree = [];

        foreach (self::arr2tree($list, $id, $pid) as $attr) {
            $attr[$path] = "{$ppath}-{$attr[$id]}";
            $attr['sub'] = isset($attr['sub']) ? $attr['sub'] : [];
            $attr['spt'] = substr_count($ppath, '-');
            $attr['spl'] = str_repeat("　├　", $attr['spt']);
            $sub         = $attr['sub'];
            unset($attr['sub']);
            $tree[] = $attr;
            if (!empty($sub)) {
                $tree = array_merge($tree, self::arr2table($sub, $id, $pid, $path, $attr[$path]));
            }
        }

        return $tree;
    }

    /**
     * Get data tree sub id
     * @param array $list data list
     * @param int $id start id
     * @param string $key sub key
     * @param string $pkey parent key
     * @return array
     */
    public static function getArrSubIds($list, $id = 0, $key = 'id', $pkey = 'pid')
    {
        $ids = [intval($id)];

        foreach ($list as $vo) {
            if (intval($vo[$pkey]) > 0 && intval($vo[$pkey]) === intval($id)) {
                $ids = array_merge($ids, self::getArrSubIds($list, intval($vo[$key]), $key, $pkey));
            }
        }

        return $ids;
    }

    /**
     * Unique digital code
     * @param integer $length
     * @return string
     */
    public static function uniqidNumberCode($length = 10)
    {
        $time = time() . '';

        if ($length < 10) {
            $length = 10;
        }

        $string = ($time[0] + $time[1]) . substr($time, 2) . rand(0, 9);

        while (strlen($string) < $length) {
            $string .= rand(0, 9);
        }

        return $string;
    }

    /**
     * Unique date encoding
     * @param integer $length
     * @return string
     */
    public static function uniqidDateCode($length = 14)
    {
        if ($length < 14) {
            $length = 14;
        }

        $string = date('Ymd') . (date('H') + date('i')) . date('s');

        while (strlen($string) < $length) {
            $string .= rand(0, 9);
        }

        return $string;
    }

    /**
     * Get random string encoding
     * @param integer $length Baseline length
     * @param integer $type String type (1 number, 2 letter, 3 number and letter)
     * @return string
     */
    public static function randomCode($length = 10, $type = 1)
    {
        $numbs = '0123456789';
        $chars = 'abcdefghijklmnopqrstuvwxyz';

        if (intval($type) === 1) {
            $chars = $numbs;
        }

        if (intval($type) === 2) {
            $chars = "a{$chars}";
        }

        if (intval($type) === 3) {
            $chars = "{$numbs}{$chars}";
        }

        $string = $chars[rand(1, strlen($chars) - 1)];

        if (isset($chars)) {
            while (strlen($string) < $length) {
                $string .= $chars[rand(0, strlen($chars) - 1)];
            }
        }

        return $string;
    }

    /**
     * File size display conversion
     * @param integer $size File size
     * @param integer $deci Decimal places
     * @return string
     */
    public static function toFileSize($size, $deci = 2)
    {
        list($pos, $map) = [0, ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB']];

        while ($size >= 1024 && $pos < 6) {
            if (++$pos) {
                $size /= 1024;
            }
        }

        return round($size, $deci) . ' ' . $map[$pos];
    }

    /**
     * Generate values ​​that expand the tree distribution based on ID values
     * @param int $id node id
     * @return array
     */
    public static function id2spread($id = 0)
    {
        foreach (self::$res as $v) {
            if ($v['id'] === $id) {
                if ($v['pid']) {
                    self::id2spread($v['pid']);
                }
                $v['disabled']       = true;
                $v['spread']         = true;
                self::$res[$v['id']] = $v;
            }
        }

        return self::$res;
    }

    /**
     * Generate tree texture based on ID value
     * @param array $list data list
     * @param int $id node id
     * @return array
     */
    public static function id2arr($list, $id = 0)
    {
        $id               = $id + 0;
        list($tree, $map) = [[], []];
        self::$res        = $list;
        $map              = self::id2spread($id);

        foreach ($map as $v) {
            if (isset($v['pid']) && isset($map[$v['pid']])) {
                $map[$v['pid']]['children'][] = &$map[$v['id']];
            } else {
                $tree[] = &$map[$v['id']];
            }
        }

        unset($map);

        return $tree;
    }
}
