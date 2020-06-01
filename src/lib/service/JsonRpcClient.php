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

namespace sveil\lib\service;

use sveil\lib\common\Data;
use sveil\lib\Service;
use sveil\Exception;

/**
 * Class JsonRpcClient
 * JsonRpc Client service
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class JsonRpcClient extends Service
{
    /**
     * Server address
     * @var string
     */
    private $proxy;
    /**
     * Request ID
     * @var integer
     */
    private $requestid;

    /**
     * Create connection object
     *
     * @param string $proxy
     * @return $this
     */
    public function create($proxy)
    {
        $this->requestid = Data::randomCode(16, 3);
        $this->proxy     = $proxy;

        return $this;
    }

    /**
     * Execution JsonRpc request
     *
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $params)
    {
        // Performs the HTTP POST
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => json_encode([
                    'jsonrpc' => '2.0', 'method' => $method, 'params' => $params, 'id' => $this->requestid,
                ], JSON_UNESCAPED_UNICODE),
            ],
        ];

        if ($fp = fopen($this->proxy, 'r', false, stream_context_create($options))) {
            $response = '';
            while ($row = fgets($fp)) {
                $response .= trim($row) . "\n";
            }

            fclose($fp);
            $response = json_decode($response, true);
        } else {
            throw new Exception("无法连接到 {$this->proxy}");
        }

        // Final checks and return
        if ($response['id'] != $this->requestid) {
            throw new Exception("错误的响应标记 (请求标记: {$this->requestid}, 响应标记: {$response['id']}）");
        }

        if (is_null($response['error'])) {
            return $response['result'];
        } else {
            throw new Exception("请求错误：[{$response['error']['code']}] {$response['error']['message']}");
        }
    }
}
