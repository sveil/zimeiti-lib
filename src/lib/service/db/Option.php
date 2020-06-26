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
use sveil\lib\model\Option as OptionModel;
use sveil\lib\Service;

/**
 * Class Option
 * Queue db data service
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\service
 */
class Option extends Service
{
    /**
     * 通过选项表ID查询选项键
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getKeyById($id)
    {
        return OptionModel::key($id)->find()->id;
    }

    /**
     * 通过选项表ID查询选项值
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getValueById($id)
    {
        return OptionModel::value($id)->find()->id;
    }

    /**
     * 通过资讯分类值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByAclass($str)
    {
        return OptionModel::aclass($str)->find()->id;
    }

    /**
     * 通过操作值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByAction($str)
    {
        return OptionModel::action($str)->find()->id;
    }

    /**
     * 通过支付宝值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByAlipay($str)
    {
        return OptionModel::alipay($str)->find()->id;
    }

    /**
     * 通过地区值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByArea($str)
    {
        return OptionModel::area($str)->find()->id;
    }

    /**
     * 通过验证值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByAuth($str)
    {
        return OptionModel::auth($str)->find()->id;
    }

    /**
     * 通过血型值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByBlood($str)
    {
        return OptionModel::blood($str)->find()->id;
    }

    /**
     * 通过人物分类值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByFclass($str)
    {
        return OptionModel::fclass($str)->find()->id;
    }

    /**
     * 通过语言值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByLang($str)
    {
        return OptionModel::lang($str)->find()->id;
    }

    /**
     * 通过短信分类值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByMclass($str)
    {
        return OptionModel::mclass($str)->find()->id;
    }

    /**
     * 通过支付方式值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByPayment($str)
    {
        return OptionModel::payment($str)->find()->id;
    }

    /**
     * 通过队列条件值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByQitem($str)
    {
        return OptionModel::qitem($str)->find()->id;
    }

    /**
     * 通过队列状态值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByQstatus($str)
    {
        return OptionModel::qstatus($str)->find()->id;
    }

    /**
     * 通过角色值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByRole($str)
    {
        return OptionModel::role($str)->find()->id;
    }

    /**
     * 通过星座值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByStarsign($str)
    {
        return OptionModel::starsign($str)->find()->id;
    }

    /**
     * 通过标签值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByTag($str)
    {
        return OptionModel::tag($str)->find()->id;
    }

    /**
     * 通过影片分类值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByVclass($str)
    {
        return OptionModel::vclass($str)->find()->id;
    }

    /**
     * 通过网站分类值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByWclass($str)
    {
        return OptionModel::wclass($str)->find()->id;
    }

    /**
     * 通过微信值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByWechat($str)
    {
        return OptionModel::wechat($str)->find()->id;
    }

    /**
     * 通过年份值查询选项表ID
     * @return binary
     * @throws Exception
     * @throws PDOException
     */
    public static function getIdByYear($str)
    {
        return OptionModel::year($str)->find()->id;
    }
}
