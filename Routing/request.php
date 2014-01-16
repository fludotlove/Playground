<?php namespace Skeleton;
/**
 * Skeleton PHP Framework
 *
 * @author   Nathan Marshall <fludotlove@gmail.com>
 * @license  MIT License
 * @package  Skeleton
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the 'Software'), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software is provided 'as is', without warranty of any kind, express or
 * implied, including but not limiting to the warranties of merchantability, fitness
 * for a particular purpose and noninfringement. In no event shall the author or
 * copyright holder be liable for any claim, damages or other liability, whether
 * in action of contract, tort or otherwise, arising from, out of or in
 * connection with the Software or the use or other dealings in the Software.
 */

/*
 * Ensure that Skeleton has been invoked from the application, if this
 * constant is not set then the file is being called directly and should not
 * continue executing
 */
defined('SKELETON') or exit('No direct access to skeleton components');

/**
 * Contains vital information on the request to the Skeleton framework,
 * information such as the request method (GET, POST etc)
 *
 * @author     Nathan Marshall <fludotlove@gmail.com>
 * @namespace  Skeleton
 */
class Request {

    /**
     * Local cache of server variables
     *
     * @access  protected
     * @var     array      $_server
     */
    protected static $_server = array();

    /**
     * Check if the HTTP request was from ajax
     *
     * @access  public
     * @return  boolean
     */
    public static function ajax() {
        $ajax = static::server('http_x_requested_with', 'web');

        if($ajax != 'XMLHttpRequest') {
            return false;
        }

        return true;
    }

    /**
     * Detect the current browser being used
     *
     * @access  public
     * @return  string
     */
    public static function browser() {
        $user_agent = strtolower(static::server('http_user_agent', 'Unknown'));
        $return = 'Unknown';

        if(!preg_match('/opera|webtv/', $user_agent) and preg_match('/msie\s(\d+)/', $user_agent, $version)) {
            $return = 'Internet Explorer '.$version[1].' '.($version[1] >= 7 ? '(Trident)' : '(MSHTML)');
        } else if(preg_match('/firefox\/(\d+)/', $user_agent, $version)) {
            $return = 'Firefox '.$version[1].' (Gecko)';
        } else if(preg_match('/opera(\s|\/)(\d+)/', $user_agent, $version)) {
            $return = 'Opera '.$version[2];
        } else if(strstr($user_agent, 'konqueror')) {
            $return = 'Konqueror (Trident)';
        } else if(strstr($user_agent, 'chrome')) {
            $return = 'Google Chrome (Webkit)';
        } else if(strstr($user_agent, 'iron')) {
            $return = 'SRWare Iron (Webkit)';
        } else if(strstr($user_agent, 'applewebkit/')) {
            $return = (preg_match('/version\/(\d+)/', $user_agent, $version)) ? 'Safari '.$version[1].' (Webkit)' : 'Safari (Webkit)';
        }

        return $return;
    }

    /**
     * Get an item from $_GET or $_POST or return the default
     *
     * @access  public
     * @param   string  $from     Variable to look in
     * @param   string  $key      Key to return
     * @param   mixed   $default  Default value to return (if none exists)
     */
    public static function get($from, $key, $default = null) {
        $from = String::upper($from);

        if($from === 'GET') {
            $from = $_GET;
        } else {
            $from = $_POST;
        }

        return Data::get($from, $key, $default);
    }

    /**
     * Get the IP address of the user
     *
     * @access  public
     * @param   mixed   $default  Default to return if nothing is set
     * @return  mixed
     */
    public static function ip($ip = '0.0.0.0') {
        if(static::server('http_client_ip')) {
            return static::server('http_client_ip');
        } else if(static::server('http_x_forwarded_for')) {
            return static::server('http_x_forwarded_for');
        } else if(static::server('http_x_forwarded')) {
            return static::server('http_x_forwarded');
        } else if(static::server('http_forwarded_for')) {
            return static::server('http_forwarded_for');
        } else if(static::server('http_forwarded')) {
            return static::server('http_forwarded_for');
        } else if(static::server('remote_addr')) {
            return static::server('remote_addr');
        }

        return $ip;
    }

    /**
     * Compares the given method with the current request
     *
     * @access  public
     * @param   string   $method  Method to compare
     * @return  boolean
     */
    public static function is($method) {
        $method = String::upper($method);

        return $method === static::method();
    }

    /**
     * Gets the current request method (or spoofed method from hidden inputs)
     *
     * @access  public
     * @return  string
     */
    public static function method() {
        $method = static::server('REQUEST_METHOD', 'GET');

        return ($method !== 'GET' ? static::get('POST', '_method', 'POST') : 'GET');
    }

    /**
     * Check if the referer exists in the $_SERVER array (original spelling)
     *
     * @access  public
     * @return  mixed
     */
    public static function referer() {
        return static::server('http_referer', null);
    }

    /**
     * Check if the referer exists in the $_SERVER array
     *
     * @access  public
     * @return  mixed
     */
    public static function referrer() {
        return static::server('http_referer', null);
    }

    /**
     * Get an item from the $_SERVER array
     *
     * @access  public
     * @param   string  $key      Key to get from the $_SERVER array
     * @param   string  $default  Default value to return if not set
     * @return  string
     */
    public static function server($key, $default = null) {
        if(array_key_exists($key, static::$_server)) {
            return static::$_server[$key];
        }

        return static::$_server[$key] = Data::get($_SERVER, strtoupper($key), $default);
    }

    /**
     * Timestamp of the time when the request was started
     *
     * @access  public
     * @return  integer
     */
    public static function time() {
        return (int) SKELETON;
    }

}

/* End of file: skeleton/core/request.php */