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

use sveil\lib\helper\Deleter;
use sveil\lib\helper\Former;
use sveil\lib\helper\Inputer;
use sveil\lib\helper\Pager;
use sveil\lib\helper\Querier;
use sveil\lib\helper\Saver;
use sveil\lib\helper\Tokener;
use sveil\lib\helper\Validater;
use sveil\App;
use sveil\Container;
use sveil\db\exception\DataNotFoundException;
use sveil\db\exception\ModelNotFoundException;
use sveil\db\Query;
use sveil\Exception;
use sveil\exception\DbException;
use sveil\exception\HttpResponseException;
use sveil\exception\PDOException;
use sveil\Response;

/**
 * Standard controller base class
 *
 * Abstract Class Controller
 * @author Richard <richard@sveil.com>
 * @package sveil
 */
abstract class Controller extends \stdClass
{

    /**
     * Current application examples
     * @var App
     */
    public $app;

    /**
     * Current request object
     * @var \sveil\Request
     */
    public $request;

    /**
     * Form CSRF verification status
     * @var boolean
     */
    public $csrf_state = false;

    /**
     * Form CSRF verification failure prompt message
     * @var string
     */
    public $csrf_message = '';

    /**
     * Controller constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {

        $this->app     = $app;
        $this->request = $app->request;

        // Controller injection container
        Container::set('sveil\Controller', $this);

        if (in_array($this->request->action(), get_class_methods(__CLASS__))) {
            $this->error('Access without permission.');
        }

        // Initialize the controller
        $this->initialize();

        // Post-controller operation
        if (method_exists($this, $method = "_{$this->request->action()}_{$this->request->method()}")) {
            $this->app->hook->add('app_end', function (Response $response) use ($method) {
                try {
                    [ob_start(), ob_clean()];
                    $return = call_user_func_array([$this, $method], $this->request->route());
                    if (is_string($return)) {
                        $response->content($response->getContent() . $return);
                    } elseif ($return instanceof Response) {
                        $this->__mergeResponse($response, $return);
                    }
                } catch (HttpResponseException $exception) {
                    $this->__mergeResponse($response, $exception->getResponse());
                } catch (\Exception $exception) {
                    throw $exception;
                }
            });
        }

    }

    /**
     * Merge request object
     *
     * @param Response $response Target response object
     * @param Response $source Data source response object
     * @return Response
     */
    private function __mergeResponse(Response $response, Response $source)
    {

        $response->code($source->getCode())->content($response->getContent() . $source->getContent());
        foreach ($source->getHeader() as $name => $value) {
            if (!empty($name) && is_string($name)) {
                $response->header($name, $value);
            }
        }

        return $response;
    }

    /**
     * Controller initialization
     */
    protected function initialize()
    {

        if (empty($this->csrf_message)) {
            $this->csrf_message = lang('lib_csrf_error');
        }

    }

    /**
     * Return failed operation
     *
     * @param mixed $info Message content
     * @param array $data Return data
     * @param integer $code Return code
     */
    public function error($info, $data = [], $code = 0)
    {

        $result = ['code' => $code, 'info' => $info, 'data' => $data];
        throw new HttpResponseException(json($result));

    }

    /**
     * Return successful operation
     *
     * @param mixed $info Message content
     * @param array $data Return data
     * @param integer $code Return code
     */
    public function success($info, $data = [], $code = 1)
    {

        if ($this->csrf_state) {
            Tokener::instance()->clear();
        }

        throw new HttpResponseException(json([
            'code' => $code, 'info' => $info, 'data' => $data,
        ]));

    }

    /**
     * URL redirect
     *
     * @param string $url Jump link
     * @param array $vars Jump parameters
     * @param integer $code Jump code
     */
    public function redirect($url, $vars = [], $code = 301)
    {
        throw new HttpResponseException(redirect($url, $vars, $code));
    }

    /**
     * Back to view content
     *
     * @param string $tpl Template name
     * @param array $vars Template variables
     * @param string $node CSRF authorized node
     */
    public function fetch($tpl = '', $vars = [], $node = null)
    {

        foreach ($this as $name => $value) {
            $vars[$name] = $value;
        }

        if ($this->csrf_state) {
            Tokener::instance()->fetchTemplate($tpl, $vars, $node);
        } else {
            throw new HttpResponseException(view($tpl, $vars));
        }

    }

    /**
     * Template variables assignment
     * @param mixed $name To be displayed template variables
     * @param mixed $value Variable value
     * @return $this
     */
    public function assign($name, $value = '')
    {

        if (is_string($name)) {
            $this->$name = $value;
        } elseif (is_array($name)) {
            foreach ($name as $k => $v) {
                if (is_string($k)) {
                    $this->$k = $v;
                }
            }
        }

        return $this;
    }

    /**
     * Data callback processing mechanism
     *
     * @param string $name Callback method name
     * @param mixed $one Callback reference parameter one
     * @param mixed $two Callback reference parameter two
     * @return boolean
     */
    public function callback($name, &$one = [], &$two = [])
    {

        if (is_callable($name)) {
            return call_user_func($name, $this, $one, $two);
        }

        foreach ([$name, "_{$this->request->action()}{$name}"] as $method) {
            if (method_exists($this, $method)) {
                if (false === $this->$method($one, $two)) {
                    return false;
                }
            }

        }

        return true;
    }

    /**
     * Check form token verification
     *
     * @param boolean $return Whether to return the result
     * @return boolean
     */
    protected function applyCsrfToken($return = false)
    {
        return Tokener::instance()->init($return);
    }

    /**
     * Quick query logic
     *
     * @param string|Query $dbQuery
     * @return Querier
     */
    protected function _query($dbQuery)
    {
        return Querier::instance()->init($dbQuery);
    }

    /**
     * Quick paging logic
     *
     * @param string|Query $dbQuery
     * @param boolean $page Whether to enable paging
     * @param boolean $display Whether to render the template
     * @param boolean $total Collection paging records
     * @param integer $limit Collection records per page
     * @return array
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    protected function _page($dbQuery, $page = true, $display = true, $total = false, $limit = 0)
    {
        return Pager::instance()->init($dbQuery, $page, $display, $total, $limit);
    }

    /**
     * Shortcut form logic
     * @param string|Query $dbQuery
     * @param string $template Template name
     * @param string $field Specify the primary key of the data object
     * @param array $where Additional update conditions
     * @param array $data Form extension data
     * @return array|boolean
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    protected function _form($dbQuery, $template = '', $field = '', $where = [], $data = [])
    {
        return Former::instance()->init($dbQuery, $template, $field, $where, $data);
    }

    /**
     * Quick update logic
     *
     * @param string|Query $dbQuery
     * @param array $data Form extension data
     * @param string $field Specify the primary key of the data object
     * @param array $where Additional update conditions
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    protected function _save($dbQuery, $data = [], $field = '', $where = [])
    {
        return Saver::instance()->init($dbQuery, $data, $field, $where);
    }

    /**
     * Quickly enter and verify（ Support Rule # Alias ）
     * @param array $rules Validation rules（ Validation information array ）
     * @param string $type Input method ( post or get )
     * @return array
     */
    protected function _vali(array $rules, $type = '')
    {
        return Validater::instance()->init($rules, $type);
    }

    /**
     * Quick input logic
     *
     * @param array|string $data verify the data
     * @param array $rule Validation rules
     * @param array $info verification message
     * @return array
     */
    protected function _input($data, $rule = [], $info = [])
    {
        return Inputer::instance()->init($data, $rule, $info);
    }

    /**
     * Quick delete logic
     *
     * @param string|Query $dbQuery
     * @param string $field Specify the primary key of the data object
     * @param array $where Additional update conditions
     * @return boolean|null
     * @return boolean|null
     * @throws Exception
     * @throws PDOException
     */
    protected function _delete($dbQuery, $field = '', $where = [])
    {
        return Deleter::instance()->init($dbQuery, $field, $where);
    }

}
