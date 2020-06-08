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

namespace sveil\lib\service;

use sveil\App;
use sveil\Exception;
use sveil\exception\PDOException;
use sveil\lib\common\Data;
use sveil\lib\Db;
use sveil\lib\Service;

/**
 * Class Install
 * Install data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Install extends Service
{
    /**
     * index object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function index()
    {
        return [
            'title'    => config('app_name') . config('site_name'),
            'siteIcon' => '/upload/decb0fe26fa3f486/b3f6521bf29403c8.png',
        ];
    }

    /**
     * check object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function check()
    {
        $disabled   = 0;
        $serverName = request()->server('SERVER_NAME') . ' (' . request()->server('HTTP_HOST') . ')';

        $pass  = '<strong><span class="color-green">√</span></strong>';
        $error = '<strong><span class="color-red">×</span></strong>';

        $phpVerComp = version_compare(PHP_VERSION, '5.6.0', '>=') ? $pass : $error;
        $phpVer     = $phpVerComp . '&nbsp;' . PHP_VERSION;
        $disabled   = $disabled ? 1 : (version_compare(PHP_VERSION, '5.6.0', '>=') ? 0 : 1);

        $timeComp = ini_get('max_execution_time') >= 300 ? $pass : $error;
        $timeout  = $timeComp . '&nbsp;' . ini_get('max_execution_time');
        $disabled = $disabled ? 1 : (ini_get('max_execution_time') >= 300 ? 0 : 1);

        $mysqlVers    = mysqlClientVersion() ? mysqlClientVersion() : '0.0.0';
        $mysqlVerComp = version_compare($mysqlVers, '5.0.1', '>=') ? $pass : $error;
        $mysqlVer     = $mysqlVerComp . '&nbsp;' . $mysqlVers;
        $disabled     = $disabled ? 1 : (version_compare($mysqlVers, '5.0.1', '>=') ? 0 : 1);

        $curlInit = function_exists('curl_init') ? $pass : $error;
        $disabled = $disabled ? 1 : (function_exists('curl_init') ? 0 : 1);

        $getContents = function_exists('file_get_contents') ? $pass : $error;
        $disabled    = $disabled ? 1 : (function_exists('file_get_contents') ? 0 : 1);

        $gdInfo   = gd_info() ? $pass : $error;
        $gd       = $gdInfo . '&nbsp;' . gd_info()['GD Version'];
        $disabled = $disabled ? 1 : (gd_info() ? 0 : 1);

        $rdPass  = '<span class="color-green">[√]读</span>';
        $rdError = '<span class="color-red">[×]读</span>';
        $wtPass  = '<span class="color-green">[√]写</span>';
        $wtError = '<span class="color-red">[×]写</span>';

        $configDir = env('config_path');
        $configs   = isRead($configDir) ? $rdPass : $rdError;
        $disabled  = $disabled ? 1 : (isRead($configDir) ? 0 : 1);
        $configs   = $configs . '&nbsp;/&nbsp;' . (isWrite($configDir) ? $wtPass : $wtError);
        $disabled  = $disabled ? 1 : (isWrite($configDir) ? 0 : 1);

        $runtimeDir = env('runtime_path');
        $runtimes   = isRead($runtimeDir) ? $rdPass : $rdError;
        $disabled   = $disabled ? 1 : (isRead($runtimeDir) ? 0 : 1);
        $runtime    = $runtimes . '&nbsp;/&nbsp;' . (isWrite($runtimeDir) ? $wtPass : $wtError);
        $disabled   = $disabled ? 1 : (isWrite($runtimeDir) ? 0 : 1);

        $uploadDir = request()->server('DOCUMENT_ROOT') . '/' . config('upload_dir');
        $uploads   = isRead($uploadDir) ? $rdPass : $rdError;
        $disabled  = $disabled ? 1 : (isRead($uploadDir) ? 0 : 1);
        $upload    = $uploads . '&nbsp;/&nbsp;' . (isWrite($uploadDir) ? $wtPass : $wtError);
        $disabled  = $disabled ? 1 : (isWrite($uploadDir) ? 0 : 1);

        return [
            'serverName'  => $serverName,
            'serverSoft'  => $_SERVER['SERVER_SOFTWARE'],
            'sapiName'    => php_sapi_name(),
            'uploadSize'  => ini_get('upload_max_filesize'),
            'appName'     => config('app_name') . config('site_name'),
            'appVer'      => config('app_version'),
            'coreVer'     => App::VERSION,
            'phpUname'    => php_uname('s'),
            'postSize'    => ini_get('post_max_size'),
            'phpVer'      => $phpVer,
            'timeout'     => $timeout,
            'mysqlVer'    => $mysqlVer,
            'curlInit'    => $curlInit,
            'getContents' => $getContents,
            'gd'          => $gd,
            'configs'     => $configs,
            'runtime'     => $runtime,
            'upload'      => $upload,
            'disable'     => $disabled,
        ];
    }

    /**
     * db object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function db($post = '')
    {
        if ('' === $post) {
            return [
                'dbhost'   => '127.0.0.1',
                'dbport'   => '3306',
                'dbname'   => 'sveil',
                'dbuser'   => 'root',
                'dbprefix' => 'sve_',
                'wechat'   => 'richard@sveil.com',
                'username' => 'admin',
                'email'    => 'support@sveil.com',
                'mobile'   => '13888888888',
            ];
        } else {
            if ($post['password'] != $post['repassword']) {
                return ['error' => '两次密码不一致。'];
            }

            if ($post['username'] === 'administrator' || $post['username'] === 'admin') {
                return ['error' => '创始人账号不能使用administrator或admin。'];
            }

            $configDb  = env('CONFIG_PATH') . 'database.php';
            $configOld = require $configDb;
            $dbConfig  = [
                'hostname' => $post['dbhost'],
                'hostport' => $post['dbport'],
                'username' => $post['dbuser'],
                'password' => $post['dbpwd'],
                'charset'  => $configOld['charset'],
                'database' => 'mysql',
            ];

            if (!Db::connect($dbConfig)) {
                return ['error' => '无法连接数据库，请查看数据库账号密码是否正确？'];
            }

            if (Db::noPermit()) {
                return ['error' => '操作数据库权限不够，请查看是否有数据库增删改查的权限？'];
            }

            if (version_compare(Db::serverVer(), '5.7.0', '<')) {
                return ['error' => '数据库版本太低，请更换数据库！'];
            }

            $dbConfig['database'] = $post['dbname'];

            if (!Db::connect($dbConfig)) {
                $dbConfig['database'] = 'mysql';
                $db                   = Db::connect($dbConfig);
                $db->query("create database " . $post['dbname']);
            }

            $configMo = env('CONFIG_PATH') . 'module.php';

            try {
                $moConfig = require $configMo;

                foreach ($moConfig as $k => $v) {
                    $moConfig[$k] = Data::randomCode(6, 2);
                }

                arrFiles($configMo, $moConfig);
            } catch (\Exception $e) {
                $this->error("写入应用配置文件错误，请查看是否有文件管理权限？");
            }

            // try {
            //     $dbConfig['debug']    = false;
            //     $dbConfig['database'] = $post['dbname'];
            //     $dbConfig['prefix']   = $post['dbprefix'];
            //     $configNew            = array_merge($dbConfigOld, $dbConfig);
            //     arrFiles(env('config_path') . 'database.php', $configNew);
            // } catch (\Exception $e) {
            //     $this->error("写入数据库配置文件错误，请查看是否有文件管理权限？");
            // }

            // try {
            //     $sql = readFiles(env('DOC_PATH') . '/install.sql');
            //     $sql = str_replace('sve_', $post['dbprefix'], $sql);
            //     // $sql = str_replace('admin', $appConfig['module.manage'], $sql);
            //     $sql                  = str_replace("\r\n", "\n", $sql);
            //     $dbConfig['database'] = $post['dbname'];
            //     $db                   = DB::connect($dbConfig);
            //     $db->query($sql);
            // } catch (\Exception $e) {
            //     $this->error("无法导入数据库，请检查数据库安装是否正确？");
            // }

            // $dbConfig['database'] = $post['dbname'];
            // $dbh                  = DB::connect($dbConfig, [\PDO::ATTR_PERSISTENT => true]);
            // try {
            //     $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            //     $dbh->beginTransaction();
            //     $dbh->exec("insert into " . $post['dbprefix'] . "users (username, password, mail, mobile) values ('" . $post['username'] . "', '" . md5($post['password']) . "', '" . $post['email'] . "', '" . $post['mobile'] . "')");
            //     $dbh->exec("insert into " . $post['dbprefix'] . "users_info (qq) values ('" . $post['mobile'] . "')");
            //     $dbh->commit();
            // } catch (\Exception $e) {
            //     $dbh->rollBack();
            //     $this->error($e->getMessage());
            // }

            // touch(env('DOC_PATH') . 'install.lock');
            return [
                'success' => '安装成功!请确认您的安装信息。',
                'url'     => url('@install') . '#' . url('install/index/info'),
            ];
        }
    }
}
