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

namespace sveil\rep\wechat;

use sveil\common\Tools;
use sveil\exception\InvalidResponseException;
use sveil\exception\LocalCacheException;
use sveil\rep\WeChat;

/**
 * Class Shake
 * Shake Around
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Shake extends WeChat
{
    /**
     * Apply for activation
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function register(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/account/register?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Check audit status
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function auditStatus()
    {
        $url = "https://api.weixin.qq.com/shakearound/account/auditstatus?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * Apply for device ID
     * @param string $quantity The number of equipment IDs applied for, and more than 500 new equipments are added at a time,
     * which requires a manual review process
     * @param string $apply_reason Reason for application, no more than 100 Chinese characters or 200 English letters
     * @param null|string $comment Remarks, no more than 15 Chinese characters or 30 English letters
     * @param null|string $poi_id The store ID associated with the device. After the store is associated,
     * there is an opportunity to give priority to the information within 1KM of the store.
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createApply($quantity, $apply_reason, $comment = null, $poi_id = null)
    {
        $data                                 = ['quantity' => $quantity, 'apply_reason' => $apply_reason];
        is_null($poi_id) || $data['poi_id']   = $poi_id;
        is_null($comment) || $data['comment'] = $comment;
        $url                                  = "https://api.weixin.qq.com/shakearound/device/applyid?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query device ID application review status
     * @param integer $applyId Apply ID, the Apply ID returned when applying for the device ID
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getApplyStatus($applyId)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/applyid?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['apply_id' => $applyId]);
    }

    /**
     * Edit device information
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateApply(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Configure the relationship between the device and the store
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function bindLocation(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/bindlocation?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query device list
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function search(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/search?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Page management
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function createPage(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/page/add?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Edit page information
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updatePage(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/page/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query page list
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function searchPage(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/page/search?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Delete page
     * @param integer page_id Specify the id of the page
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deletePage($page_id)
    {
        $url = "https://api.weixin.qq.com/shakearound/page/delete?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['page_id' => $page_id]);
    }

    /**
     * Upload image material
     * @param string $filename Picture name
     * @param string $type Icon：Shake the icon picture displayed on the page；License：Qualification documents that need to
     * be uploaded when applying to activate the shake peripheral function；If no type is passed, the default type=icon
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function upload($filename, $type = 'icon')
    {
        $url = "https://api.weixin.qq.com/shakearound/material/add?access_token=ACCESS_TOKEN&type={$type}";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['media' => Tools::createCurlFile($filename)]);
    }

    /**
     * Configure the device-page association
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function bindPage(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/bindpage?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query the relationship between the device and the page
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function queryPage(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/relation/search?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Data statistics interface with device as dimension
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function totalDevice(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/statistics/device?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Batch query device statistics data interface
     * @param integer $date Specify the query date timestamp, in seconds
     * @param integer $page_index Specify the serial number of the query result page；The returned
     * results are sorted in descending order by the number of people around them, every 50 records are a page
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function totalDeviceList($date, $page_index = 1)
    {
        $url = "https://api.weixin.qq.com/shakearound/statistics/devicelist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['date' => $date, 'page_index' => $page_index]);
    }

    /**
     * Data statistics interface with page
     * @param integer $page_id Device ID of the specified page
     * @param integer $begin_date Start date timestamp，The maximum time span is 30 days, in seconds
     * @param integer $end_date End date timestamp，The maximum time span is 30 days, in seconds
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function totalPage($page_id, $begin_date, $end_date)
    {
        $url = "https://api.weixin.qq.com/shakearound/statistics/page?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['page_id' => $page_id, 'begin_date' => $begin_date, 'end_date' => $end_date]);
    }

    /**
     * Edit group information
     * @param integer $group_id Group unique identification, globally unique
     * @param string $group_name Group name, no more than 100 Chinese characters or 200 English letters
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateGroup($group_id, $group_name)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/group/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['group_id' => $group_id, 'group_name' => $group_name]);
    }

    /**
     * Delete group
     * @param integer $group_id Group unique identification, globally unique
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteGroup($group_id)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/group/delete?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['group_id' => $group_id]);
    }

    /**
     * Query group list
     * @param integer $begin The starting index value of the grouping list
     * @param integer $count The number of groups to be queried cannot exceed 1000
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGroupList($begin = 0, $count = 10)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/group/getlist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['begin' => $begin, 'count' => $count]);
    }

    /**
     * Query group details
     * @param integer $group_id Group unique identification, globally unique
     * @param integer $begin The starting index value of the device in the group
     * @param integer $count The number of devices in the group to be queried cannot exceed 1000
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGroupDetail($group_id, $begin = 0, $count = 100)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/group/getdetail?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['group_id' => $group_id, 'begin' => $begin, 'count' => $count]);
    }

    /**
     * Add device to group
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addDeviceGroup(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/group/adddevice?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Remove device from group
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteDeviceGroup(array $data)
    {
        $url = "https://api.weixin.qq.com/shakearound/device/group/deletedevice?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }
}
