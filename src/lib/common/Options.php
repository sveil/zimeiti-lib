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

namespace sveil\lib\common;

/**
 * Data access object
 *
 * Class Object
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class Options implements \ArrayAccess
{

    /**
     * Current data object
     * @var array
     */
    private $data = [];

    /**
     * Object constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Determine whether the data has been set
     *
     * @param string $name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Set Data Object
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Get data content
     *
     * @param string|null $name
     * @return mixed|null
     */
    public function get($name = null)
    {

        if (is_null($name)) {
            return $this->data;
        }

        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Delete data content
     *
     * @param string $name
     */
    public function del($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Clean up all configurations
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * Add merged data
     *
     * @param array $data
     * @param boolean $append
     * @return array
     */
    public function merge($data, $append = false)
    {
        $result = array_merge($this->data, $data);
        return $append ? ($this->data = $result) : $result;
    }

    /**
     * Determine whether the data has been set
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Get data content
     *
     * @param string|null $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set Data Object
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Delete data content
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->del($offset);
    }

}
