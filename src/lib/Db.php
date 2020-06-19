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

use sveil\exception\DbException;

/**
 * Class Db
 * Database base class
 * @author Richard <richard@sveil.com>
 * @package sveil\lib
 */
class Db
{
    // DB instance
    private static $objInstance;

    // DB host
    private static $host;

    // DB user
    private static $user;

    /*
     * Class Constructor - Create a new database connection if one doesn't exist
     * Set to private so no-one can create a new instance via ' = new DB();'
     */
    private function __construct()
    {}

    /*
     * Like the constructor, we make __clone private so nobody can clone the instance
     */
    private function __clone()
    {}

    /*
     * Returns DB instance or create initial connection
     * @param
     * @return $mix;
     */
    public static function connect($config = [], $options = null)
    {
        if (!empty($config) || !self::$objInstance) {
            self::$host = strtolower($config['hostname']);
            self::$user = $config['username'];

            try {
                self::$objInstance = new \PDO('mysql:host=' . self::$host . ';port=' . $config['hostport'] . ';dbname=' . $config['database'] . ';charset=' . $config['charset'] . ';', self::$user, $config['password'], $options);
                self::$objInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                return false;
            }
        }

        return self::$objInstance;
    }

    /*
     * Passes on any static calls to this class onto the singleton PDO instance
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

    /*
     * is not permission
     * @param $config
     * @return boolean
     */
    public static function noPermit()
    {
        $noPermit = true;

        if (self::$objInstance && self::$host && self::$user) {
            $db   = self::$objInstance;
            $user = self::$user;

            if (self::$host === 'localhost' || self::$host === '127.0.0.1') {
                $host = 'localhost';
            } else {
                $host = '%';
            }

            $sql = "SELECT `Select_priv`,`Insert_priv`,`Update_priv`,`Delete_priv`,`Create_priv`,`Drop_priv` FROM `user` WHERE `User`='" . $user . "' AND `Host`='" . $host . "';";

            foreach ($db->query($sql) as $row) {
                $noPermit = $row['Select_priv'] === 'N' ?: false;
                $noPermit = $row['Insert_priv'] === 'N' ?: false;
                $noPermit = $row['Update_priv'] === 'N' ?: false;
                $noPermit = $row['Delete_priv'] === 'N' ?: false;
                $noPermit = $row['Create_priv'] === 'N' ?: false;
                $noPermit = $row['Drop_priv'] === 'N' ?: false;
            }
        }

        return $noPermit;
    }

    /**
     * mysql server version
     * @return string
     */
    public static function serverVer()
    {
        if (self::$objInstance) {
            $db = self::$objInstance;

            foreach ($db->query('SELECT version() as ver') as $row) {
                return $row['ver'];
            }
        }

        return '';
    }

    /**
     * mysql client version
     * @return string
     */
    public static function clientVer()
    {
        if (function_exists('mysqli_get_client_version')) {
            $version = @mysqli_get_client_version();
            return (intval($version / 10000)) . '.' . ($version / 100 - intval($version / 10000) * 100);
        }

        return '';
    }
}
