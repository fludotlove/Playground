<?php
/**
* Copyright 2013 Nathan Marshall
*
* @author Nathan Marshall (FDL) <nathan@fludotlove.com>
* @copyright (c) 2013, Nathan Marshall
*/

namespace FDL;

use InvalidArgumentException;

/**
 * A simple view rendering class.
 *
 * @author Nathan Marshall
 */
class Outline {

    /**
     * Number of seconds in an hour.
     *
     * @const HOUR
     */
    const HOUR = 3600;

    /**
     * Base path to the views directory.
     *
     * @access protected
     * @var string
     */
    protected $_basePath;

    /**
     * Number of hours to cache views for.
     *
     * @access protected
     * @var integer
     */
    protected $_cacheLimit = 0;

    /**
     * Creates a new Outline instance.
     *
     * @access public
     * @param string $basePath
     * @param integer $cacheLimit
     */
    public function __construct($basePath, $cacheLimit = 0)
    {
        $this->_basePath = (string) $basePath;
        $this->_cacheLimit = (integer) $cacheLimit;
    }

    /**
     * Displays a view.
     *
     * @access public
     * @param string $templateName
     * @param array $data
     * @param boolean $allowCache
     */
    public function displayView($template, $data = [], $allowCache = false)
    {
        echo $this->renderView($template, $data, $allowCache);
    }

    /**
     * Returns the base path to the views directory.
     *
     * @access public
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Returns the number of hours views are cached for.
     *
     * @access public
     * @return integer
     */
    public function getCacheLimit()
    {
        return $this->_cacheLimit;
    }

    /**
     * Renders a view ready for output.
     *
     * @access public
     * @param string $template
     * @param array $data
     * @param boolean $allowCache
     * @return string
     */
    public function renderView($template, $data = [], $allowCache = false)
    {
        if($allowCache && $this->_cacheLimit > 0) {
            $cacheTemplatePath = $this->_formatCachePath($template);

            if(is_file($cacheTemplatePath) != false) {
                $modifiedTime = filemtime($cacheTemplatePath);

                if($modifiedTime < ($this->_cacheLimit * self::HOUR) + time()) {
                    return file_get_contents($cacheTemplatePath);
                }
            }
        }

        if($data === null) {
            $data = [];
        }

        $templatePath = $this->_formatPath($template);
        $publicVariables = $this->_getPublicVariables();
        foreach($publicVariables as $key => $value) {
            unset($this->$key);
        }

        foreach($data as $key => $value) {
            $this->_setData($key, $value);
        }

        if(is_file($templatePath) != false) {
            ob_start();

            echo eval('?>'.str_replace('<?=', '<?php echo ', file_get_contents($templatePath)));

            $buffer = ob_get_contents();
            @ob_end_clean();

            if($allowCache && $this->_cacheLimit > 0) {
                file_put_contents($cacheTemplatePath, $buffer);
            }

            return (string) $buffer;
        }
    }

    /**
     * Set the base path to the views directory.
     *
     * @access public
     * @param string $path
     * @return Outline
     */
    public function setBasePath($path)
    {
        $this->_basePath = (string) $path;

        return $this;
    }

    /**
     * Sets the number of hours views are cached for.
     *
     * @access public
     * @param type $hours
     * @return Outline
     * @throws InvalidArgumentException
     */
    public function setCacheLimit($hours = 0)
    {
        if(!is_numeric($hours)) {
            throw new InvalidArgumentException('Limit should be numeric');
        }

        $this->_cacheLimit = (integer) $hours;

        return $this;
    }

    /**
     * Formats the path to the cached views directory.
     *
     * @access protected
     * @param string $view
     * @return string
     */
    protected function _formatCachePath($view)
    {
        return (string) $this->_basePath.'cache\\'.str_replace('/', DIRECTORY_SEPARATOR, $view).'.php';
    }

    /**
     * Formats the path to the views directory.
     *
     * @access protected
     * @param string $view
     * @return string
     */
    protected function _formatPath($view)
    {
        return (string) $this->_basePath.str_replace('/', DIRECTORY_SEPARATOR, $view).'.php';
    }

    /**
     * Get an array of public object variables.
     *
     * @access protected
     * @return array
     */
    protected function _getPublicVariables()
    {
        $object = $this;

        return function() use ($object) {
            return get_object_vars($object);
        };
    }

    /**
     * Sets the data from a view into the object.
     *
     * @access protected
     * @param string $key
     * @param mixed $value
     */
    protected function _setData($key, $value)
    {
        $this->$key = $value;
    }

}