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

class Article extends Model
{
    // 类型转换
    protected $type = [
        'level'        => 'integer',
        'points'       => 'integer',
        'points_count' => 'integer',
        'up'           => 'integer',
        'down'         => 'integer',
        'hits'         => 'integer',
        'hits_day'     => 'integer',
        'hits_week'    => 'integer',
        'hits_month'   => 'integer',
        'score'        => 'float',
        'score_sum'    => 'integer',
        'score_count'  => 'integer',
        'hits_at'      => 'timestamp:Y-m-d H:i:s',
        'make_at'      => 'integer',
    ];

    // 注册文章事件观察者
    protected $observerClass = 'sveil\lib\model\event\Article';

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
}
