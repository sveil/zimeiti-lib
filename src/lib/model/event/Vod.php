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

class Vod
{
    public function beforeInsert($vod)
    {
        if (empty($vod->id)) {
            $uuid    = findOne("SELECT UNHEX(REPLACE(UUID(), '-', ''))");
            $no      = findOne("SELECT current_serial(table_prefix('vod'))");
            $vod->id = $uuid;

            Uuid::create([
                'id'      => $uuid,
                'tb_name' => config('database.prefix') . 'vod',
                'tb_no'   => $no,
            ]);
        }
    }
}
