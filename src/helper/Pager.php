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
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

/**
 * Page management assistant
 *
 * Class Pager
 * @author Richard <richard@sveil.com>
 * @package sveil\helper
 */
class Pager extends Helper
{

    /**
     * Whether to enable paging
     * @var boolean
     */
    protected $page;

    /**
     * Collection paging records
     * @var integer
     */
    protected $total;

    /**
     * Collection records per page
     * @var integer
     */
    protected $limit;

    /**
     * Whether to render the template
     * @var boolean
     */
    protected $display;

    /**
     * Logic initialization
     *
     * @param string|Query $dbQuery
     * @param boolean $page Whether to enable paging
     * @param boolean $display Whether to render the template
     * @param boolean $total Collection paging records
     * @param integer $limit Collection records per page
     * @return array|mixed
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public function init($dbQuery, $page = true, $display = true, $total = false, $limit = 0)
    {

        $this->page    = $page;
        $this->total   = $total;
        $this->limit   = $limit;
        $this->display = $display;
        $this->query   = $this->buildQuery($dbQuery);

        // List sort operation
        if ($this->controller->request->isPost()) {
            $this->_sort();
        }

        // Automatically sort by sort field when no order rule is configured
        if (!$this->query->getOptions('order') && method_exists($this->query, 'getTableFields')) {
            if (in_array('sort', $this->query->getTableFields())) {
                $this->query->order('sort desc');
            }

        }

        // List paging and result set processing
        if ($this->page) {
            // Display the number of records per page
            $limit = intval($this->controller->request->get('limit', cookie('page-limit')));
            cookie('page-limit', $limit = $limit >= 10 ? $limit : 20);
            if ($this->limit > 0) {
                $limit = $this->limit;
            }
            $rows = [];
            $page = $this->query->paginate($limit, $this->total, ['query' => ($query = $this->controller->request->get())]);
            foreach ([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180, 190, 200] as $num) {
                list($query['limit'], $query['page'], $selected) = [$num, '1', $limit === $num ? 'selected' : ''];
                $url                                             = url('@admin') . '#' . $this->controller->request->baseUrl() . '?' . urldecode(http_build_query($query));
                array_push($rows, "<option data-num='{$num}' value='{$url}' {$selected}>{$num}</option>");
            }
            $selects  = "<select onchange='location.href=this.options[this.selectedIndex].value' data-auto-none>" . join('', $rows) . "</select>";
            $pagetext = lang('think_library_page_html', [$page->total(), $selects, $page->lastPage(), $page->currentPage()]);
            $pagehtml = "<div class='pagination-container nowrap'><span>{$pagetext}</span>{$page->render()}</div>";
            $this->controller->assign('pagehtml', preg_replace('|href="(.*?)"|', 'data-open="$1" onclick="return false" href="$1"', $pagehtml));
            $result = ['page' => ['limit' => intval($limit), 'total' => intval($page->total()), 'pages' => intval($page->lastPage()), 'current' => intval($page->currentPage())], 'list' => $page->items()];
        } else {
            $result = ['list' => $this->query->select()];
        }
        if (false !== $this->controller->callback('_page_filter', $result['list']) && $this->display) {
            return $this->controller->fetch('', $result);
        } else {
            return $result;
        }

    }

    /**
     * List sort operation
     *
     * @throws Exception
     * @throws PDOException
     */
    protected function _sort()
    {

        switch (strtolower($this->controller->request->post('action', ''))) {
            case 'resort':
                foreach ($this->controller->request->post() as $key => $value) {
                    if (preg_match('/^_\d{1,}$/', $key) && preg_match('/^\d{1,}$/', $value)) {
                        list($where, $update) = [['id' => trim($key, '_')], ['sort' => $value]];
                        if (false === Db::table($this->query->getTable())->where($where)->update($update)) {
                            return $this->controller->error(lang('think_library_sort_error'));
                        }
                    }
                }
                return $this->controller->success(lang('think_library_sort_success'), '');
            case 'sort':
                $where = $this->controller->request->post();
                $sort  = intval($this->controller->request->post('sort'));
                unset($where['action'], $where['sort']);
                if (Db::table($this->query->getTable())->where($where)->update(['sort' => $sort]) !== false) {
                    return $this->controller->success(lang('think_library_sort_success'), '');
                } else {
                    return $this->controller->error(lang('think_library_sort_error'));
                }
        }

    }

}
