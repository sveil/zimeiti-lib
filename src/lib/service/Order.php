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

namespace sveil\lib\service;

use sveil\lib\Service;
use sveil\think\Db;
use sveil\think\db\exception\DataNotFoundException;
use sveil\think\db\exception\ModelNotFoundException;
use sveil\think\Exception;
use sveil\think\exception\DbException;
use sveil\think\exception\PDOException;

/**
 * Class Order
 * Order Service Manager
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Order extends Service
{
    /**
     * Upgrade membership level based on order number
     *
     * @param string $order_no Order number
     * @return boolean
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public static function update($order_no)
    {
        // @todo update order
    }

    /**
     * Synchronize inventory sales based on orders
     *
     * @param string $order_no
     * @return boolean
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public static function syncStock($order_no)
    {

        $map      = ['order_no' => $order_no];
        $goodsIds = Db::name('StoreOrderList')->where($map)->column('goods_id');

        foreach (array_unique($goodsIds) as $goodsId) {
            if (!GoodsService::syncStock($goodsId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Order profit calculation
     *
     * @param string $order_no
     * @return boolean
     */
    public static function profit($order_no = '')
    {
        // @todo Calculate order rebate
    }

}
