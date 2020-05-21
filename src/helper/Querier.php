<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
// +----------------------------------------------------------------------

namespace sveil\helper;

use sveil\Helper;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

/**
 * Query data assistant
 *
 * Class Querier
 * @author Richard <richard@sveil.com>
 * @package sveil\helper
 */
class Querier extends Helper
{

    /**
     * Query call
     *
     * @param string $name Calling method name
     * @param array $args Call parameter content
     * @return Querier
     */
    public function __call($name, $args)
    {

        if (is_callable($callable = [$this->query, $name])) {
            call_user_func_array($callable, $args);
        }

        return $this;
    }

    /**
     * Logic initialization
     *
     * @param string|Query $dbQuery
     * @return $this
     */
    public function init($dbQuery)
    {

        $this->query = $this->buildQuery($dbQuery);

        return $this;
    }

    /**
     * Get the current Db operation object
     *
     * @return Query
     */
    public function db()
    {
        return $this->query;
    }

    /**
     * Set Like query conditions
     *
     * @param string|array $fields Query field
     * @param string $input Input type get|post
     * @param string $alias Alias delimiter
     * @return $this
     */
    public function like($fields, $input = 'request', $alias = '#')
    {

        $data = $this->app->request->$input();

        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query->whereLike($dk, "%{$data[$qk]}%");
            }
        }

        return $this;
    }

    /**
     * Set Equal query conditions
     *
     * @param string|array $fields Query field
     * @param string $input Input type get|post
     * @param string $alias Alias delimiter
     * @return $this
     */
    public function equal($fields, $input = 'request', $alias = '#')
    {

        $data = $this->app->request->$input();

        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query->where($dk, "{$data[$qk]}");
            }
        }

        return $this;
    }

    /**
     * Set IN interval query
     *
     * @param string $fields Query field
     * @param string $split splitter
     * @param string $input Input type get|post
     * @param string $alias Alias delimiter
     * @return $this
     */
    public function in($fields, $split = ',', $input = 'request', $alias = '#')
    {

        $data = $this->app->request->$input();

        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                $this->query->whereIn($dk, explode($split, $data[$qk]));
            }
        }

        return $this;
    }

    /**
     * Set content interval query
     *
     * @param string|array $fields Query field
     * @param string $split splitter
     * @param string $input Input type get|post
     * @param string $alias Alias delimiter
     * @return $this
     */
    public function valueBetween($fields, $split = ' ', $input = 'request', $alias = '#')
    {
        return $this->setBetweenWhere($fields, $split, $input, $alias);
    }

    /**
     * Set date and time interval query
     *
     * @param string|array $fields Query field
     * @param string $split splitter
     * @param string $input Input type
     * @param string $alias Alias delimiter
     * @return $this
     */
    public function dateBetween($fields, $split = ' - ', $input = 'request', $alias = '#')
    {
        return $this->setBetweenWhere($fields, $split, $input, $alias, function ($value, $type) {
            if ($type === 'after') {
                return "{$value} 23:59:59";
            } else {
                return "{$value} 00:00:00";
            }
        });
    }

    /**
     * Set timestamp interval query
     *
     * @param string|array $fields Query field
     * @param string $split splitter
     * @param string $input Input type
     * @param string $alias Alias delimiter
     * @return $this
     */
    public function timeBetween($fields, $split = ' - ', $input = 'request', $alias = '#')
    {
        return $this->setBetweenWhere($fields, $split, $input, $alias, function ($value, $type) {
            if ($type === 'after') {
                return strtotime("{$value} 23:59:59");
            } else {
                return strtotime("{$value} 00:00:00");
            }
        });
    }

    /**
     * Set area query conditions
     *
     * @param string|array $fields Query field
     * @param string $split splitter
     * @param string $input Input type
     * @param string $alias Alias delimiter
     * @param callable $callback
     * @return $this
     */
    private function setBetweenWhere($fields, $split = ' ', $input = 'request', $alias = '#', $callback = null)
    {

        $data = $this->app->request->$input();

        foreach (is_array($fields) ? $fields : explode(',', $fields) as $field) {
            list($dk, $qk) = [$field, $field];
            if (stripos($field, $alias) !== false) {
                list($dk, $qk) = explode($alias, $field);
            }
            if (isset($data[$qk]) && $data[$qk] !== '') {
                list($begin, $after) = explode($split, $data[$qk]);
                if (is_callable($callback)) {
                    $after = call_user_func($callback, $after, 'after');
                    $begin = call_user_func($callback, $begin, 'begin');
                }
                $this->query->whereBetween($dk, [$begin, $after]);
            }
        }

        return $this;
    }

    /**
     * Instantiate the paging manager
     *
     * @param boolean $page Whether to enable paging
     * @param boolean $display Whether to render the template
     * @param boolean $total Collection paging records
     * @param integer $limit Collection records per page
     * @return mixed
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public function page($page = true, $display = true, $total = false, $limit = 0)
    {
        return Pager::instance()->init($this->query, $page, $display, $total, $limit);
    }

}
