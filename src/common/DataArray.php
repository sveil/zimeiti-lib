<?php

// +----------------------------------------------------------------------
// | Library for Sveil
// +----------------------------------------------------------------------
// | Copyright (c) 2019-2020 KuangJia Inc.
// +----------------------------------------------------------------------
// | Website: https://sveil.com
// +----------------------------------------------------------------------
// | License ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee：https://gitee.com/boy12371/think-lib
// | github：https://github.com/boy12371/think-lib
// +----------------------------------------------------------------------

namespace sveil\common;

/**
 * you can access class as array and the same time as object
 *
 * Class DataArray
 * @author Richard <richard@sveil.com>
 * @package sveil\common
 */
class DataArray implements ArrayAccess
{

    /**
     * Current configuration value
     * @var array
     */
    private $config = [];

    /**
     * Config constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->config = $options;
    }

    /**
     * Set configuration item values
     *
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * Get configuration item parameters
     *
     * @param string|null $offset
     * @return array|string|null
     */
    public function get($offset = null)
    {
        return $this->offsetGet($offset);
    }

    /**
     * Merge data into objects
     *
     * @param array $data data to be merged
     * @param bool $append whether to append data
     * @return array
     */
    public function merge(array $data, $append = false)
    {

        if ($append) {
            return $this->config = array_merge($this->config, $data);
        }

        return array_merge($this->config, $data);
    }

    /**
     * Set configuration item values
     *
     * @param string $offset
     * @param string|array|null|integer $value
     */
    public function offsetSet($offset, $value)
    {

        if (is_null($offset)) {
            $this->config[] = $value;
        } else {
            $this->config[$offset] = $value;
        }

    }

    /**
     * Determine if the configuration key exists
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * Clean up configuration items
     *
     * @param string|null $offset
     */
    public function offsetUnset($offset = null)
    {

        if (is_null($offset)) {
            $this->config = [];
        } else {
            unset($this->config[$offset]);
        }

    }

    /**
     * Get configuration item parameters
     * @param string|null $offset
     * @return array|string|null
     */
    public function offsetGet($offset = null)
    {

        if (is_null($offset)) {
            return $this->config;
        }

        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }

}
