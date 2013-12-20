<?php
/**
* Copyright 2013 Nathan Marshall
*
* @author Nathan Marshall (FDL) <nathan@fludotlove.com>
* @copyright (c) 2013, Nathan Marshall
*/

/**
 * Remove a key from an array using dot-notation.
 *
 * @param array $array
 * @param string $key
 */
function array_forget(array &$array, $key)
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
function array_get(array $array, $key, $default = null)
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
function array_has(array $array, $key)
{
    return null !== array_get($array, $key, null);
}

/**
 * Set an array key using dot-notation.
 *
 * @param array $array
 * @param string $key
 * @param mixed $value
 */
function array_set(array &$array, $key, $value)
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