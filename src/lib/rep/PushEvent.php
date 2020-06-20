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

namespace sveil\lib\rep;

use sveil\lib\exception\InvalidArgumentException;
use sveil\lib\exception\InvalidDecryptException;
use sveil\lib\exception\InvalidResponseException;

/**
 * Abstract Class PushEvent
 * WeChat notification processing basic class
 * @author Richard <richard@sveil.com>
 * @package sveil\lib\rep
 */
abstract class PushEvent
{
    /**
     * WeOpen APPID
     * @var string
     */
    protected $appid;

    /**
     * WeOpen push XML content
     * @var string
     */
    protected $postxml;

    /**
     * WeOpen push encryption type
     * @var string
     */
    protected $encryptType;

    /**
     * WeOpen push request parameters
     * @var DataArray
     */
    protected $input;

    /**
     * Current WeOpen configuration object
     * @var DataArray
     */
    protected $config;

    /**
     * WeOpen push content object
     * @var DataArray
     */
    protected $receive;

    /**
     * Content of the message ready to reply
     * @var array
     */
    protected $message;

    /**
     * BasicPushEvent constructor
     * @param array $options
     * @throws InvalidResponseException
     */
    public function __construct(array $options)
    {
        if (empty($options['appid'])) {
            throw new InvalidArgumentException("Missing Config -- [appid]");
        }

        if (empty($options['appsecret'])) {
            throw new InvalidArgumentException("Missing Config -- [appsecret]");
        }

        if (empty($options['token'])) {
            throw new InvalidArgumentException("Missing Config -- [token]");
        }

        // Parameter initialization
        $this->config = new DataArray($options);
        $this->input  = new DataArray($_REQUEST);
        $this->appid  = $this->config->get('appid');

        // Push message processing
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->postxml     = file_get_contents("php://input");
            $this->encryptType = $this->input->get('encrypt_type');

            if ($this->isEncrypt()) {
                if (empty($options['encodingaeskey'])) {
                    throw new InvalidArgumentException("Missing Config -- [encodingaeskey]");
                }

                if (!class_exists('Prpcrypt', false)) {
                    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'Prpcrypt.php';
                }

                $prpcrypt = new \Prpcrypt($this->config->get('encodingaeskey'));
                $result   = Tools::xml2arr($this->postxml);
                $array    = $prpcrypt->decrypt($result['Encrypt']);

                if (intval($array[0]) > 0) {
                    throw new InvalidResponseException($array[1], $array[0]);
                }

                list($this->postxml, $this->appid) = [$array[1], $array[2]];
            }

            $this->receive = new DataArray(Tools::xml2arr($this->postxml));
        } elseif ($_SERVER['REQUEST_METHOD'] == "GET" && $this->checkSignature()) {
            @ob_clean();
            exit($this->input->get('echostr'));
        } else {
            throw new InvalidResponseException('Invalid interface request.', '0');
        }
    }

    /**
     * Does the message need to be encrypted
     * @return boolean
     */
    public function isEncrypt()
    {
        return $this->encryptType === 'aes';
    }

    /**
     * Reply message
     * @param array $data Message content
     * @param boolean $return Whether to return XML content
     * @param boolean $isEncrypt Whether to encrypt content
     * @return string
     * @throws InvalidDecryptException
     */
    public function reply(array $data = [], $return = false, $isEncrypt = false)
    {
        $xml = Tools::arr2xml(empty($data) ? $this->message : $data);

        if ($this->isEncrypt() || $isEncrypt) {
            if (!class_exists('Prpcrypt', false)) {
                require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'Prpcrypt.php';
            }

            $prpcrypt = new \Prpcrypt($this->config->get('encodingaeskey'));
            // If it is Usage, use component_appid for encryption
            $component_appid = $this->config->get('component_appid');
            $appid           = empty($component_appid) ? $this->appid : $component_appid;
            $array           = $prpcrypt->encrypt($xml, $appid);

            if ($array[0] > 0) {
                throw new InvalidDecryptException('Encrypt Error.', '0');
            }

            list($timestamp, $encrypt) = [time(), $array[1]];
            $nonce                     = rand(77, 999) * rand(605, 888) * rand(11, 99);
            $tmpArr                    = [$this->config->get('token'), $timestamp, $nonce, $encrypt];
            sort($tmpArr, SORT_STRING);
            $signature = sha1(implode($tmpArr));
            $format    = "<xml><Encrypt><![CDATA[%s]]></Encrypt><MsgSignature><![CDATA[%s]]></MsgSignature><TimeStamp>%s</TimeStamp><Nonce><![CDATA[%s]]></Nonce></xml>";
            $xml       = sprintf($format, $encrypt, $signature, $timestamp, $nonce);
        }

        if ($return) {
            return $xml;
        }

        @ob_clean();
        echo $xml;

    }

    /**
     * Verification comes from WeChat server
     * @param string $str
     * @return bool
     */
    private function checkSignature($str = '')
    {
        $nonce         = $this->input->get('nonce');
        $timestamp     = $this->input->get('timestamp');
        $msg_signature = $this->input->get('msg_signature');
        $signature     = empty($msg_signature) ? $this->input->get('signature') : $msg_signature;
        $tmpArr        = [$this->config->get('token'), $timestamp, $nonce, $str];
        sort($tmpArr, SORT_STRING);

        return sha1(implode($tmpArr)) === $signature;
    }

    /**
     * Get WeOpen push object
     * @param null|string $field Specified get field
     * @return array
     */
    public function getReceive($field = null)
    {
        return $this->receive->get($field);
    }

    /**
     * Get the current WeChat OPENID
     * @return string
     */
    public function getOpenid()
    {
        return $this->receive->get('FromUserName');
    }

    /**
     * Get the current push message type
     * @return string
     */
    public function getMsgType()
    {
        return $this->receive->get('MsgType');
    }

    /**
     * Get the current push message ID
     * @return string
     */
    public function getMsgId()
    {
        return $this->receive->get('MsgId');
    }

    /**
     * Get the current push time
     * @return integer
     */
    public function getMsgTime()
    {
        return $this->receive->get('CreateTime');
    }

    /**
     * Get the current push WeOpen
     * @return string
     */
    public function getToOpenid()
    {
        return $this->receive->get('ToUserName');
    }
}
