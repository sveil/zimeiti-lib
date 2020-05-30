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

use sveil\think\App;
use sveil\think\Container;
use sveil\think\Request;

/**
 * Service base class
 *
 * Abstract Class Service
 * @author Richard <richard@sveil.com>
 * @package sveil
 */
abstract class Service
{

    /**
     * Current example application
     * @var App
     */
    protected $app;

    /**
     * Current request object
     * @var \think\Request
     */
    protected $request;

    /**
     * Service constructor
     *
     * @param App $app
     * @param Request $request
     */
    public function __construct(App $app, Request $request)
    {

        $this->app     = $app;
        $this->request = $request;
        $this->initialize();

    }

    /**
     * Initialize the service
     *
     * @return $this
     */
    protected function initialize()
    {
        return $this;
    }

    /**
     * Static instance object
     *
     * @return static
     */
    public static function instance()
    {
        return Container::getInstance()->make(static::class);
    }

}