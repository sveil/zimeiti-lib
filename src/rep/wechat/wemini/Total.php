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

namespace sveil\rep\wechat\wemini;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * WeChat Applet Data Interface
 *
 * Class Total
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Total extends WeChat
{

    /**
     * Data analysis interface
     *
     * @param string $begin_date start date
     * @param string $end_date End date, limited to query 1 day of data, the maximum value allowed by end_date is yesterday
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidDailySummarytrend($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappiddailysummarytrend?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Access analysis
     *
     * @param string $begin_date start date
     * @param string $end_date End date, limited to query 1 day of data, the maximum value allowed by end_date is yesterday
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidDailyVisittrend($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Weekly trend
     *
     * @param string $begin_date start date，Is a Monday date
     * @param string $end_date End date is Sunday date, limited query for one week of data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidWeeklyVisittrend($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappidweeklyvisittrend?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Monthly trend
     *
     * @param string $begin_date start date，for the first day of the natural month
     * @param string $end_date End date, the last day of the natural month, limited query for one month of data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidMonthlyVisittrend($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyvisittrend?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Access distribution
     *
     * @param string $begin_date start date
     * @param string $end_date End date, limited to query 1 day of data, the maximum value allowed by end_date is yesterday
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidVisitdistribution($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappidvisitdistribution?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Daily retention
     *
     * @param string $begin_date start date
     * @param string $end_date End date, limited to query 1 day of data, the maximum value allowed by end_date is yesterday
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidDailyRetaininfo($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappiddailyretaininfo?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Weekly retention
     *
     * @param string $begin_date start date，Is a Monday date
     * @param string $end_date End date is Sunday date, limited query for one week of data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidWeeklyRetaininfo($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappidweeklyretaininfo?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Monthly retention
     *
     * @param string $begin_date start date，for the first day of the natural month
     * @param string $end_date End date, the last day of the natural month, limited query for one month of data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidMonthlyRetaininfo($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyretaininfo?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * Visit page
     *
     * @param string $begin_date start date
     * @param string $end_date End date, limited to query 1 day of data, the maximum value allowed by end_date is yesterday
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidVisitPage($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

    /**
     * User avatar
     * @param string $begin_date start date
     * @param string $end_date End date, the number of days between the start date and the end date is limited to 0/6/29,
     * which means querying the latest 1/7/30 days of data, and the maximum value allowed by end_date is yesterday
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeanalysisAppidUserportrait($begin_date, $end_date)
    {

        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappiduserportrait?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['begin_date' => $begin_date, 'end_date' => $end_date], true);
    }

}
