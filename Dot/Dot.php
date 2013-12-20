<?php
/**
* Copyright 2013 Nathan Marshall
*
* @author Nathan Marshall (FDL) <nathan@fludotlove.com>
* @copyright (c) 2013, Nathan Marshall
*/

/**
 * Class for using dot-notation to access arrays.
 *
 * @author Nathan Marshall
 */
class Dot {

    /**
     * Remove a key from an array using dot-notation.
     *
     * @param array $array
     * @param string $key
     */
    public static function forget(array &$array, $key)
    {
        $keys = explode('.', $key);

        while(count($keys) > 1)
        {
            $key = array_shift($keys);

            if(!isset($array[$key]) || !is_array($array[$key])) {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }

    /**
     * Get a value from an array using dot-notation.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        $keys = explode('.', $key);

        foreach($keys as $segment) {
            if(!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Determine if an array has a key using dot-notation.
     *
     * @param array $array
     * @param string $key
     * @return boolean
     */
    public static function has(array $array, $key)
    {
        return null !== static::get($array, $key, null);
    }

    /**
     * Set an array key using dot-notation.
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     */
    public static function set(array &$array, $key, $value)
    {
        $keys = explode('.', $key);

        while(count($keys) > 1) {
            $key = array_shift($keys);

            if(!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

}