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

namespace sveil\lib;

use sveil\Model as Models;

/**
 * Abstract Class Model
 * Standard model base class
 * @author Richard <richard@sveil.com>
 * @package sveil\lib
 */
abstract class Model extends Models
{
    // 定义时间戳字段名
    protected $createTime = 'create_at';

    // 关闭自动写入update_time字段
    protected $updateTime = false;
}
