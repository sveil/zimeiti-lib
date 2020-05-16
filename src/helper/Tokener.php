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
use sveil\service\Token;
use think\exception\HttpResponseException;

/**
 * Token assistant
 *
 * Class Tokener
 * @author Richard <richard@sveil.com>
 * @package sveil\helper
 */
class Tokener extends Helper
{

    /**
     * Initialize the verification code
     *
     * @param boolean $return
     * @return boolean
     */
    public function init($return = false)
    {

        $this->controller->csrf_state = true;

        if ($this->app->request->isPost() && !Token::instance()->checkFormToken()) {
            if ($return) {
                return false;
            }

            $this->controller->error($this->controller->csrf_message);
        } else {
            return true;
        }

    }

    /**
     * Clean up form tokens
     */
    public function clear()
    {
        Token::instance()->clearFormToken();
    }

    /**
     * Back to view content
     * @param string $tpl Template name
     * @param array $vars Template variables
     * @param string $node CSRF authorized node
     */
    public function fetchTemplate($tpl = '', $vars = [], $node = null)
    {

        throw new HttpResponseException(view($tpl, $vars, 200, function ($html) use ($node) {
            return preg_replace_callback('/<\/form>/i', function () use ($node) {
                $csrf = TokenService::instance()->buildFormToken($node);
                return "<input type='hidden' name='_token_' value='{$csrf['token']}'></form>";
            }, $html);
        }));

    }

}
