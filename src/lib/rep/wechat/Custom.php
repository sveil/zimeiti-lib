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

namespace sveil\lib\rep\wechat;

use sveil\lib\common\Tools;
use sveil\lib\exception\InvalidResponseException;
use sveil\lib\exception\LocalCacheException;
use sveil\lib\rep\WeChat;

/**
 * Customer service message processing
 *
 * Class Custom
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat
 */
class Custom extends WeChat
{

    /**
     * Add customer service account
     *
     * @param string $kf_account Customer Service Account
     * @param string $nickname Customer Service Nickname
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function addAccount($kf_account, $nickname)
    {

        $data = ['kf_account' => $kf_account, 'nickname' => $nickname];
        $url  = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Modify customer service account
     *
     * @param string $kf_account Customer Service Account
     * @param string $nickname Customer Service Nickname
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function updateAccount($kf_account, $nickname)
    {

        $data = ['kf_account' => $kf_account, 'nickname' => $nickname];
        $url  = "https://api.weixin.qq.com/customservice/kfaccount/update?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Delete customer service account
     *
     * @param string $kf_account Customer Service Account
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function deleteAccount($kf_account)
    {

        $data = ['kf_account' => $kf_account];
        $url  = "https://api.weixin.qq.com/customservice/kfaccount/del?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Invitation to bind customer service account
     *
     * @param string $kf_account Complete customer service account，The format is: Account prefix@WeOpen account
     * @param string $invite_wx Customer service WeChat receiving binding invitation
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function inviteWorker($kf_account, $invite_wx)
    {

        $url = 'https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token=ACCESS_TOKEN';
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->callPostApi($url, ['kf_account' => $kf_account, 'invite_wx' => $invite_wx]);
    }

    /**
     * Get all customer service accounts
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function getAccountList()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());
        return $this->httpGetForJson($url);
    }

    /**
     * Set avatar for customer service account
     *
     * @param string $kf_account Customer account
     * @param string $image Avatar file location
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function uploadHeadimg($kf_account, $image)
    {

        $url = "http://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token=ACCESS_TOKEN&kf_account={$kf_account}";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['media' => Tools::createCurlFile($image)]);
    }

    /**
     * Customer Service Interface - Send Message
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function send(array $data)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Customer service input status
     *
     * @param string $openid normal user（openid）
     * @param string $command Typing: Typing, CancelTyping: Cancel typing
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function typing($openid, $command = 'Typing')
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/typing?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['touser' => $openid, 'command' => $command]);
    }

    /**
     * Mass sending based on tags [Both subscription number and service number are available after authentication]
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function massSendAll(array $data)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Mass sending according to OpenID list [Subscription number is not available, service number is available after authentication]
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function massSend(array $data)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Delete bulk [Both subscription number and service number are available after authentication]
     *
     * @param integer $msg_id Outgoing message id
     * @param null|integer $article_idx The position of the article to be deleted in the graphic message, The first article is numbered 1,
     * If this field is not filled or 0 is filled, all articles will be deleted
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function massDelete($msg_id, $article_idx = null)
    {

        $data                                         = ['msg_id' => $msg_id];
        is_null($article_idx) || $data['article_idx'] = $article_idx;
        $url                                          = "https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Preview interface [Both subscription number and service number are available after authentication]
     *
     * @param array $data
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function massPreview(array $data)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, $data);
    }

    /**
     * Query the sending status of group messages [Both subscription number and service number are available after authentication]
     *
     * @param integer $msg_id Message id returned after bulk message
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function massGet($msg_id)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['msg_id' => $msg_id]);
    }

    /**
     * Get the mass sending speed
     *
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function massGetSeed()
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/speed/get?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, []);
    }

    /**
     * Set the mass sending speed
     *
     * @param integer $speed the level of bulk speed
     * @return array
     * @throws InvalidResponseException
     * @throws LocalCacheException
     */
    public function massSetSeed($speed)
    {

        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/speed/set?access_token=ACCESS_TOKEN";
        $this->registerApi($url, __FUNCTION__, func_get_args());

        return $this->httpPostForJson($url, ['speed' => $speed]);
    }

}
