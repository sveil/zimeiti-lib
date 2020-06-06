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
}
