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

class Queue extends Model
{
    // 类型转换
    protected $type = [
        'percent' => 'integer',
    ];

    // 注册队列事件观察者
    protected $observerClass = 'sveil\lib\model\event\Queue';

    // 对应一对一UUID
    public function uuid()
    {
        return $this->belongsTo('Uuid', 'id');
    }

    // 对应一对一状态选项
    public function qstatus()
    {
        return $this->belongsTo('Option', 'qstatus_option_id');
    }

    // 对应一对一触发条件选项
    public function qitem()
    {
        return $this->belongsTo('Option', 'qitem_option_id');
    }
}
