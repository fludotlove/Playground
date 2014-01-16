<?php

namespace Routing;

/**
 * 
 * 
 * @author Nathan Marshall <nathan@fludotlove.com>
 */
class Router 
{

    /**
     * Contains the current URL being requested.
     * 
     * @var string $currentUrl
     */
    protected $currentUrl;

    /**
     * Fallback routes.
     * 
     * When a route contains a regular expression match, it 
     * is classed as a fallback route.
     * 
     * @var array $fallbackRoutes
     */
    protected $fallbackRoutes = [];

    /**
     * Routes which have been named.
     * 
     * @var array $namedRoutes
     */
    protected $namedRoutes = [];

    /**
     * Actions which have been reverse routed.
     * 
     * @var array $reversedRoutes
     */
    protected $reversedRoutes = [];

    /**
     * Routes registered with the router.
     * 
     * @var array $routes
     */
    protected $routes = [
        'CONNECT' => [], 
        'DELETE' => [], 
        'HEAD' => [], 
        'GET' => [], 
        'OPTIONS' => [], 
        'PATCH' => [], 
        'POST' => [], 
        'PUT' => [], 
        'TRACE' => []
    ];

    /**
     * Maximum number of URL segments.
     * 
     * @param int $segmentCount
     */
    protected $segmentCount = 5;

    public function __construct()
    {}

    /**
     * Set the maximum number of URL segments.
     * 
     * @param int $segmentCount Number of segments.
     * @return self
     */
    public function setSegmentCount($segmentCount)
    {
        if (!is_int($segmentCount)) {
            throw new \InvalidArgumentException('Router @ ' . __METHOD__ . ': method expects an integer, given ' . gettype($segmentCount));
        }

        $this->segmentCount = $segmentCount;

        return $this;
    }

}