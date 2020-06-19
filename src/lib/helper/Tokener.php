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

use sveil\exception\HttpResponseException;
use sveil\lib\Helper;
use sveil\lib\service\Token;

/**
 * Class Tokener
 * Token assistant
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\helper
 */
class Tokener extends Helper
{
    /**
     * Initialize the verification code
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
