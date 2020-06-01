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

use sveil\lib\Service;
use sveil\exception\HttpResponseException;

/**
 * JsonRpc Server service
 *
 * Class JsonRpcServer
 * @author Richard <richard@sveil.com>
 * @package sveil\service
 */
class JsonRpcServer extends Service
{
    /**
     * Set listening object
     *
     * @param mixed $object
     */
    public function handle($object)
    {
        // Checks if a JSON-RCP request has been received
        if ($this->app->request->method() !== "POST" || $this->app->request->contentType() != 'application/json') {
            echo "<h2>" . get_class($object) . "</h2>";
            foreach (get_class_methods($object) as $method) {
                if ($method[0] !== '_') {
                    echo "<p>method {$method}()</p>";
                }

            }
        } else {
            // Reads the input data
            $request = json_decode(file_get_contents('php://input'), true);
            if (empty($request)) {
                $error    = ['code' => '-32700', 'message' => '语法解析错误', 'meaning' => '服务端接收到无效的JSON'];
                $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => null, 'error' => $error];
            } elseif (!isset($request['id']) || !isset($request['method']) || !isset($request['params'])) {
                $error    = ['code' => '-32600', 'message' => '无效的请求', 'meaning' => '发送的JSON不是一个有效的请求对象'];
                $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => null, 'error' => $error];
            } else {
                try {
                    // Executes the task on local object
                    if ($result = @call_user_func_array([$object, $request['method']], $request['params'])) {
                        $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => $result, 'error' => null];
                    } else {
                        $error    = ['code' => '-32601', 'message' => '找不到方法', 'meaning' => '该方法不存在或无效'];
                        $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => null, 'error' => $error];
                    }
                } catch (\Exception $e) {
                    $error    = ['code' => $e->getCode(), 'message' => $e->getMessage()];
                    $response = ['jsonrpc' => '2.0', 'id' => $request['id'], 'result' => null, 'error' => $error];
                }
            }

            // Output the response
            throw new HttpResponseException(json($response)->contentType('text/javascript'));
        }
    }
}
