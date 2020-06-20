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

/**
 * Class Build
 * Authorized data processing
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Build extends Service
{
    /**
     * Authorized data filtering conversion processing
     * @param array $info
     * @return mixed
     */
    public static function filter(array $info)
    {
        if (isset($info['func_info'])) {
            $info['func_info'] = join(',', array_map(function ($tmp) {
                return $tmp['funcscope_category']['id'];
            }, $info['func_info']));
        }

        $info['verify_type_info']  = join(',', $info['verify_type_info']);
        $info['service_type_info'] = join(',', $info['service_type_info']);
        $info['business_info']     = json_encode($info['business_info'], JSON_UNESCAPED_UNICODE);
        // WeChat type:  0 Subscription number, 2 Service number, 3 Applets
        $info['service_type'] = intval($info['service_type_info']) === 2 ? 2 : 0;

        if (!empty($info['MiniProgramInfo'])) {
            // WeChat type:  0 Subscription number, 2 Service number, 3 Applets
            $info['service_type'] = 3;
            // Applet information
            $info['miniprograminfo'] = json_encode($info['MiniProgramInfo'], JSON_UNESCAPED_UNICODE);
        }

        unset($info['MiniProgramInfo']);
        // WeChat authentication: -1 not certified, 0 WeChat authentication
        $info['verify_type'] = intval($info['verify_type_info']) !== 0 ? -1 : 0;

        return $info;
    }
}
