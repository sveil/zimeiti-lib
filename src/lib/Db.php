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

namespace sveil\lib;

use sveil\DbException;

/**
 * Database base class
 *
 * Class Db
 * @author Richard <richard@sveil.com>
 * @package sveil
 */
class Db
{

    private static $objInstance;

    /*
     * Class Constructor - Create a new database connection if one doesn't exist
     * Set to private so no-one can create a new instance via ' = new DB();'
     */
    // private function __construct() {}

    /*
     * Like the constructor, we make __clone private so nobody can clone the instance
     */
    private function __clone()
    {}

    /*
     * Returns DB instance or create initial connection
     *
     * @param
     * @return $objInstance;
     */
    public static function connect($config = [], $options = null)
    {

        if (!empty($config) || !self::$objInstance) {
            self::$objInstance = new \PDO('mysql:host=' . $config['hostname'] . ';port=' . $config['hostport'] . ';dbname=' . $config['database'] . ';charset=' . $config['charset'] . ';', $config['username'], $config['password'], $options);
            self::$objInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$objInstance;
    }

    /*
     * Passes on any static calls to this class onto the singleton PDO instance
     *
     * @param $chrMethod, $arrArguments
     * @return $mix
     */
    final public static function __callStatic($chrMethod, $arrArguments)
    {

        if (self::$objInstance) {
            return call_user_func_array([self::$objInstance, $chrMethod], $arrArguments);
        } else {
            throw new DbException();
        }

    }

    public static function mysqlClientVersion()
    {

        if (function_exists('mysqli_get_client_version')) {
            $version = @mysqli_get_client_version();
            return (intval($version / 10000)) . '.' . ($version / 100 - intval($version / 10000) * 100);
        }

        return '';
    }

}
