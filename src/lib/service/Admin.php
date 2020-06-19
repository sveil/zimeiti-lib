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

use sveil\Db;
use sveil\db\exception\DataNotFoundException;
use sveil\db\exception\ModelNotFoundException;
use sveil\exception\DbException;
use sveil\lib\common\Data;
use sveil\lib\Service;
use sveil\lib\service\Node;

/**
 * System authority management service
 *
 * Class Admin
 * @package sveil\lib\service
 */
class Admin extends Service
{

    /**
     * Determine if you are already logged in
     *
     * @return boolean
     */
    public function isLogin()
    {
        return $this->app->session->get('user.id') ? true : false;
    }

    /**
     * Check the specified node authorization, Need to read cache or scan all nodes
     *
     * @param string $node
     * @return boolean
     * @throws \ReflectionException
     */
    public function check($node = '')
    {

        $service = Node::instance();

        if ($this->app->session->get('user.rid') === 0) {
            return true;
        }

        list($real, $nodes) = [$service->fullnode($node), $service->getMethods()];

        if (empty($nodes[$real]['isauth'])) {
            return empty($nodes[$real]['islogin']) ? true : $this->isLogin();
        } else {
            return in_array($real, (array) $this->app->session->get('user.nodes'));
        }

    }

    /**
     * Get a list of authorized nodes
     *
     * @param array $checkeds
     * @return array
     * @throws \ReflectionException
     */
    public function getTree($checkeds = [])
    {

        list($nodes, $pnodes) = [[], []];
        $methods              = array_reverse(Node::instance()->getMethods());

        foreach ($methods as $node => $method) {
            $count = substr_count($node, '/');
            $pnode = substr($node, 0, strripos($node, '/'));
            if ($count === 2 && !empty($method['isauth'])) {
                in_array($pnode, $pnodes) or array_push($pnodes, $pnode);
                $nodes[$node] = ['node' => $node, 'title' => $method['title'], 'pnode' => $pnode, 'checked' => in_array($node, $checkeds)];
            } elseif ($count === 1 && in_array($pnode, $pnodes)) {
                $nodes[$node] = ['node' => $node, 'title' => $method['title'], 'pnode' => $pnode, 'checked' => in_array($node, $checkeds)];
            }
        }

        foreach (array_keys($nodes) as $key) {
            foreach ($methods as $node => $method) {
                if (stripos($key, "{$node}/") !== false) {
                    $pnode         = substr($node, 0, strripos($node, '/'));
                    $nodes[$node]  = ['node' => $node, 'title' => $method['title'], 'pnode' => $pnode, 'checked' => in_array($node, $checkeds)];
                    $nodes[$pnode] = ['node' => $pnode, 'title' => ucfirst($pnode), 'pnode' => '', 'checked' => in_array($pnode, $checkeds)];
                }
            }
        }

        return Data::arr2tree(array_reverse($nodes), 'node', 'pnode', '_sub_');
    }

    /**
     * Initialize user permissions
     *
     * @param boolean $force Mandatory permissions
     * @return AdminService
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function apply($force = false)
    {

        if ($force) {
            $this->app->cache->rm('system_auth_node');
        }

        if (($uid = $this->app->session->get('user.id'))) {
            $user = Db::name('Users')->where(['id' => $uid])->find();
            if (($aids = $user['rid'])) {
                $where         = [['status', 'eq', '1'], ['id', 'in', explode(',', $aids)]];
                $subsql        = Db::name('SystemAuth')->field('id')->where($where)->buildSql();
                $user['nodes'] = array_unique(Db::name('SystemAuthNode')->whereRaw("auth in {$subsql}")->column('node'));
            } else {
                $user['nodes'] = [];
            }
            unset($user['password']);
            $this->app->session->set('user', $user);
        }

        return $this;
    }

}
