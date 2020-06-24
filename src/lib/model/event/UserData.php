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

class UserData
{
    public function beforeInsert($userData)
    {
        if (empty($userData->id)) {
            $uuid         = findRes("SELECT UNHEX(REPLACE(UUID(), '-', ''))");
            $no           = findRes("SELECT current_serial(table_prefix('user_data'))");
            $userData->id = $uuid;

            Uuid::create([
                'id'      => $uuid,
                'tb_name' => config('database.prefix') . 'user_data',
                'tb_no'   => $no,
            ]);
        }
    }
}
