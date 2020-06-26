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

namespace sveil\lib\service\db;

use sveil\Exception;
use sveil\exception\PDOException;
use sveil\lib\model\Article as ArticleModel;
use sveil\lib\model\ArticleData as ArticleDataModel;
use sveil\lib\model\ArticleText as ArticleTextModel;
use sveil\lib\Service;
use sveil\lib\service\db\Option;

/**
 * Class Article
 * Article db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Article extends Service
{
    /**
     * all object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function all()
    {
        $arr = ArticleModel::withJoin([
            'uuid' => ['create_at', 'is_disabled'],
            'user' => ['id', 'name'],
        ])->select();

        foreach ($arr as $k => $v) {
            $arr[$k]['create_at']   = $v->uuid->create_at;
            $arr[$k]['is_disabled'] = $v->uuid->is_disabled;
            $arr[$k]['aclass']      = [];
            $arr[$k]['tag']         = [];
            $arr[$k]['slide']       = [];
            $datas                  = ArticleDataModel::where('article_id', $v->id)->select();
            $texts                  = ArticleTextModel::where('article_id', $v->id)->select();

            foreach ($datas as $kk => $vv) {
                // 资讯分类
                if ($vv['key'] === 'aclass_option_id') {
                    array_push($arr[$k]['aclass'], Option::getKeyById(hex2bin($vv['value'])));
                }

                // 标签
                if ($vv['key'] === 'tag_option_id') {
                    array_push($arr[$k]['tag'], Option::getKeyById(hex2bin($vv['value'])));
                }

                // 子标题
                if ($vv['key'] === 'sub') {
                    $arr[$k]['sub'] = $vv['value'];
                }

                // 英文标题
                if ($vv['key'] === 'en') {
                    $arr[$k]['en'] = $vv['value'];
                }

                // 索引图片
                if ($vv['key'] === 'index') {
                    $arr[$k]['index'] = $vv['value'];
                }

                // 滚动图片
                if ($vv['key'] === 'slide') {
                    array_push($arr[$k]['slide'], $vv['value']);
                }

                // 文章摘要
                if ($vv['key'] === 'blurb') {
                    $arr[$k]['blurb'] = $vv['value'];
                }
            }

            foreach ($texts as $kk => $vv) {
                // 资讯内容
                if ($vv['key'] === 'content') {
                    $arr[$k]['content'] = $vv['value'];
                }
            }
        }

        return $arr;
    }

    /**
     * select object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function select()
    {
        $arr = ArticleModel::withJoin([
            'uuid' => ['create_at', 'is_disabled'],
            'user' => ['id', 'name'],
        ])->where('uuid.is_disabled', 0)->select();

        foreach ($arr as $k => $v) {
            $arr[$k]['create_at']   = $v->uuid->create_at;
            $arr[$k]['is_disabled'] = $v->uuid->is_disabled;
        }

        return $arr;
    }

    /**
     * count object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function count()
    {
        return ArticleModel::withJoin([
            'uuid' => ['is_disabled'],
        ])->where('uuid.is_disabled', 0)->count();
    }

    /**
     * add object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function add($row, $replace = false)
    {
        return ArticleModel::create([
            'user_id' => User::getIdByName($row['user']),
            'title'   => $row['title'],
            'letter'  => $row['letter'],
            'level'   => $row['level'],
        ], true, $replace);
    }

    /**
     * addAll object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function addAll($rows)
    {
        $article = new ArticleModel;
        $arr     = [];

        foreach ($rows as $k => $v) {
            $arr[$k]['user_id'] = User::getIdByName($v['user']);
            $arr[$k]['title']   = $v['title'];
            $arr[$k]['letter']  = $v['letter'];
            $arr[$k]['level']   = $v['level'];
        }

        return $article->saveAll($arr);
    }

    /**
     * delete object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function delete($id)
    {
        return UuidModel::where('id', $id)->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);
    }

    /**
     * clear object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function clear()
    {
        return UuidModel::where('tb_name', 'article')->update(['is_disabled' => 2]);
    }
}
