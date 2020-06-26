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

namespace sveil\lib\model;

use sveil\lib\Model;

class Option extends Model
{
    // 注册选项事件观察者
    protected $observerClass = 'sveil\lib\model\event\Option';

    /**
     * 一对一UUID
     * @return object
     */
    public function uuid()
    {
        return $this->belongsTo('Uuid', 'id');
    }

    // 通过选项表ID查询选项键
    public function scopeKey($query, $id)
    {
        $query->where('id', $id)->field('key');
    }

    // 通过选项表ID查询选项值
    public function scopeValue($query, $id)
    {
        $query->where('id', $id)->field('value');
    }

    // 通过资讯分类值查询选项表ID
    public function scopeAclass($query, $value)
    {
        $query->where('title', 'aclass')->where('value', $value)->field('id');
    }

    // 通过操作值查询选项表ID
    public function scopeAction($query, $value)
    {
        $query->where('title', 'action')->where('value', $value)->field('id');
    }

    // 通过支付宝值查询选项表ID
    public function scopeAlipay($query, $value)
    {
        $query->where('title', 'alipay')->where('value', $value)->field('id');
    }

    // 通过地区值查询选项表ID
    public function scopeArea($query, $value)
    {
        $query->where('title', 'area')->where('value', $value)->field('id');
    }

    // 通过验证值查询选项表ID
    public function scopeAuth($query, $value)
    {
        $query->where('title', 'auth')->where('value', $value)->field('id');
    }

    // 通过血型值查询选项表ID
    public function scopeBlood($query, $value)
    {
        $query->where('title', 'blood')->where('value', $value)->field('id');
    }

    // 通过血型值查询选项表ID
    public function scopeFclass($query, $value)
    {
        $query->where('title', 'fclass')->where('value', $value)->field('id');
    }

    // 通过语言值查询选项表ID
    public function scopeLang($query, $value)
    {
        $query->where('title', 'lang')->where('value', $value)->field('id');
    }

    // 通过短信分类值查询选项表ID
    public function scopeMclass($query, $value)
    {
        $query->where('title', 'mclass')->where('value', $value)->field('id');
    }

    // 通过支付方式值查询选项表ID
    public function scopePayment($query, $value)
    {
        $query->where('title', 'payment')->where('value', $value)->field('id');
    }

    // 通过队列条件值查询选项表ID
    public function scopeQitem($query, $value)
    {
        $query->where('title', 'qitem')->where('value', $value)->field('id');
    }

    // 通过队列状态值查询选项表ID
    public function scopeQstatus($query, $value)
    {
        $query->where('title', 'qstatus')->where('value', $value)->field('id');
    }

    // 通过角色值查询选项表ID
    public function scopeRole($query, $value)
    {
        $query->where('title', 'role')->where('value', $value)->field('id');
    }

    // 通过星座值查询选项表ID
    public function scopeStarsign($query, $value)
    {
        $query->where('title', 'starsign')->where('value', $value)->field('id');
    }

    // 通过标签值查询选项表ID
    public function scopeTag($query, $value)
    {
        $query->where('title', 'tag')->where('value', $value)->field('id');
    }

    // 通过影片分类值查询选项表ID
    public function scopeVclass($query, $value)
    {
        $query->where('title', 'vclass')->where('value', $value)->field('id');
    }

    // 通过网站分类值查询选项表ID
    public function scopeWclass($query, $value)
    {
        $query->where('title', 'wclass')->where('value', $value)->field('id');
    }

    // 通过微信值查询选项表ID
    public function scopeWechat($query, $value)
    {
        $query->where('title', 'wechat')->where('value', $value)->field('id');
    }

    // 通过年份值查询选项表ID
    public function scopeYear($query, $value)
    {
        $query->where('title', 'year')->where('value', $value)->field('id');
    }
}
