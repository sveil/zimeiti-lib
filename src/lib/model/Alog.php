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

namespace sveil\lib\model;

use sveil\lib\Model;

class Alog extends Model
{
    // 注册日志事件观察者
    protected $observerClass = 'sveil\lib\model\event\Alog';

    // 对应一对一UUID
    public function uuid()
    {
        return $this->belongsTo('Uuid', 'id');
    }

    // 对应多对一用户
    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    // 对应一对一方法选项
    public function method()
    {
        return $this->belongsTo('Option', 'method_option_id');
    }
}
