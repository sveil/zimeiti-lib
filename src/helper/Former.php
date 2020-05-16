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
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

/**
 * Form management assistant
 *
 * Class Former
 * @author Richard <richard@sveil.com>
 * @package sveil\helper
 */
class Former extends Helper
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
     * Template data
     * @var array
     */
    protected $data;

    /**
     * Template name
     * @var string
     */
    protected $template;

    /**
     * 逻辑器初始化
     * @param string|Query $dbQuery
     * @param string $template Template name
     * @param string $field Operation data primary key
     * @param array $where Additional form update conditions
     * @param array $data Form extension data
     * @return array|mixed
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public function init($dbQuery, $template = '', $field = '', $where = [], $data = [])
    {

        $this->query                                     = $this->buildQuery($dbQuery);
        list($this->template, $this->where, $this->data) = [$template, $where, $data];
        $this->field                                     = empty($field) ? ($this->query->getPk() ? $this->query->getPk() : 'id') : $field;
        $this->value                                     = input($this->field, isset($data[$this->field]) ? $data[$this->field] : null);

        // GET request get data and display form page
        if ($this->app->request->isGet()) {
            if ($this->value !== null) {
                $where = [$this->field => $this->value];
                $data  = (array) $this->query->where($where)->where($this->where)->find();
            }
            $data = array_merge($data, $this->data);
            if (false !== $this->controller->callback('_form_filter', $data)) {
                return $this->controller->fetch($this->template, ['vo' => $data]);
            } else {
                return $data;
            }
        }

        // POST request automatic data storage processing
        if ($this->app->request->isPost()) {
            $data = array_merge($this->app->request->post(), $this->data);
            if (false !== $this->controller->callback('_form_filter', $data, $this->where)) {
                $result = data_save($this->query, $data, $this->field, $this->where);
                if (false !== $this->controller->callback('_form_result', $result, $data)) {
                    if ($result !== false) {
                        $this->controller->success(lang('think_library_form_success'), '');
                    } else {
                        $this->controller->error(lang('think_library_form_error'));
                    }
                }
                return $result;
            }
        }

    }

}
