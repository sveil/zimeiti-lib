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

namespace sveil\lib\rep\wechat\wemini;

use sveil\lib\common\Tools;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WeChat;

/**
 * Class Code
 * Code management
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep\wechat\wemini
 */
class Code extends WeChat
{
    /**
     * 1. Upload the applet code for the authorized applet account
     * @param string $templateId Code template ID in the code base
     * @param string $extJson Vendor custom configuration
     * @param string $userVersion Code version number developers can customize
     * @param string $userDesc Code description developers can customize
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function commit($templateId, $extJson, $userVersion, $userDesc)
    {
        $url = 'https://api.weixin.qq.com/wxa/commit?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $data = [
            'template_id'  => $templateId,
            'ext_json'     => $extJson,
            'user_version' => $userVersion,
            'user_desc'    => $userDesc,
        ];

        return $this->httpPostForJson($url, $data, true);
    }

    /**
     * 2. Get the experience QR code of the experience applet
     * @param null|string $path Specify the trial version QR code to jump to a specific page
     * @param null|string $outType Specify the output type
     * @return array|bool|string
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getQrcode($path = null, $outType = null)
    {
        $pathStr = is_null($path) ? '' : ("&path=" . urlencode($path));
        $url     = "https://api.weixin.qq.com/wxa/get_qrcode?access_token=ACCESS_TOKEN{$pathStr}";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        $result = Tools::get($url);

        if (json_decode($result)) {
            return Tools::json2arr($result);
        }

        return is_null($outType) ? $result : $outType($result);
    }

    /**
     * 3. Obtain optional categories for authorized applet accounts
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getCategory()
    {
        $url = 'https://api.weixin.qq.com/wxa/get_category?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 4. Get the page configuration of the third-party submission code of the applet
     * (only for third-party developers to call on behalf of the applet)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getPage()
    {
        $url = 'https://api.weixin.qq.com/wxa/get_page?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 5. Submit the code package submitted by the third party for review
     * (only for third-party developers to call on behalf of the applet)
     * @param array $itemList Submit a list of review items
     * @param string $feedbackInfo The feedback content does not exceed 200 words
     * @param string $feedbackStuff Picture media_id list
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function submitAudit(array $itemList, $feedbackInfo = '', $feedbackStuff = '')
    {
        $url = 'https://api.weixin.qq.com/wxa/submit_audit?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['item_list' => $itemList, 'feedback_info' => '', 'feedback_stuff' => $feedbackStuff], true);
    }

    /**
     * 6. Obtain audit results
     * @return array
     */
    public function getNotify()
    {
        return Tools::xml2arr(file_get_contents('php://input'));
    }

    /**
     * 7. Query the audit status of a specified version (only for third-party applet calls)
     * @param string $auditid Review id obtained when submitting review
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAuditstatus($auditid)
    {
        $url = 'https://api.weixin.qq.com/wxa/get_auditstatus?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['auditid' => $auditid], true);
    }

    /**
     * 8. Check the latest audit status submitted (only for third-party applet calls)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getLatestAuditatus()
    {
        $url = 'https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 9. Publish the approved applet (only for third-party applet calls)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function publishRelease()
    {
        $url = 'https://api.weixin.qq.com/wxa/release?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, [], true);
    }

    /**
     * 10. Modify the visible state of the online code of the applet (only for the third-party to call the applet)
     * @param string $action Set the accessibility state, which is accessible by default after publishing,
     * close is invisible, open is visible
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function changeVisitStatus($action)
    {
        $url = 'https://api.weixin.qq.com/wxa/change_visitstatus?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['action' => $action], true);
    }

    /**
     * 11. The applet version rollback (only for third-party applet calls)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function revertCodeRelease()
    {
        $url = 'https://api.weixin.qq.com/wxa/revertcoderelease?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 12. Query the currently set minimum base library version and the proportion of users in each version
     * (only for third-party applet calls)
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getWeappSupportVersion()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/getweappsupportversion?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, []);
    }

    /**
     * 13. Set the minimum basic library version (only for third-party applet calls)
     * @param string $version version
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function setWeappSupportVersion($version)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/setweappsupportversion?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['version' => $version]);
    }

    /**
     * 14. Set the applet "scan common link QR code to open applet"
     * (1) Add or modify QR code rules
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addQrcodeJump(array $data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/qrcodejumpadd?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * 14. Set the applet "scan common link QR code to open applet"
     * (2) Obtain the set QR code rules
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getQrcodeJump(array $data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/qrcodejumpget?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * 14. Set the applet "scan common link QR code to open applet"
     * (3) Obtain the name and content of the verification file
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function downloadQrcodeJump()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/qrcodejumpdownload?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, []);
    }

    /**
     * 14. Set the applet "scan common link QR code to open applet"
     * (4) Delete the set QR code rules
     * @param string $prefix QR code rules
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteQrcodeJump($prefix)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/qrcodejumpdelete?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['prefix' => $prefix]);
    }

    /**
     * 14. Set the applet "scan common link QR code to open applet"
     * (5) Publish the set QR code rules
     * @param string $prefix QR code rules
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function publishQrcodeJump($prefix)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/wxopen/qrcodejumppublish?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['prefix' => $prefix]);
    }

    /**
     * 16. Withdrawal of small program review
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function undoCodeAudit()
    {
        $url = 'https://api.weixin.qq.com/wxa/undocodeaudit?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 17. Applets are released in stages
     * (1) Release the interface in stages
     * @param integer $gray_percentage The percentage of grayscale, an integer from 1 to 100
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function grayRelease($gray_percentage)
    {
        $url = 'https://api.weixin.qq.com/wxa/grayrelease?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['gray_percentage' => $gray_percentage]);
    }

    /**
     * 17. Applets are released in stages
     * (2) Cancel phased release
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function revertGrayRelease()
    {
        $url = 'https://api.weixin.qq.com/wxa/revertgrayrelease?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }

    /**
     * 17. Applets are released in stages
     * (3) Query the details of the current staged release
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getGrayreLeasePlan()
    {
        $url = 'https://api.weixin.qq.com/wxa/getgrayreleaseplan?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpGetForJson($url);
    }
}
