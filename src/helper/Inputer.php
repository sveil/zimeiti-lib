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
use think\Validate;

/**
 * Input data assistant
 *
 * Class Inputer
 * @author Richard <richard@sveil.com>
 * @package sveil\helper
 */
class Inputer extends Helper
{

    /**
     * Validator rules
     * @var array
     */
    protected $rule;

    /**
     * Data to be verified
     * @var array
     */
    protected $data;

    /**
     * Verification result message
     * @var array
     */
    protected $info;

    /**
     * Parsing input data
     *
     * @param array|string $data
     * @param array $result
     * @return array
     */
    private function parse($data, $result = [])
    {

        if (is_array($data)) {
            return $data;
        }

        if (is_string($data)) {
            foreach (explode(',', $data) as $field) {
                if (strpos($field, '#') === false) {
                    $array               = explode('.', $field);
                    $result[end($array)] = input($field);
                } else {
                    list($name, $value)  = explode('#', $field);
                    $array               = explode('.', $name);
                    $result[end($array)] = input($name, $value);
                }
            }
        }

        return $result;
    }

    /**
     * Input validator
     *
     * @param array $data
     * @param array $rule
     * @param array $info
     * @return array
     */
    public function init($data, $rule, $info)
    {

        list($this->rule, $this->info) = [$rule, $info];
        $this->data                    = $this->parse($data);
        $validate                      = Validate::make($this->rule, $this->info);

        if ($validate->check($this->data)) {
            return $this->data;
        } else {
            $this->controller->error($validate->getError());
        }

    }

}
