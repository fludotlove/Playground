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

use Closure;

/**
 * Handles routing throughout the Skeleton framework (distribution of URL's
 * to relevant controllers or closure methods)
 *
 * @author     Nathan Marshall <fludotlove@gmail.com>
 * @namespace  Skeleton
 */
class Router {

    /**
     * Current request URL
     *
     * @access  protected
     * @var     string     $_current
     */
    protected static $_current;

    /**
     * All of the fallback routes which have been configured
     *
     * @access  protected
     * @var     array      $_fallback
     */
    protected static $_fallback = array(
        'DELETE' => array(),
        'GET'    => array(),
        'HEAD'   => array(),
        'POST'   => array(),
        'PUT'    => array()
    );

    /**
     * Holds before and after filters for patterned routes
     *
     * @access  protected
     * @var     array      $_filters
     */
    protected static $_filters = array();

    /**
     * Current shared attributes from the routes
     *
     * @access  protected
     * @var     array      $_group
     */
    protected static $_group;

    /**
     * HTTP request methods the router can handle
     *
     * @access  protected
     * @var     array      $_methods
     */
    protected static $_methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD');

    /**
     * Route names which have been matched
     *
     * @access  protected
     * @var     array      $_names
     */
    protected static $_names = array();

    /**
     * Optional wildcard patterns supported by the router
     *
     * @access  protected
     * @var     array      $_optional
     */
    protected static $_optional = array(
        '/(:num?)' => '(?:/([0-9]+)',
        '/(:any?)' => '(?:/([a-zA-Z0-9\.\-_%=]+)',
        '/(:all?)' => '(?:/(.*)'
    );

    /**
     * Wildcard patterns supported by the router
     *
     * @access  protected
     * @var     array      $_patterns
     */
    protected static $_patterns = array(
        '(:num)' => '([0-9]+)',
        '(:any)' => '([a-zA-Z0-9\.\-_%=]+)',
        '(:all)' => '(.*)'
    );

    /**
     * All of the routes which have been registered with the router
     *
     * @access  protected
     * @var     array      $_routes
     */
    protected static $_routes = array(
        'DELETE' => array(),
        'GET'    => array(),
        'HEAD'   => array(),
        'POST'   => array(),
        'PUT'    => array()
    );

    /**
     * Maximum number of segments allowed as method arguments
     *
     * @access  protected
     * @var     integer    $_segments
     */
    protected static $_segments = 5;

    /**
     * Actions which have been reverse routed
     *
     * @access  protected
     * @var     array      $_uses
     */
    protected static $_uses = array();

    /**
     * Registers a controller with the router
     *
     * @access  public
     * @param   mixed   $controllers  Controller(s) to register with the router
     * @param   array   $defaults     Default attributes to pass to the route if they aren't set in the URL
     */
    public static function controller($controllers, $defaults = array('home')) {
        foreach((array) $controllers as $controller) {
            $controller = str_replace('.', '/', $controller);
            $wildcards = static::_repeat('(:any?)', static::$_segments);
            $pattern = trim($controller.'/'.$wildcards, '/');

            $uses = $controller;
            $attributes = compact('uses', 'defaults');

            static::register('*', $pattern, $attributes);
        }
    }

    /**
     * Find a route by the routes assigned name
     *
     * @access  public
     * @param   string  $name  Name of the route to find
     * @return  array
     */
    public static function find($name) {
        if(isset(static::$_names[$name])) {
            return static::$_names[$name];
        }

        /*
         * If no routes are found at this stage we'll assume no reverse routing
         * has been processed so we can kick it off here
         */
        foreach(static::_routes() as $method => $routes) {
            foreach($routes as $key => $value) {
                if(isset($value['as'])) {

                    /*
                     * Split the name at each pipe so we can give multiple names
                     * to a single route if required
                     */
                    $split = explode('|', $value['as']);

                    if(in_array($name, $split) or $value['as'] === $name) {
                        return static::$_names[$name] = array($key => $value);
                    }
                }
            }
        }
    }

    /**
     * Register a group of routes which share attributes
     *
     * @access  public
     * @param   array     $attributes  Attributes to assign to the group
     * @param   callback  $callback    Callback function to run
     */
    public static function group($attributes, $callback) {
        static::$_group = $attributes;

        call_user_func($callback);

        /*
         * Once the routes have been registered, we want to clear the group so the
         * attributes will not be given to any of the routes that are added
         * after the group is declared
         */
        static::$_group = null;
    }

    /**
     * Registers a route (or multiple routes) to the router
     *
     * @access  public
     * @param   string  $method  HTTP method for this route
     * @param   mixed   $route   Path for the route (or pattern)
     * @param   mixed   $action  Action to take when the route is called
     */
    public static function register($method, $route, $action) {
        if(ctype_digit($route)) {
            $route = '('.$route.')';
        }

        if(is_string($route)) {
            $route = explode(', ', $route);
        }

        /*
         * If the HTTP method is the catch-all shortcode then we'll set the
         * method to an array of each and this will then be looped in the next
         * section, resulting in a route and action for all methods
         */
        if($method == '*') {
            $method = static::$_methods;
        }

        /*
         * If we have multiple HTTP methods, loop through each one and register
         * a route for each of them with the route and action
         */
        if(is_array($method)) {
            foreach($method as $http) {
                static::register($http, $route, $action);
            }

            return;
        }

        foreach((array) $route as $url) {
            $url = ltrim(str_replace('-', '_', $url), '/');
            if($url == '') {
                $url = '/';
            }

            /*
             * If the route has a regular expression we class it as a fallback
             * route, this means if we get an exact match we can use that,
             * otherwise we fallback to the regular expression match
             */
            if($url[0] == '(') {
                $routes =& static::$_fallback;
            } else {
                $routes =& static::$_routes;
            }

            /*
             * If the action passed is already an array we'll just continue as
             * is, but otherwise we need to format the action as a valid
             * action array (a closure call or an action array)
             */
            if(is_array($action)) {
                $routes[$method][$url] = $action;
            } else {
                $routes[$method][$url] = static::_action($action);
            }

            if(!is_null(static::$_group)) {
                $routes[$method][$url] += static::$_group;
            }
        }
    }

    /**
     * Sets multiple routes to a single action
     *
     * @access  public
     * @param   array   $routes  Array of routes
     * @param   mixed   $action  Action to take when the route is called
     */
    public static function share($routes, $action) {
        foreach($routes as $route) {
            static::register($route[0], $route[1], $action);
        }
    }

    /**
     * Searches the routes matching method and URL
     *
     * @access  public
     * @param   string  $method  HTTP method being used
     * @param   string  $url     URL being requested by the client
     * @return  array
     */
    public static function route($method, $url = null) {
        $routes = (array) static::_method($method);

        if(is_null($url)) {
            $url = static::_current();
        }

        /*
         * Here we search for literal matches first, these are much faster and
         * can be very specific so they come first in priority
         */
        if(array_key_exists($url, $routes)) {
            $action = $routes[$url];

            return static::_handle($method, $url, $action);
        }

        if(!is_null($route = static::_match($method, $url))) {
            return $route;
        }
    }

    /**
     * Find the route(s) which uses the given action
     *
     * @access  public
     * @param   string  $action  Name of the action to find
     * @return  array
     */
    public static function uses($action) {
        if(isset(static::$_uses[$action])) {
            return static::$_uses[$action];
        }

        foreach(static::_routes() as $method => $routes) {
            foreach($routes as $key => $value) {
                if(isset($value['uses']) and $value['uses'] === $action) {
                    return static::$_uses[$action] = array($key => $value);
                }
            }
        }
    }

    /**
     * Adds a filter callback for the given pattern
     *
     * @access  public
     * @param   string  $pattern  Pattern to enable this filters
     * @param   mixed   $actions  Function to run when pattern is matched
     */
    public static function when($pattern, $actions = array()) {
        $pattern = '#'.str_replace('*', '(.*)', $pattern).'#';

        if(is_callable($actions['before'])) {
            static::$_filters[$pattern]['before'][] = $actions['before'];
        }

        if(is_callable($actions['after'])) {
            static::$_filters[$pattern]['after'][] = $actions['after'];
        }
    }

    /**
     * Converts a route action to a valid action array
     *
     * @access  protected
     * @param   mixed      $action  Action to convert
     * @return  array
     */
    protected static function _action($action) {
        if(is_string($action)) {
            $action = array('uses' => $action, 'as' => $action);
        } elseif($action instanceof Closure) {
            $action = array($action);
        }

        return (array) $action;
    }

    /**
     * Fetches the current URL requested
     *
     * @access  protected
     * @return  string
     */
    protected static function _current() {
        if(!is_null(static::$_current)) {
            return static::$_current;
        }

        static::$_current = isset($_GET['url']) ? trim(strtolower($_GET['url']), '/') : 'index.php';

        if(static::$_current === 'index.php') {
            static::$_current = '/';
        }

        return str_replace('-', '_', static::$_current);
    }

    protected static function _filters($route) {
        foreach(static::$_filters as $for => $callbacks) {
            if(preg_match($for, $route)) {
                return $callbacks;
            }
        }

        return false;
    }

    /**
     * Handles the processing of a routes action (does callback or loads controller)
     *
     * @access  protected
     * @param   sting      $method      HTTP method for this request
     * @param   string     $route       Route being requested
     * @param   array      $action      Formatted valid action array for processing
     * @param   array      $parameters  Attributes for the method
     */
    protected static function _handle($method, $route, $action, $parameters = array()) {
        $defaults = (array) Data::get($action, 'defaults');
        $filters = static::_filters($route);

        if(count($defaults) > count($parameters)) {
            $defaults = array_slice($defaults, count($parameters));
            $parameters = array_merge($parameters, $defaults);
        }

        if(!is_null($handler = Data::get($action, 'uses', null))) {
            $explode = array_merge(explode('@', $handler), $parameters);

            $parts['controller'] = str_replace('-', '_', array_shift($explode));
            $parts['action'] = str_replace('-', '_', array_shift($explode));
            $parts['parameters'] = $explode;

            require path('controllers').$parts['controller'].'.php';
            $controller = $parts['controller'] = $parts['controller'].'_Controller';
            $controller = new $controller();

            if(isset($controller->restful)) {
                $parts['action'] = String::lower($method).'_'.$parts['action'];
            }

            if(!method_exists($controller, $parts['action'])) {
                $parts['action'] = 'home';
            }

            return static::_process($controller, $action, $parts, $filters, 'controller');
        }

        /*
         * If we're at this stage we've found a callback to run, so we'll
         * process the parameters from the request and the callback and run
         * along with it all
         */
        $handler = Data::get($action, 'do', static::_handler($action));

        if(!is_null($handler)) {
            return static::_process($handler, $action, $parameters, $filters, 'closure');
        }
    }

    /**
     * Handles the closure function
     *
     * @access  protected
     * @param   array      $action  Action array to handle
     * @return  callback
     */
    protected static function _handler($action) {
        return Data::first($action, function($key, $value) {
            return is_callable($value);
        });
    }

    /**
     * Matches a HTTP method and URL to a registered route
     *
     * @access  protected
     * @param   string     $method  HTTP method name to match against
     * @param   string     $url     Requested route
     */
    protected static function _match($method, $url) {
        foreach(static::_method($method) as $route => $action) {

            /*
             * If the route contains regular expressions we'll swap out the
             * wildcards for the parameters passed and see if we find a route
             */
            if(strpos($route, '(') !== false) {
                $pattern = '#^'.static::_wildcards($route).'$#';

                if(preg_match($pattern, $url, $parameters)) {
                    return static::_handle($method, $route, $action, array_slice($parameters, 1));
                }
            }
        }
    }

    /**
     * Returns all routes which match the given HTTP method
     *
     * @access  protected
     * @param   string     $method  HTTP method to match against
     * @return  array
     */
    protected static function _method($method) {
        $routes = Data::get(static::$_routes, $method, array());

        return array_merge($routes, Data::get(static::$_fallback, $method, array()));
    }

    /**
     * Processes a handler
     *
     * @access  protected
     * @param   string     $handler     The name of the handler
     * @param   array      $action      Actions array for the handler
     * @param   array      $parameters  Parameters passed to the handler
     * @param   mixed      $filters     Filters to apply to the handler
     * @param   string     $type        Type of handler to process
     */
    protected static function _process($handler, $action, $parameters, $filters, $type) {
        if(is_callable($action['before'])) {
            call_user_func($action['before'], $parameters);
        }

        if(is_array($filters)) {
            if(array_key_exists('before', $filters)) {
                foreach($filters['before'] as $callback) {
                    call_user_func($callback);
                }
            }
        }

        if($type == 'controller') {
            call_user_func_array(array($handler, $parameters['action']), $parameters['parameters']);
        } else {
            call_user_func_array($handler, $parameters);
        }

        if(is_callable($action['after'])) {
            call_user_func($action['after'], $parameters);
        }

        if(is_array($filters)) {
            if(array_key_exists('after', $filters)) {
                foreach($filters['after'] as $callback) {
                    call_user_func($callback);
                }
            }
        }
    }

    /**
     * Repeats a string any number of times
     *
     * @access  protected
     * @param   string     $pattern  Pattern to repeat
     * @param   integer    $times    Number of times to repeat the string
     * @return  string
     */
    protected static function _repeat($pattern, $times) {
        return implode('/', array_fill(0, $times, $pattern));
    }

    /**
     * Fetches all registered routes with fallbacks appended to the end
     *
     * @access  protected
     * @return  array
     */
    protected static function _routes() {
        $routes = static::$_routes;

        foreach(static::$_methods as $method) {

                if(!isset($routes[$method])) {
                    $routes[$method] = array();
                }

                $fallback = Data::get(static::$_fallback, $method, array());

                /*
                 * When building the array of routes, we'll merge in all of the
                 * fallback routes for each request method individually, this allows
                 * us to avoid collisions when merging the arrays together
                 */
                $routes[$method] = array_merge($routes[$method], $fallback);
        }

        return $routes;
    }

    /**
     * Translate route URL wildcards into regular expressions
     *
     * @access  protected
     * @param   string     $key  Route to translate
     * @return  string
     */
    protected static function _wildcards($key) {
        list($search, $replace) = Data::divide(static::$_optional);

        /*
         * For optional parameters, first translate the wildcards to their
         * regular expression equivalent, sans the ")?" ending
         *
         * We'll add the endings back on when we know the replacement count
         */
        $key = str_replace($search, $replace, $key, $count);

        if($count > 0) {
            $key .= str_repeat(')?', $count);
        }

        return strtr($key, static::$_patterns);
    }

}

/* End of file: skeleton/core/router.php */