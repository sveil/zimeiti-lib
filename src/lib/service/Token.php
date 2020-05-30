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

namespace sveil\lib\service;

use sveil\lib\Service;
use sveil\lib\service\Node;

/**
 * Class Token
 * Form Token Management Service
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Token extends Service
{
    /**
     * Get the current request token
     * @return array|string
     */
    public function getInputToken()
    {
        return $this->app->request->header('user-token-csrf', input('_token_', ''));
    }

    /**
     * Verify that the form token is valid
     * @param string $token Form token
     * @param string $node Authorized node
     * @return boolean
     */
    public function checkFormToken($token = null, $node = null)
    {
        if (is_null($token)) {
            $token = $this->getInputToken();
        }

        if (is_null($node)) {
            $node = Node::instance()->getCurrent();
        }

        // Read the cache and check if it is valid
        $cache = $this->app->session->get($token);

        if (empty($cache['node']) || empty($cache['time']) || empty($cache['token'])) {
            return false;
        }

        if ($cache['token'] !== $token || $cache['time'] + 600 < time() || $cache['node'] !== $node) {
            return false;
        }

        return true;
    }

    /**
     * Clean up form CSRF information
     * @param string $token
     * @return $this
     */
    public function clearFormToken($token = null)
    {
        if (is_null($token)) {
            $token = $this->getInputToken();
        }

        $this->app->session->delete($token);

        return $this;
    }

    /**
     * Generate form CSRF information
     * @param null|string $node
     * @return array
     */
    public function buildFormToken($node = null)
    {
        list($token, $time) = [uniqid('csrf'), time()];

        foreach ($this->app->session->get() as $key => $item) {
            if (stripos($key, 'csrf') === 0 && isset($item['time'])) {
                if ($item['time'] + 600 < $time) {
                    $this->clearFormToken($key);
                }

            }
        }

        $data = ['node' => Node::instance()->fullnode($node), 'token' => $token, 'time' => $time];
        $this->app->session->set($token, $data);

        return $data;
    }
}
