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

namespace sveil;

use sveil\App;
use sveil\Container;
use sveil\Request;

/**
 * Abstract Class Service
 * Service base class
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
     * @var \sveil\Request
     */
    protected $request;

    /**
     * Service constructor
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
     * @return $this
     */
    protected function initialize()
    {
        return $this;
    }

    /**
     * Static instance object
     * @return static
     */
    public static function instance()
    {
        return Container::getInstance()->make(static::class);
    }
}
