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
defined('SKELETON') or exit('No direct access to components');

/**
 * Redirection class, can be used to redirect users to a given controller and
 * method, or directly to a URL
 *
 * @author     Nathan Marshall <fludotlove@gmail.com>
 * @namespace  Skeleton
 */
class Redirect {

    /**
     * Redirection instance, since only a single redirect can happen per page
     *
     * @access  protected
     * @var     object     $_redirect
     */
    protected static $_redirect;

    /**
     * Direct the user back to the previous page (if possible)
     *
     * @access  public
     * @param   integer  $seconds  Seconds before the redirect should kick in
     * @return  object
     */
    public static function back($seconds = 0) {
        if(!is_null(Request::referer())) {
            return static::to(Request::referer(), $seconds);
        }
    }

    /**
     * Create a redirect to the given route
     *
     * @access  public
     * @param   string   $url         Route to redirect to
     * @param   array    $parameters  Parameters to pass to the route
     * @param   integer  $seconds     Seconds before the redirect should kick in
     * @return  object
     */
    public static function route($route, $parameters = array(), $seconds = 0) {
        return static::to(URL::route($route, $parameters), $seconds);
    }

    /**
     * Create a redirect to the given path
     *
     * @access  public
     * @param   string   $url      Path to redirect to
     * @param   integer  $seconds  Seconds before the redirect should kick in
     * @return  object
     */
    public static function to($url, $seconds = 0) {
        $url = URL::resolve($url);

        return static::_make($url, $seconds);
    }

    /**
     * Adds a flash message to the session whilst redirecting the request
     *
     * @access  public
     * @param   string  $value  Value to pass with the redirection
     * @return  object
     */
    public function with($value) {
        if(Configuration::get('session.enable', false) === false) {
            throw new \Exception('Session must be configured to use flash data');
        }

        Session::add_flash($value);

        return static::$_redirect;
    }

    /**
     * Creates a new redirect instance, since there can only be one redirect per
     * page it's safe to store this statically
     *
     * @access  protected
     * @param   string     $url      Path to redirect to
     * @param   integer    $seconds  Seconds before the redirect should kick in
     * @return  object
     */
    protected static function _make($url, $seconds) {
        header(((is_integer($seconds) and $seconds != 0) ? 'Refresh: '.$seconds.'; URL='.$url : 'Location: '.$url));

        return static::$_redirect = new static;
    }
}

/* End of file: skeleton/core/redirect.php */