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

namespace sveil\lib\helper;

use sveil\lib\Helper;
use think\Validate;

/**
 * Validator assistant
 *
 * Class Validater
 * @author Richard <richard@sveil.com>
 * @package sveil\helper
 */
class Validater extends Helper
{

    /**
     * Quick input and verification (support rule # alias)
     * @param array $rules Verification rules (verification information array)
     * @param string $type Input method (post Or get)
     * @return array
     */
    public function init(array $rules, $type = '')
    {
        list($data, $rule, $info) = [[], [], []];
        foreach ($rules as $name => $message) {
            if (stripos($name, '#') !== false) {
                list($name, $alias) = explode('#', $name);
            }
            if (stripos($name, '.') === false) {
                if (is_numeric($name)) {
                    $keys = $message;
                    if (is_string($message) && stripos($message, '#') !== false) {
                        list($name, $alias) = explode('#', $message);
                        $keys               = empty($alias) ? $name : $alias;
                    }
                    $data[$name] = input("{$type}{$keys}");
                } else {
                    $data[$name] = $message;
                }
            } else {
                list($_rgx)         = explode(':', $name);
                list($_key, $_rule) = explode('.', $name);
                $keys               = empty($alias) ? $_key : $alias;
                $info[$_rgx]        = $message;
                $data[$_key]        = input("{$type}{$keys}");
                $rule[$_key]        = empty($rule[$_key]) ? $_rule : "{$rule[$_key]}|{$_rule}";
            }
        }
        $validate = new Validate();
        if ($validate->rule($rule)->message($info)->check($data)) {
            return $data;
        } else {
            $this->controller->error($validate->getError());
        }
    }

}
