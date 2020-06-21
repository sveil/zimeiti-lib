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

class Option extends Model
{
    // 注册选项事件观察者
    protected $observerClass = 'sveil\lib\model\event\Option';

    // 一对一UUID
    public function uuid()
    {
        return $this->hasOne('Uuid', 'id')->bind('is_disabled');
    }
}
