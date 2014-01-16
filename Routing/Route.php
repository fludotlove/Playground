<?php

namespace Routing;

/**
 * 
 * 
 * 
 */
class Route 
{

    /**
     * Action to take when this route is accessed.
     * 
     * @var callback $action
     */
    protected $action;

    /**
     * Route paths.
     * 
     * @var array $route
     */
    protected $route = [];

    /**
     * HTTP verbs this route should respond to.
     * 
     * @var array $verbs;
     */
    protected $verbs = [];

    /**
     * Wildcard patterns/regular expressions.
     * 
     * @var array $wildcards
     */
    protected $wildcards = [
        'mandatory' => [
            '(:all)' => '(.*)',
            '(:alpha)' => '([a-zA-Z]+)',
            '(:any)' => '([a-zA-Z0-9\.\-_%=]+)',
            '(:num)' => '([0-9]+)'
        ],
        'optional' => [
            '/(:all?)' => '(?:/(.*)',
            '/(:alpha?)' => '(?:/([a-zA-Z]+)',
            '/(:any?)' => '(?:/([a-zA-Z0-9\.\-_%=]+)',
            '/(:num?)' => '(?:/([0-9]+)'
        ]
    ];

    public function __construct()
    {}

    /**
     * Get the route paths this route should respond to.
     * 
     * @return array Route paths.
     */
    public function getRoutes()
    {
        return $this->route;
    }

    /**
     * Get the HTTP verbs this route should respond to.
     * 
     * @return array The HTTP verbs.
     */
    public function getVerbs()
    {
        return $this->verbs;
    }

    /**
     * Route paths for this route to respond to.
     * 
     * @param mixed $route Route paths.
     * @return self
     */
    public function setRoutes($route)
    {
        if (ctype_digit($route)) {
            $route = '('.$route.')';
        }

        if (is_string($route)) {
            $route = explode(',', $route);
        }

        foreach((array) $route as $url) {
            $url = ltrim(str_replace('-', '_', $url), '/');
            $url = $url === '' ? '/' : $url;

            $this->route[] = $url;
        }

        return $this;
    }

    /**
     * Set the HTTP verbs this route should respond to.
     * 
     * @param array|string $verbs HTTP verbs to respond to.
     * @return self
     */
    public function setVerbs($verbs = '*')
    {
        if ($verbs === '*') {
            $verbs = ['CONNECT', 'DELETE', 'HEAD', 'GET', 'OPTIONS', 'PATCH', 'POST', 'PUT', 'TRACE'];
        }

        $this->verbs = (array)$verbs;

        return $this;
    }

}