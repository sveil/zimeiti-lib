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

namespace sveil\service;

use sveil\Service;

/**
 * Class Data
 * Enterprise data processing services
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Data extends Service
{
    /**
     * Format MAC address information
     * @param string $mac
     * @return string
     */
    public static function applyMacValue(&$mac)
    {
        $mac = strtoupper(str_replace('-', ':', $mac));

        if (preg_match('/([A-F0-9]{2}:){5}[A-F0-9]{2}/', $mac)) {
            return $mac;
        } else {
            return false;
        }
    }
}
