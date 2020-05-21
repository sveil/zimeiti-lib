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

namespace sveil\rep\wechat\weopen;

use sveil\rep\PushEvent;

/**
 * WeOpen push management
 *
 * Class Receive
 * @author Richard <richard@sveil.com>
 * @package sveil\rep\wechat\weopen
 */
class Receive extends PushEvent
{

    /**
     * Forward multiple customer service messages
     *
     * @param string $account
     * @return $this
     */
    public function transferCustomerService($account = '')
    {

        $this->message = [
            'CreateTime'   => time(),
            'ToUserName'   => $this->getOpenid(),
            'FromUserName' => $this->getToOpenid(),
            'MsgType'      => 'transfer_customer_service',
        ];
        empty($account) || $this->message['TransInfo'] = ['KfAccount' => $account];

        return $this;
    }

    /**
     * Set text message
     *
     * @param string $content Text content
     * @return $this
     */
    public function text($content = '')
    {

        $this->message = [
            'MsgType'      => 'text',
            'CreateTime'   => time(),
            'Content'      => $content,
            'ToUserName'   => $this->getOpenid(),
            'FromUserName' => $this->getToOpenid(),
        ];

        return $this;
    }

    /**
     * Set reply text
     *
     * @param array $newsData
     * @return $this
     */
    public function news($newsData = [])
    {

        $this->message = [
            'CreateTime'   => time(),
            'MsgType'      => 'news',
            'Articles'     => $newsData,
            'ToUserName'   => $this->getOpenid(),
            'FromUserName' => $this->getToOpenid(),
            'ArticleCount' => count($newsData),
        ];

        return $this;
    }

    /**
     * Set Picture Message
     *
     * @param string $mediaId Media ID
     * @return $this
     */
    public function image($mediaId = '')
    {

        $this->message = [
            'MsgType'      => 'image',
            'CreateTime'   => time(),
            'ToUserName'   => $this->getOpenid(),
            'FromUserName' => $this->getToOpenid(),
            'Image'        => ['MediaId' => $mediaId],
        ];

        return $this;
    }

    /**
     * Set voice reply message
     *
     * @param string $mediaid Media ID
     * @return $this
     */
    public function voice($mediaid = '')
    {

        $this->message = [
            'CreateTime'   => time(),
            'MsgType'      => 'voice',
            'ToUserName'   => $this->getOpenid(),
            'FromUserName' => $this->getToOpenid(),
            'Voice'        => ['MediaId' => $mediaid],
        ];

        return $this;
    }

    /**
     * Set video reply message
     *
     * @param string $mediaid Media ID
     * @param string $title Title
     * @param string $description Description
     * @return $this
     */
    public function video($mediaid = '', $title = '', $description = '')
    {

        $this->message = [
            'CreateTime'   => time(),
            'MsgType'      => 'video',
            'ToUserName'   => $this->getOpenid(),
            'FromUserName' => $this->getToOpenid(),
            'Video'        => [
                'Title'       => $title,
                'MediaId'     => $mediaid,
                'Description' => $description,
            ],
        ];

        return $this;
    }

    /**
     * Set music reply message
     *
     * @param string $title Title
     * @param string $desc Description
     * @param string $musicurl Music URL
     * @param string $hgmusicurl High Music URL
     * @param string $thumbmediaid Media id of music picture thumbnail (optional)
     * @return $this
     */
    public function music($title, $desc, $musicurl, $hgmusicurl = '', $thumbmediaid = '')
    {

        $this->message = [
            'CreateTime'   => time(),
            'MsgType'      => 'music',
            'ToUserName'   => $this->getOpenid(),
            'FromUserName' => $this->getToOpenid(),
            'Music'        => [
                'Title'       => $title,
                'Description' => $desc,
                'MusicUrl'    => $musicurl,
                'HQMusicUrl'  => $hgmusicurl,
            ],
        ];

        if ($thumbmediaid) {
            $this->message['Music']['ThumbMediaId'] = $thumbmediaid;
        }

        return $this;
    }

}
