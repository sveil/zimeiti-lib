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

use sveil\Db;
use sveil\Exception;
use sveil\exception\PDOException;
use sveil\lib\model\Queue as QueueModel;
use sveil\lib\model\Uuid as UuidModel;
use sveil\lib\Service;
use sveil\lib\service\db\Option;

/**
 * Class Queue
 * Queue data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Queue extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = QueueModel::withJoin([
            'uuid'    => ['create_at', 'is_disabled'],
            'qstatus' => ['title', 'key', 'value'],
            'qitem'   => ['title', 'key', 'value'],
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
        $arr = QueueModel::withJoin([
            'uuid'    => ['create_at', 'is_disabled'],
            'qstatus' => ['title', 'key', 'value'],
            'qitem'   => ['title', 'key', 'value'],
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
        return QueueModel::withJoin([
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
        return QueueModel::create([
            'qstatus_option_id' => Option::getIdByQstatus($row['qstatus']),
            'qitem_option_id'   => Option::getIdByQitem($row['qitem']),
            'title'             => $row['title'],
            'command'           => $row['command'],
            'log'               => $row['log'],
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
        $queue = new QueueModel;
        $arr   = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['qstatus_option_id'] = Option::getIdByQstatus($v['qstatus']);
            $arr[$k]['qitem_option_id']   = Option::getIdByQitem($v['qitem']);
            $arr[$k]['title']             = $v['title'];
            $arr[$k]['command']           = $v['command'];
            $arr[$k]['log']               = $v['log'];
        }

        return $queue->saveAll($arr);
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
}
