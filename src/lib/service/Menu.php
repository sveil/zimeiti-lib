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

use sveil\lib\common\Data;
use sveil\lib\Service;
use sveil\lib\service\Node;
use sveil\Db;
use sveil\db\exception\DataNotFoundException;
use sveil\db\exception\ModelNotFoundException;
use sveil\exception\DbException;

/**
 * Class Menu
 * System menu management service
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class Menu extends Service
{
    /**
     * Get optional menu node
     * @return array
     * @throws \ReflectionException
     */
    public function getList()
    {
        static $nodes = [];

        if (count($nodes) > 0) {
            return $nodes;
        }

        foreach (Node::instance()->getMethods() as $node => $method) {
            if ($method['ismenu']) {
                $nodes[] = ['node' => $node, 'title' => $method['title']];
            }

        }

        return $nodes;
    }

    /**
     * Get system menu tree data
     * @return array
     * @throws \ReflectionException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     */
    public function getTree()
    {
        $result = Db::name('SystemMenu')->where(['status' => '1'])->order('sort desc,id asc')->select();

        return $this->buildData(Data::arr2tree($result), Node::instance()->getMethods());
    }

    /**
     * Background main menu permission filtering
     * @param array $menus Current menu list
     * @param array $nodes System authority node
     * @return array
     * @throws \ReflectionException
     */
    private function buildData($menus, $nodes)
    {
        foreach ($menus as $key => &$menu) {
            if (!empty($menu['sub'])) {
                $menu['sub'] = $this->buildData($menu['sub'], $nodes);
            }

            if (!empty($menu['sub'])) {
                $menu['url'] = '#';
            } elseif ($menu['url'] === '#') {
                unset($menus[$key]);
            } elseif (preg_match('|^https?://|i', $menu['url'])) {
                continue;
            } else {
                $node        = join('/', array_slice(explode('/', preg_replace('/[\W]/', '/', $menu['url'])), 0, 3));
                $menu['url'] = url($menu['url']) . (empty($menu['params']) ? '' : "?{$menu['params']}");
                if (!AdminService::instance()->check($node)) {
                    unset($menus[$key]);
                }

            }
        }

        return $menus;
    }
}
