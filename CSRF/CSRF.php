<?php 
/**
 * Copyright 2013 Nathan Marshall
 *
 * @author     Nathan Marshall (FDL) <nathan@fludotlove.com>
 * @copyright  (c) 2013, Nathan Marshall
 */

namespace FDL;

/**
 * Handles application cross-site request forgery protection.
 *
 * @author   Nathan Marshall
 */
class CSRF {

    /**
     * Number of seconds in a minute.
     *
     * @const MINUTE
     */
    const MINUTE = 60;

    /**
     * Reasons tokens failed to validate.
     *
     * @access protected
     * @var array
     */
    protected $_failureReasons = [];

    /**
     * Perform an origin check on tokens?
     *
     * @access protected
     * @var boolean
     */
    protected $_performOriginCheck = true;

    /**
     * Create a new CSRF instance.
     *
     * @access public
     */
    public function __construct($originCheck = true)
    {
        $this->_performOriginCheck = $originCheck == true ? true : false;
        $this->_origin = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Check a CSRF token is valid.
     *
     * @access public
     * @param string $key
     * @param string $token
     * @param integer $timespan
     * @return boolean
     */
    public function checkToken($key, $token, $timespan = null)
    {
        $hash = $this->_getToken('tokens.'.$key);

        // Check the token exists.
        if($hash === null) {
            $this->_setFailureReason($key, 'Token is not set in the session');

            return false;
        }

        $this->_forgetToken('tokens.'.$key);

        // Check the token origin.
        if($this->_performOriginCheck && !$this->_checkTokenOrigin($hash)) {
            $this->_setFailureReason($key, 'Origin of the token is incorrect');

            return false;
        }

        // Check the token is correct.
        if($token !== $hash) {
            $this->_setFailureReason($key, 'Token provided did not match the session');

            return false;
        }

        // Check if the token has exipired.
        if($timespan !== null && is_int($timespan) && intval(substr(base64_decode($hash), 0, 10)) + ($timespan * self::MINUTE) < time()) {
            $this->_setFailureReason($key, 'Token has expired; expires after '.$timespan.' minutes');

            return false;
        }

        return true;
    }

    /**
     * Generate a new CSRF token.
     *
     * @access public
     * @param type $key
     * @return string
     */
    public function generateToken($key) {
        $tokenString = sha1($this->_randomString(mt_rand(18, 26)));

        $token = base64_encode(time().$this->_origin.$tokenString);

        $this->_setToken('tokens.'.$key, $token);

        return $token;
    }

    /**
     * Get the reason a token failed to validate.
     *
     * @access public
     * @param string $key
     * @return string
     */
    public function getFailureReason($key)
    {
        if(array_key_exists($key, $this->_failureReasons)) {
            return $this->_failureReasons[$key];
        }
    }

    /**
     * Check the origin of the token.
     *
     * A combination of the source IP of the TCP connection and user agent
     * should be enough to determine the origin is the same.
     *
     * @access protected
     * @param string $key
     * @param string $hash
     * @return boolean
     */
    protected function _checkTokenOrigin($hash)
    {
        if($this->_origin != substr(base64_decode($hash), 10, 40)) {
            return false;
        }

        return true;
    }

    /**
     * Remove a token from the session.
     *
     * @access protected
     * @param string $key
     */
    protected function _forgetToken($key)
    {
        $array =& $_SESSION;
        $keys = explode('.', $key);

        while(count($keys) > 1) {
            $key = array_shift($keys);
            if(!isset($array[$key]) or !is_array($array[$key])) {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }

    /**
     * Get a token value from the session.
     *
     * @access protected
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getToken($key, $default = null)
    {
        $array = $_SESSION;
        $keys = explode('.', $key);

        foreach($keys as $segment) {
            if(!is_array($array) or !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Set the reason a token failed to validate.
     *
     * @access protected
     * @param string $key
     * @param string $reason
     */
    protected function _setFailureReason($key, $reason)
    {
        $this->_failureReasons[$key] = $reason;
    }

    /**
     * Set a token value in the session.
     *
     * @access protected
     * @param string $key
     * @param string $value
     */
    protected function _setToken($key, $value)
    {
        $array =& $_SESSION;
        $keys = explode('.', $key);

        while(count($keys) > 1) {
            $key = array_shift($keys);
            if(!isset($array[$key]) or !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * Creates a random (enough) string.
     *
     * @access protected
     * @param integer $length
     * @return string
     */
    protected function _randomString($length)
    {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, $length);
    }

}