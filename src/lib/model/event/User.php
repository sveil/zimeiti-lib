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

class User
{
    public function beforeInsert($user)
    {
        if (empty($user->id)) {
            $rows = Db::query("SELECT UNHEX(REPLACE(UUID(), '-', '')) as uuid");

            foreach ($rows as $row) {}

            $user->id = $row['uuid'];

            Uuid::create([
                'id'      => $row['uuid'],
                'tb_name' => config('database.prefix') . 'user',
                'tb_no'   => 0,
            ]);
        }
    }
}
