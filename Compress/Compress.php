<?php
/**
* Copyright 2013 Nathan Marshall
*
* @author Nathan Marshall (FDL) <nathan@fludotlove.com>
* @copyright (c) 2013, Nathan Marshall
*/

namespace FDL;

/**
* Compresses CSS files and replaces comment variables in the CSS.
*
* @author Nathan Marshall
*/
class Compress {

    /**
     * Number of seconds in a day.
     *
     * @const DAY
     */
    const DAY = 86400;

    /**
     * Directory to use when creating and compressing files.
     *
     * @access protected
     * @var string
     */
    protected $_directory;

    /**
     * Create a new instance of the compressor.
     *
     * @access public
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->_directory = $directory;
    }

    /**
     * Build a compressed CSS file.
     *
     * @access public
     * @param array $files
     * @param integer $timeout
     * @param mixed $prefix
     * @return string
     */
    public function build(array $files, $timeout = 7, $prefix = null)
    {
        if(is_null($prefix) || $prefix === '') {
            $prefix = 'gen_';
        }

        $this->_cleanOldFiles($timeout * self::DAY);

        $path = '';
        foreach($files as $file) {
            $path .= filemtime($file);
        }

        $path = $this->_directory.'/'.$prefix.sha1($path).'.css';
        if(file_exists($path)) {
            return $path;
        } else {
            return $this->_crushFiles($files, $path);
        }
    }

    /**
     * Remove old compressed files from the directory.
     *
     * @access protected
     * @param integer $timeout
     */
    protected function _cleanOldFiles($timeout)
    {
        $files = glob($this->_directory.'/*');

        foreach($files as $file) {
            if((filemtime($file) + $timeout) < time() && strstr($file, $prefix) !== false) {
                unlink($file);
            }
        }
    }

    /**
     * Crush multiple files into a compressed file.
     *
     * @access protected
     * @param array $files
     * @param string $name
     * @return string
     */
    protected function _crushFiles(array $files, $name)
    {
        $crushed = '';
        foreach($files as $file) {
            $contents = file_get_contents($this->_directory.'/'.$file);

            $contents = $this->_replaceVariables($contents);

            $crushed .= $contents;
        }

        $crushed = preg_replace([
            '#\s*{\s*#', '#\s*}\s*#', '#\s*,\s*#',
            '#\s*;\s*#', '#:\s*#', '#\s\s+#',
            '#/\*[^*]*\*+([^/][^*]*\*+)*/#'
        ], [
            '{', '}', ',', ';', ':', ' ', ''
        ], $crushed);
        $crushed = str_replace(';}', '}', $crushed);
        $crushed = '/* FDL compression'."\r\n".' * File contains: '.implode($files, ', ').' */'."\r\n".$crushed;

        file_put_contents($this->_directory.'/'.$name, $crushed, LOCK_EX);

        return $name;
    }

    /**
     * Replace comment variables within the content.
     *
     * @access protected
     * @param string $contents
     * @return string
     */
    protected function _replaceVariables($contents)
    {
        preg_match_all('#(\$[\w]+)\s?=\s?(.*)\;#i', $contents, $matches, PREG_SET_ORDER);
        foreach($matches as $variable) {
            $contents = str_replace($variable[1], $variable[2], $contents);
        }

        return $contents;
    }

}