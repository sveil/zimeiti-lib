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

namespace sveil\lib\model\event;

use sveil\lib\model\Uuid;

class Link
{
    public function beforeInsert($link)
    {
        if (empty($link->id)) {
            $uuid     = findRes("SELECT UNHEX(REPLACE(UUID(), '-', ''))");
            $no       = findRes("SELECT current_serial(table_prefix('link'))");
            $link->id = $uuid;

            Uuid::create([
                'id'      => $uuid,
                'tb_name' => config('database.prefix') . 'link',
                'tb_no'   => $no,
            ]);
        }
    }
}
