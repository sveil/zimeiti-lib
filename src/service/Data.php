<?php

// +----------------------------------------------------------------------
// | Library for Sveil
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/boy12371/think-lib
// | github：https://github.com/boy12371/think-lib
// +----------------------------------------------------------------------

namespace sveil\service;

use sveil\Service;

/**
 * Enterprise data processing services
 *
 * Class Data
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Data extends Service
{

    /**
     * Format MAC address information
     *
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
