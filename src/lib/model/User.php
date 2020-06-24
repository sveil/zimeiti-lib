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

class User extends Model
{
    // 类型转换
    protected $type = [
        'status'   => 'integer',
        'score'    => 'float',
        'birthday' => 'timestamp:Y-m-d H:i:s',
    ];

    // 注册用户事件观察者
    protected $observerClass = 'sveil\lib\model\event\User';

    // 一对一UUID
    public function uuid()
    {
        return $this->belongsTo('Uuid', 'id');
    }
}
