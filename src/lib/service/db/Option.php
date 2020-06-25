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

namespace sveil\lib\service\db;

use sveil\Exception;
use sveil\exception\PDOException;
use sveil\lib\model\Option as OptionModel;
use sveil\lib\Service;

/**
 * Class Option
 * Queue db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Option extends Service
{
    /**
     * qstatus object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByQstatus($str)
    {
        return OptionModel::qstatus($str)->find()->id;
    }

    /**
     * qitem object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByQitem($str)
    {
        return OptionModel::qitem($str)->find()->id;
    }

    /**
     * action object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByAction($str)
    {
        return OptionModel::action($str)->find()->id;
    }
}
