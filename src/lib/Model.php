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

// use sveil\model\concern\SoftDelete;

/**
 * Abstract Class Model
 * Standard model base class
 * @author Richard <richard@sveil.com>
 * @package sveil\lib
 */
abstract class Model extends Models
{
    // use SoftDelete;

    // 定义时间戳字段名
    protected $createTime = 'create_at';

    // 关闭自动写入update_time字段
    protected $updateTime = false;

    // 软删除标记字段
    // protected $deleteTime = 'is_disabled';

    // 软删除字段的默认值
    // protected $defaultSoftDelete = 2;

    // 定义全局的查询范围
    // protected $globalScope = ['create_at', 'is_disabled'];

    // public function scopeIsDisabled($query)
    // {
    //     $query->where('is_disabled', 0);
    // }
}
