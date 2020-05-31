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

use sveil\lib\Service;
use sveil\lib\service\Node;
use sveil\think\Db;
use sveil\think\db\exception\DataNotFoundException;
use sveil\think\db\exception\ModelNotFoundException;
use sveil\think\db\Query;
use sveil\think\Exception;
use sveil\think\exception\DbException;
use sveil\think\exception\PDOException;

/**
 * Class System
 * System parameter management service
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class System extends Service
{
    /**
     * Configure data caching
     * @var array
     */
    protected $data = [];

    /**
     * Data incremental storage
     *
     * @param Query|string $dbQuery Data query object
     * @param array $data Data to be saved or updated
     * @param string $key Primary key restrictions by condition
     * @param array $where Other where conditions
     * @return bool|int|mixed|string
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public function save($dbQuery, $data, $key = 'id', $where = [])
    {
        $db                  = is_string($dbQuery) ? Db::name($dbQuery) : $dbQuery;
        list($table, $value) = [$db->getTable(), isset($data[$key]) ? $data[$key] : null];
        $map                 = isset($where[$key]) ? [] : (is_string($value) ? [[$key, 'in', explode(',', $value)]] : [$key => $value]);

        if (is_array($info = Db::table($table)->master()->where($where)->where($map)->find()) && !empty($info)) {
            if (Db::table($table)->strict(false)->where($where)->where($map)->update($data) !== false) {
                return isset($info[$key]) ? $info[$key] : true;
            } else {
                return false;
            }
        } else {
            return Db::table($table)->strict(false)->insertGetId($data);
        }
    }

    /**
     * Save data content
     * @param string $name Data name
     * @param mixed $value Data content
     * @return boolean
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public function setData($name, $value)
    {
        $data = ['name' => $name, 'value' => serialize($value)];

        return $this->save('SystemData', $data, 'name');
    }

    /**
     * Read data content
     * @param string $name Data name
     * @param mixed $default Default
     * @return mixed
     */
    public function getData($name, $default = [])
    {
        try {
            $value = Db::name('SystemData')->where(['name' => $name])->value('value');
            return empty($value) ? $default : unserialize($value);
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Write to system log
     * @param string $action
     * @param string $content
     * @return integer
     */
    public function setOplog($action, $content)
    {
        return Db::name('SystemLog')->insert([
            'node'     => Node::instance()->getCurrent(),
            'action'   => $action, 'content' => $content,
            'geoip'    => $this->app->request->isCli() ? '127.0.0.1' : $this->app->request->ip(),
            'username' => $this->app->request->isCli() ? 'cli' : $this->app->session->get('user.username'),
        ]);
    }

    /**
     * Print output data to file
     * @param mixed $data Output data
     * @param boolean $new Force file replacement
     * @param string|null $file file name
     */
    public function putDebug($data, $new = false, $file = null)
    {
        if (is_null($file)) {
            $file = $this->app->getRuntimePath() . date('Ymd') . '.txt';
        }

        $str = (is_string($data) ? $data : ((is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true))) . PHP_EOL;
        $new ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }

    /**
     * Determine the operating environment
     * @param string $type Operating mode（dev|demo|local）
     * @return boolean
     */
    public function checkRunMode($type = 'dev')
    {
        $domain  = $this->app->request->host(true);
        $isDemo  = is_numeric(stripos($domain, 'thinkadmin.top'));
        $isLocal = in_array($domain, ['127.0.0.1', 'localhost']);

        if ($type === 'dev') {
            return $isLocal || $isDemo;
        }

        if ($type === 'demo') {
            return $isDemo;
        }

        if ($type === 'local') {
            return $isLocal;
        }

        return true;
    }
}
