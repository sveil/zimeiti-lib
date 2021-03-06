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

class Vod extends Model
{
    // 注册视频事件观察者
    protected $observerClass = 'sveil\lib\model\event\Vod';

    // 一对一UUID
    public function uuid()
    {
        return $this->belongsTo('Uuid', 'id');
    }

    // 多对一影片分类
    public function vclass()
    {
        return $this->belongsTo('Option', 'vclass_option_id');
    }
}
