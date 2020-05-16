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

namespace sveil;

use think\App;
use think\Container;
use think\Request;

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
