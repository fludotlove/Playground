<?php


/**
 * Automatically load classes from namespaces.
 *
 * @author Nathan Marshall <nathan@fludotlove.com>
 */
class Autoloader
{

    /**
     * Registered class aliases.
     *
     * @var array $aliases
     */
    protected $aliases = [];

    /**
     * Registered namespace to directory mappings.
     *
     * @var array $namespaces
     */
    protected $namespaces = [];

    /**
     * Create an Autoload instance and register the autoloader.
     *
     * @return self
     */
    public function __construct()
    {
        spl_autoload_register([$this, 'load']);
    }

    /**
     * Get a registered alias.
     *
     * @param string $alias Alias name to get.
     * @return null|string
     */
    public function getAlias($alias)
    {
        return array_key_exists($alias, $this->aliases) ? $this->aliases[$alias] : null;
    }

    /**
     * Get a registered namespace to directory mapping.
     *
     * @param string $namespace Namespace to get.
     * @return null|string
     */
    public function getNamespace($namespace)
    {
        return array_key_exists($namespace, $this->namespaces) ? $this->namespaces[$namespace] : null;
    }

    /**
     * Get all registered namespace to directory mappings.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Attempt to automatically load a class.
     *
     * @param string $class Class name to load.
     */
    public function load($class)
    {
        if (array_key_exists($class, $this->aliases)) {
            return class_alias($this->aliases[$class], $class);
        }

        foreach ($this->namespaces as $namespace => $directory) {
            if (strpos($class, $namespace) === 0) {
                return $this->resolveNamespace($class, $namespace, $directory);
            }
        }

        return $this->resolve($class);
    }

    /**
     * Register a class alias.
     *
     * @param string $class Class name.
     * @param string $alias Alias.
     * @return self
     */
    public function setAlias($class, $alias)
    {
        $this->aliases[$alias] = $class;

        return $this;
    }

    /**
     * Register an array of namespace to directory mappings.
     *
     * @param array $namespaces Namespaces to register.
     * @param string $append String to append to namespaces.
     * @return self
     */
    public function setNamespaces(array $namespaces, $append = '\\')
    {
        $namespaces = $this->formatNamespaces($namespaces, $append);

        $this->namespaces = array_unique(array_merge($this->namespaces, $namespaces));

        return $this;
    }

    /**
     * Format namespaces.
     *
     * @param array $namespaces Namespaces to format.
     * @param string $append String to append to namespaces.
     * @return array
     */
    protected function formatNamespaces(array $namespaces, $append)
    {
        $formatted = [];

        foreach ($namespaces as $namespace => $directory) {
            $namespace = rtrim($namespace, $append) . $append;

            $formatted[$namespace] = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        return $formatted;
    }

    /**
     * Resolve a path to a class.
     *
     * @param string $class Class to resolve.
     * @param null|string $directory Directory to search in.
     */
    protected function resolve($class, $directory = null)
    {
        $file = str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $class);

        if (null !== $directory && file_exists($path = $directory . $file . '.php')) {
            return require $path;
        }
    }

    /**
     * Resolve a path to a namespaced class.
     *
     * @param string $class Class to resolve.
     * @param string $namespace Namespace.
     * @param string $directory Directory to search in.
     */
    protected function resolveNamespace($class, $namespace, $directory)
    {
        return $this->resolve(substr($class, strlen($namespace)), $directory);
    }

}