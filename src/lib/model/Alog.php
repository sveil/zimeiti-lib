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

    // 一对一UUID
    public function uuid()
    {
        return $this->hasOne('Uuid', 'id')->bind('is_disabled');
    }

    // 多对一用户
    public function user()
    {
        return $this->belongsTo('User', 'user_id')->bind('name,email,mobile');
    }

    // 一对一方法选项
    public function option()
    {
        return $this->hasOne('Option', 'method_option_id')->bind('title,key,value');
    }
}
