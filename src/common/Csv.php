<?php

// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-lib
// | github：https://github.com/sveil/zimeiti-lib
// +----------------------------------------------------------------------

namespace sveil\common;

/**
 * CSV export tool
 *
 * Class Csv
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Csv
{

    /**
     * Write into CSV file header
     *
     * @param string $filename Export file
     * @param array $headers CSV header (One-dimensional array)
     */
    public static function header($filename, array $headers)
    {

        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=" . iconv('UTF-8', 'GB2312//IGNORE', $filename));
        $handle = fopen('php://output', 'w');

        foreach ($headers as $key => $value) {
            $headers[$key] = iconv("UTF-8", "GB2312//IGNORE", $value);
        }

        fputcsv($handle, $headers);

        if (is_resource($handle)) {
            fclose($handle);
        }

    }

    /**
     * Write into CSV file content
     *
     * @param array $list Data list (two-dimensional array or multi-dimensional array)
     * @param array $rules Data rules (one-dimensional array)
     */
    public static function body(array $list, array $rules)
    {

        $handle = fopen('php://output', 'w');

        foreach ($list as $data) {
            $rows = [];
            foreach ($rules as $rule) {
                $rows[] = self::parseKeyDotValue($data, $rule);
            }

            fputcsv($handle, $rows);
        }

        if (is_resource($handle)) {
            fclose($handle);
        }

    }

    /**
     * Query from array key (with dot rules)
     *
     * @param array $data data
     * @param string $rule rules, such as: order.order_no
     * @return mixed
     */
    public static function parseKeyDotValue(array $data, $rule)
    {

        list($temp, $attr) = [$data, explode('.', trim($rule, '.'))];

        while ($key = array_shift($attr)) {
            $temp = isset($temp[$key]) ? $temp[$key] : $temp;
        }

        return (is_string($temp) || is_numeric($temp)) ? @iconv('UTF-8', 'GB2312//IGNORE', "{$temp}") : '';
    }

}
