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

namespace sveil\exception;

/**
 * Exception thrown if invalid response
 *
 * Class InvalidResponseException
 * @author Richard <richard@sveil.com>
 * @package sveil\exception
 */
class InvalidResponseException extends \Exception
{

    /**
     * @var array
     */
    public $raw = [];

    /**
     * InvalidResponseException constructor.
     * @param string $message
     * @param integer $code
     * @param array $raw
     */
    public function __construct($message, $code = 0, $raw = [])
    {
        parent::__construct($message, intval($code));
        $this->raw = $raw;
    }

}
