<?php
// +----------------------------------------------------------------------
// | Library for sveil/zimeiti-cms
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 http://sveil.com All rights reserved.
// +----------------------------------------------------------------------
// | License ( http://www.gnu.org/licenses )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/sveil/zimeiti-cms
// | github：https://github.com/sveil/zimeiti-cms
// +----------------------------------------------------------------------

namespace sveil\rep\wechat\wemini;

use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * WeChat Mini Program Account Management
 *
 * Class Account
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\wemini
 */
class Account extends WeChat
{

    /**
     * 2.1 Basic account information
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAccountBasicinfo()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/account/getaccountbasicinfo?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 2.2 Setting and changing the name of the applet
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setNickname(array $data)
    {

        $url = 'https://api.weixin.qq.com/wxa/setnickname?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * 2.3 Querying the status of the review of the applet
     *
     * @param integer $audit_id Audit order id
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryChangeNicknameAuditStatus($audit_id)
    {

        $url = "https://api.weixin.qq.com/wxa/api_wxa_querynickname?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['audit_id' => $audit_id]);
    }

    /**
     *
     * 2.4 WeChat authentication name detection
     *
     * @param string $nickname WeChat authentication name
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function checkWxVerifyNickname($nickname)
    {

        $url = "https://api.weixin.qq.com/wxa/api_wxa_querynickname?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['nick_name' => $nickname]);
    }

    /**
     * 2.5 Modify avatar
     *
     * @param string $headImgMediaId Avatar material media_id
     * @param integer $x1 The x coordinate of the upper left corner of the crop box（Ranges：[0, 1]）
     * @param integer $y1 The y coordinate of the upper left corner of the crop box（Ranges：[0, 1]）
     * @param integer $x2 The x coordinate of the lower right corner of the crop box（Ranges：[0, 1]）
     * @param integer $y2 The y coordinate of the lower right corner of the crop box（Ranges：[0, 1]）
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function modifyHeadImage($headImgMediaId, $x1 = 0, $y1 = 0, $x2 = 1, $y2 = 1)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/account/modifyheadimage?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['head_img_media_id' => $headImgMediaId]);
    }

    /**
     * 2.6 Introduction to modification functions
     *
     * @param string $signature Function introduction (introduction)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException]
     */
    public function modifySignature($signature)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/account/modifysignature?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['signature' => $signature]);
    }

    /**
     * 2.7.3 Jump to the third-party platform, and the third-party platform calls the quick registration API to complete the
     * administrator change
     *
     * @param string $taskid Change the task sequence number of the administrator(The public platform finally clicks submit to jump
     * back to the third-party platform to carry)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function componentreBindAdmin($taskid)
    {

        $url = 'https://api.weixin.qq.com/cgi- bin/account/componentrebindadmin?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['taskid' => $taskid]);
    }

    /**
     * 3.1 Obtain all categories that the account can set
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAllCategories()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/getallcategories?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 3.2 Add category
     *
     * @param array $categories
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addCategory($categories)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/addcategory?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['categories' => $categories]);
    }

    /**
     * 3.3 Delete category
     *
     * @param string $first Category first ID
     * @param string $second Category second ID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function delCategroy($first, $second)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/deletecategory?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['first' => $first, 'second' => $second]);
    }

    /**
     * 3.4 Get all categories that the account has set
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCategory()
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/getcategory?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 3.5 Modify category
     *
     * @param string $first Category first ID
     * @param string $second Category second ID
     * @param array $certicates
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function modifyCategory($first, $second, $certicates)
    {

        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/modifycategory?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['first' => $first, 'second' => $second, 'categories' => $categories]);
    }

}
