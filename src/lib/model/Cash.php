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

class Cash extends Model
{
    // 注册现金事件观察者
    protected $observerClass = 'sveil\lib\model\event\Cash';

    // 一对一UUID
    public function uuid()
    {
        return $this->hasOne('Uuid', 'id')->bind('is_disabled');
    }

    // 多对一文章
    public function article()
    {
        return $this->belongsTo('Article', 'article_id')->bind('title,score');
    }
}
