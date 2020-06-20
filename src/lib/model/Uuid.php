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

class Uuid extends Model
{
    // 类型转换
    protected $type = [
        'status'   => 'integer',
        'score'    => 'float',
        'birthday' => 'timestamp:Y-m-d H:i:s',
    ];

    // 自动完成
    protected $auto   = [];
    protected $insert = ['is_disabled' => 0];
    protected $update = [];

    // 对应一对一日志
    public function alog()
    {
        return $this->belongsTo('Alog');
    }

    // 对应一对一用户
    public function user()
    {
        return $this->belongsTo('User');
    }
}
