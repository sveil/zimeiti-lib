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

            foreach ($datas as $data) {
                // 资讯分类
                if ($data['key'] === 'aclass_option_id') {
                    array_push($arr[$k]['aclass'], Option::getKeyById(hex2bin($data['value'])));
                }

                // 标签
                if ($data['key'] === 'tag_option_id') {
                    array_push($arr[$k]['tag'], Option::getKeyById(hex2bin($data['value'])));
                }

                // 子标题
                if ($data['key'] === 'sub') {
                    $arr[$k]['sub'] = $data['value'];
                }

                // 英文标题
                if ($data['key'] === 'en') {
                    $arr[$k]['en'] = $data['value'];
                }

                // 索引图片
                if ($data['key'] === 'index') {
                    $arr[$k]['index'] = $data['value'];
                }

                // 滚动图片
                if ($data['key'] === 'slide') {
                    array_push($arr[$k]['slide'], $data['value']);
                }

                // 文章摘要
                if ($data['key'] === 'blurb') {
                    $arr[$k]['blurb'] = $data['value'];
                }
            }

            foreach ($texts as $text) {
                // 资讯内容
                if ($text['key'] === 'content') {
                    $arr[$k]['content'] = $text['value'];
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
            $arr[$k]['aclass']      = [];
            $arr[$k]['tag']         = [];
            $arr[$k]['slide']       = [];
            $datas                  = ArticleDataModel::where('article_id', $v->id)->select();
            $texts                  = ArticleTextModel::where('article_id', $v->id)->select();

            foreach ($datas as $data) {
                // 资讯分类
                if ($data['key'] === 'aclass_option_id') {
                    array_push($arr[$k]['aclass'], Option::getKeyById(hex2bin($data['value'])));
                }

                // 标签
                if ($data['key'] === 'tag_option_id') {
                    array_push($arr[$k]['tag'], Option::getKeyById(hex2bin($data['value'])));
                }

                // 子标题
                if ($data['key'] === 'sub') {
                    $arr[$k]['sub'] = $data['value'];
                }

                // 英文标题
                if ($data['key'] === 'en') {
                    $arr[$k]['en'] = $data['value'];
                }

                // 索引图片
                if ($data['key'] === 'index') {
                    $arr[$k]['index'] = $data['value'];
                }

                // 滚动图片
                if ($data['key'] === 'slide') {
                    array_push($arr[$k]['slide'], $data['value']);
                }

                // 文章摘要
                if ($data['key'] === 'blurb') {
                    $arr[$k]['blurb'] = $data['value'];
                }
            }

            foreach ($texts as $text) {
                // 资讯内容
                if ($text['key'] === 'content') {
                    $arr[$k]['content'] = $text['value'];
                }
            }
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
        // 启动事务
        Db::transaction(function () {
            $result = ArticleModel::create([
                'user_id' => User::getIdByName($row['user']),
                'title'   => $row['title'],
                'letter'  => $row['letter'],
                'level'   => $row['level'],
            ], true, $replace);

            for ($i = 0; $i < count($row) - 4; $i++) {
                if (isset($row['aclass'])) {
                    foreach ($row['aclass'] as $k => $v) {
                        ArticleDataModel::create([
                            'article_id' => $result['id'],
                            'key'        => 'aclass_option_id',
                            'value'      => Option::getIdByAclass($v),
                        ], true, $replace);
                    }
                }

                if (isset($row['tag'])) {
                    foreach ($row['tag'] as $k => $v) {
                        ArticleDataModel::create([
                            'article_id' => $result['id'],
                            'key'        => 'tag_option_id',
                            'value'      => Option::getIdByAclass($v),
                        ], true, $replace);
                    }
                }

                if (isset($row['sub'])) {
                    ArticleDataModel::create([
                        'article_id' => $result['id'],
                        'key'        => 'sub',
                        'value'      => $row['sub'],
                    ], true, $replace);
                }

                if (isset($row['en'])) {
                    ArticleDataModel::create([
                        'article_id' => $result['id'],
                        'key'        => 'en',
                        'value'      => $row['en'],
                    ], true, $replace);
                }

                if (isset($row['index'])) {
                    ArticleDataModel::create([
                        'article_id' => $result['id'],
                        'key'        => 'index',
                        'value'      => $row['index'],
                    ], true, $replace);
                }

                if (isset($row['slide'])) {
                    foreach ($row['slide'] as $k => $v) {
                        ArticleDataModel::create([
                            'article_id' => $result['id'],
                            'key'        => 'slide',
                            'value'      => $v,
                        ], true, $replace);
                    }
                }

                if (isset($row['blurb'])) {
                    ArticleDataModel::create([
                        'article_id' => $result['id'],
                        'key'        => 'blurb',
                        'value'      => $row['blurb'],
                    ], true, $replace);
                }

                if (isset($row['content'])) {
                    ArticleTextModel::create([
                        'article_id' => $result['id'],
                        'key'        => 'content',
                        'value'      => $row['content'],
                    ], true, $replace);
                }
            }
        });
    }

    /**
     * addAll object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function addAll($rows)
    {
        $results = [];

        foreach ($rows as $k => $v) {
            $row            = [];
            $row['user']    = $v['user'];
            $row['title']   = $v['title'];
            $row['letter']  = $v['letter'];
            $row['level']   = $v['level'];
            $row['aclass']  = $v['aclass'];
            $row['tag']     = $v['tag'];
            $row['sub']     = $v['sub'];
            $row['en']      = $v['en'];
            $row['index']   = $v['index'];
            $row['slide']   = $v['slide'];
            $row['blurb']   = $v['blurb'];
            $row['content'] = $v['content'];
            $result         = self::add($row);
            array_push($results, $result);
        }

        return $results;
    }

    /**
     * delete object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function delete($id)
    {
        // 启动事务
        Db::transaction(function () {
            $datas = ArticleDataModel::where('article_id', $id)->select();

            foreach ($datas as $data) {
                UuidModel::where('id', $data['id'])->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);
            }

            $texts = ArticleTextModel::where('article_id', $id)->select();

            foreach ($texts as $text) {
                UuidModel::where('id', $text['id'])->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);
            }

            return UuidModel::where('id', $id)->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);
        });

        return 0;
    }

    /**
     * clear object
     * @return array
     * @throws Exception
     * @throws PDOException
     */
    public static function clear()
    {
        // 启动事务
        Db::transaction(function () {
            UuidModel::where('tb_name', 'article_data')->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);
            UuidModel::where('tb_name', 'article_text')->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);

            return UuidModel::where('tb_name', 'article')->where('is_disabled', '<>', 2)->update(['is_disabled' => 2]);
        });

        return 0;
    }
}
