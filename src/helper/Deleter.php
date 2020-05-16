<?php

// +----------------------------------------------------------------------
// | Library for Sveil
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/boy12371/think-lib
// | github：https://github.com/boy12371/think-lib
// +----------------------------------------------------------------------

namespace sveil\helper;

use sveil\Helper;
use think\db\Query;
use think\Exception;
use think\exception\PDOException;

/**
 * Delete Data Assistant
 *
 * Class Deleter
 * @author Richard <richard@sveil.com>
 * @package sveil\helper
 */
class Deleter extends Helper
{

    /**
     * Additional form update conditions
     * @var array
     */
    protected $where;

    /**
     * Data object primary key name
     * @var string
     */
    protected $field;

    /**
     * Data object primary key value
     * @var string
     */
    protected $value;

    /**
     * Logic initialization
     *
     * @param string|Query $dbQuery
     * @param string $field Operation data primary key
     * @param array $where Additional form update conditions
     * @return boolean|null
     * @throws Exception
     * @throws PDOException
     */
    public function init($dbQuery, $field = '', $where = [])
    {

        $this->where = $where;
        $this->query = $this->buildQuery($dbQuery);
        $this->field = empty($field) ? $this->query->getPk() : $field;
        $this->value = $this->app->request->post($this->field, null);

        // Primary key restriction processing
        if (!isset($this->where[$this->field]) && is_string($this->value)) {
            $this->query->whereIn($this->field, explode(',', $this->value));
        }

        // Pre-callback processing
        if (false === $this->controller->callback('_delete_filter', $this->query, $where)) {
            return null;
        }

        // Perform delete operation
        if (method_exists($this->query, 'getTableFields') && in_array('is_deleted', $this->query->getTableFields())) {
            $result = $this->query->where($this->where)->update(['is_deleted' => '1']);
        } else {
            $result = $this->query->where($this->where)->delete();
        }

        // Result callback processing
        if (false === $this->controller->callback('_delete_result', $result)) {
            return $result;
        }

        // Reply to front-end results
        if ($result !== false) {
            $this->controller->success(lang('think_library_delete_success'), '');
        } else {
            $this->controller->error(lang('think_library_delete_error'));
        }

    }

}
