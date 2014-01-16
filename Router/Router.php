<?php

class Router
{

    /**
     * Catcher response actions.
     *
     * Catcher response actions are actions which match a
     * pattern with one or multiple wildcards, these are
     * used as a fallback when no identical pattern is found.
     *
     * @var array $catchers
     */
    protected $catchers = [];

    /**
     * Named response actions.
     *
     * @var array $named
     */
    protected $named = [];

    /**
     * Wildcard patterns.
     *
     * @var array $patterns
     */
    protected $patterns = [
        'mandatory' => [
            '(:alpha)' => '([a-zA-Z]+)',
            '(:all)' => '(.*)',
            '(:any)' => '([a-zA-Z0-9\.\-_%=]+)',
            '(:num)' => '([0-9]+)'
        ],
        'optional' => [
            '/(:all?)' => '(?:/(.*)',
            '/(:alpha?)' => '(?:/([a-zA-Z]+)',
            '/(:any?)' => '(?:/([a-zA-Z0-9\.\-_%=]+)',
            '/(:num?)' => '(?:/([0-9]+)',
        ]
    ];

    /**
     * Registered response actions.
     *
     * @var array $routes
     */
    protected $routes = [];

    /**
     * Separator used for splitting.
     *
     * @var string $separator
     */
    protected $separator = ',';

    /**
     * Current requested URL.
     *
     * @var string $url
     */
    protected $url;

    /**
     * HTTP verbs in use.
     *
     * @var array $verbs
     */
    protected $verbs = [
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PUT',
        'TRACE'
    ];

    /**
     * Get the current requested URL.
     *
     * @return string Current URL.
     */
    public function getCurrentUrl()
    {
        if (null !== $this->url) {
            return $this->url;
        }

        $url = 'index.php';

        if (isset($_GET['url'])) {
            $url = trim(strtolower($_GET['url']), '/');
        }

        if ($url === 'index.php') {
            $url = '/';
        }

        return (string) $this->url = $url;
    }

    /**
     * Fetch a response action by it's name.
     *
     * @param string $name Name of response action.
     * @return mixed
     */
    public function getRouteByName($name)
    {
        if (array_key_exists($name, $this->named)) {
            return $this->named[$name];
        }

        foreach ($this->getRoutes() as $verb => $routes) {
            foreach ($routes as $pattern => $response) {
                $split = array_map('trim', explode($this->separator, $response['name']));

                if (in_array($name, $split) || $response['name'] === $name) {
                    return $this->names[$name] = [$pattern => $response];
                }
            }
        }
    }

    /**
     * Fetches all registered response actions.
     *
     * @return array Routes.
     */
    public function getRoutes()
    {
        $routes = $this->routes;

        foreach ($this->verbs as $verb) {
            if (!array_key_exists($verb, $routes)) {
                $routes[$verb] = [];
            }

            if (!array_key_exists($verb, $this->catchers)) {
                $this->catchers[$verb] = [];
            }

            $routes[$verb] = array_merge($routes[$verb], $this->catchers[$verb]);
        }

        return $routes;
    }

    /**
     * Fetches response actions which match the given HTTP verb.
     *
     * @param string $verb HTTP verb.
     * @return array
     */
    public function getRoutesByVerb($verb)
    {
        if (!array_key_exists($verb, $this->routes)) {
            $this->routes[$verb] = [];
        }

        if (!array_key_exists($verb, $this->catchers)) {
            $this->catchers[$verb] = [];
        }

        return array_merge($this->routes[$verb], $this->catchers[$verb]);
    }

    /**
     * Handle a request.
     *
     * @param null|string $url URL to handle (optional).
     * @param null|string $verb HTTP verb to use (optional).
     */
    public function route($url = null, $verb = null)
    {
        if (null === $verb) {
            $verb = 'GET';

            if (isset($_SERVER['REQUEST_METHOD'])) {
                $verb = $_SERVER['REQUEST_METHOD'];
            }

            if ($verb !== 'GET') {
                $verb = 'POST';

                if (isset($_POST['_method'])) {
                    $verb = $_POST['_method'];
                }
            }
        }

        if (null === $url) {
            $url = $this->getCurrentUrl();
        }

        $routes = $this->getRoutesByVerb($verb);

        if (array_key_exists($url, $routes)) {
            return $this->handle($routes[$url]);
        }

        return $this->matchPattern($verb, $url);
    }

    /**
     * Registers a response action.
     *
     * @param array|string $verb HTTP verbs to respond to.
     * @param string $pattern URL pattern to match.
     * @param array $response Response action array.
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setRoute($verbs, $patterns, array $response)
    {
        if (!array_key_exists('do', $response)) {
            throw new \InvalidArgumentException(__METHOD__ . ': Response action array must contain a \'do\' key.');
        }

        if (!array_key_exists('name', $response)) {
            throw new \InvalidArgumentException(__METHOD__ . ': Response action array must contain a \'name\' key.');
        }

        if (ctype_digit($patterns)) {
            $patterns = '(' . $patterns . ')';
        }

        if (is_string($patterns)) {
            $patterns = array_map('trim', explode($this->separator, $patterns));
        }

        if ($verbs === '*') {
            $verbs = $this->verbs;
        }

        if (is_array($verbs)) {
            foreach($verbs as $verb) {
                $this->setRoute($verb, $patterns, $response);
            }

            return $this;
        }

        $patterns = (array) $patterns;

        foreach ($patterns as $pattern) {
            $pattern = ltrim(str_replace('-', '_', $pattern), '/');

            if ($pattern == '') {
                $pattern = '/';
            }

            if ($pattern[0] === '(') {
                $this->catchers[$verbs][$pattern] = $response;
            } else {
                $this->routes[$verbs][$pattern] = $response;
            }
        }

        return $this;
    }

    /**
     * Set the separator to use when splitting names and patterns.
     *
     * @param string $separator Separator.
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setSeparator($separator)
    {
        if (!is_string($separator)) {
            throw new \InvalidArgumentException(__METHOD__ . ': Separator must be of type string, ' . gettype($separator) . ' given.');
        }

        $this->separator = $separator;

        return $this;
    }

    /**
     * Handle the response action.
     *
     * @param array $response Response action to perform.
     * @param array $parameters Parameters to pass.
     */
    protected function handle(array $response, array $parameters = [])
    {
        $defaults = [];
        if (array_key_exists('defaults', $response)) {
            $defaults = $response['defaults'];
        }

        if (count($defaults) > count($parameters)) {
            $defaults = array_slice($defaults, count($parameters));
            $parameters = array_merge($parameters, $defaults);
        }

        if (array_key_exists('do', $response)) {
            return call_user_func_array($response['do'], $parameters);
        }
    }

    /**
     * Matches a URL pattern to a response action.
     *
     * @param string $verb HTTP verb.
     * @param string $pattern URL pattern.
     */
    protected function matchPattern($verb, $pattern)
    {
        foreach ($this->getRoutesByVerb($verb) as $route => $response) {
            if (false !== strpos($route, '(')) {
                $regex = '#^' . $this->formatWildcards($route) . '$#';

                if (preg_match($regex, $pattern, $parameters)) {
                    return $this->handle($response, array_slice($parameters, 1));
                }
            }
        }
    }

    /**
     * Formats wildcard patterns.
     *
     * @param string $pattern URL pattern.
     * @return string Formatted wildcard pattern.
     */
    protected function formatWildcards($pattern)
    {
        list($search, $replace) = [array_keys($this->patterns['optional']), array_values($this->patterns['optional'])];

        $pattern = str_replace($search, $replace, $pattern, $count);

        if ($count > 0) {
            $pattern = $pattern . str_repeat(')?', $count);
        }

        return strtr($pattern, $this->patterns['mandatory']);
    }

}
