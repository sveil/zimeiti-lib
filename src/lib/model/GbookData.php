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

class GbookData extends Model
{
    // 注册留言数据事件观察者
    protected $observerClass = 'sveil\lib\model\event\GbookData';

    // 一对一UUID
    public function uuid()
    {
        return $this->belongsTo('Uuid', 'id');
    }

    // 多对一留言
    public function gbook()
    {
        return $this->belongsTo('Gbook', 'gbook_id');
    }
}
