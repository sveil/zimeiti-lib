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
use sveil\exception\DbException;
use sveil\lib\Service;

/**
 * Mall postage service
 *
 * Class Express
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Express extends Service
{

    /**
     * Order postage calculation
     *
     * @param string $province Delivery province
     * @param string $number Billed quantity
     * @param string $amount order amount
     * @return array
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public static function price($province, $number, $amount)
    {

        // Read the corresponding template rules
        $map  = [['is_default', 'eq', '0'], ['rule', 'like', "%{$province}%"]];
        $rule = Db::name('StoreExpressTemplate')->where($map)->find();

        if (!empty($rule)) {
            return self::buildData($rule, '普通模板', $number, $amount);
        }

        $rule = Db::name('StoreExpressTemplate')->where(['is_default' => '1'])->find();

        return self::buildData($rule, '默认模板', $number, $amount);

    }

    /**
     * Generate postage data
     *
     * @param array $rule Template rules
     * @param string $type Template type
     * @param integer $number Count pieces
     * @param double $amount order amount
     * @return array
     */
    protected static function buildData($rule, $type, $number, $amount)
    {

        // Exception rule
        if (empty($rule)) {
            return [
                'express_price' => 0.00, 'express_type' => '未知模板', 'express_desc' => '未匹配到邮费模板',
            ];
        }

        // Full reduction mail
        if ($rule['order_reduction_state'] && $amount >= $rule['order_reduction_price']) {
            return [
                'express_price' => 0.00, 'express_type' => $type,
                'express_desc'  => "订单总金额满{$rule['order_reduction_price']}元减免全部邮费",
            ];
        }

        // First billing
        if ($number <= $rule['first_number']) {
            return [
                'express_price' => $rule['first_price'], 'express_type' => $type,
                'express_desc'  => "首件计费，{$rule['first_number']}件及{$rule['first_number']}以内计费{$rule['first_price']}元",
            ];
        }

        // Continued billing
        list($price1, $price2) = [$rule['first_price'], 0];

        if ($rule['next_number'] > 0 && $rule['next_price'] > 0) {
            $price2 = $rule['next_price'] * ceil(($number - $rule['first_number']) / $rule['next_number']);
        }

        return [
            'express_price' => $price1 + $price2, 'express_type' => $type,
            'express_desc'  => "续件计费，超出{$rule['first_number']}件，首件费用{$rule['first_price']}元 + 续件费用{$price2}元",
        ];

    }

}
