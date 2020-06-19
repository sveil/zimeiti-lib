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

use sveil\Db;
use sveil\db\exception\DataNotFoundException;
use sveil\db\exception\ModelNotFoundException;
use sveil\Exception;
use sveil\exception\DbException;
use sveil\exception\PDOException;
use sveil\lib\Service;

/**
 * Commodity data management
 *
 * Class Goods
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Goods extends Service
{

    /**
     * Synchronize product inventory information
     *
     * @param integer $goodsId
     * @return boolean
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public static function syncStock($goodsId)
    {

        // Commodity storage statistics
        $fields    = "goods_id,goods_spec,ifnull(sum(number_stock),0) number_stock";
        $stockList = Db::name('StoreGoodsStock')->field($fields)->where(['goods_id' => $goodsId])->group('goods_id,goods_spec')->select();
        // Commodity sales statistics
        $where     = [['b.goods_id', 'eq', $goodsId], ['a.status', 'in', ['1', '2', '3', '4', '5']]];
        $fields    = 'b.goods_id,b.goods_spec,ifnull(sum(b.number_goods),0) number_sales';
        $salesList = Db::table('store_order a')->field($fields)->leftJoin('store_order_list b', 'a.order_no=b.order_no')->where($where)->group('b.goods_id,b.goods_spec')->select();
        // set up update data
        $dataList = [];

        foreach (array_merge($stockList, $salesList) as $vo) {
            $key            = "{$vo['goods_id']}@@{$vo['goods_spec']}";
            $dataList[$key] = isset($dataList[$key]) ? array_merge($dataList[$key], $vo) : $vo;
            if (empty($dataList[$key]['number_sales'])) {
                $dataList[$key]['number_sales'] = '0';
            }
            if (empty($dataList[$key]['number_stock'])) {
                $dataList[$key]['number_stock'] = '0';
            }
        }

        unset($salesList, $stockList);

        // Update product specifications, sales and inventory
        foreach ($dataList as $vo) {
            Db::name('StoreGoodsList')->where([
                'goods_id'   => $goodsId,
                'goods_spec' => $vo['goods_spec'],
            ])->update([
                'number_stock' => $vo['number_stock'],
                'number_sales' => $vo['number_sales'],
            ]);
        }

        // Update the sales volume and inventory of goods
        Db::name('StoreGoods')->where(['id' => $goodsId])->update([
            'number_stock' => intval(array_sum(array_column($dataList, 'number_stock'))),
            'number_sales' => intval(array_sum(array_column($dataList, 'number_sales'))),
        ]);

        return true;
    }

}
