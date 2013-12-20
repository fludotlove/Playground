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
 * A simple benchmarking class.
 * 
 * @author Nathan Marshall
 */
class Benchmark {

    /**
     * Store for markers.
     *
     * @access protected
     * @var array
     */
    protected static $_markers = array();

    /**
     * Set a marker.
     *
     * @access public
     * @param string $name
     */
    public static function addMarker($name) 
    {
        static::$_markers[$name] = microtime();
    }

    /**
     * Calculate the time between two markers.
     *
     * @access public
     * @param string $start
     * @param string $end
     * @param integer $decimals
     * @return string
     */
    public static function calculateMarkerToMarker($start, $end = null, $decimals = 4) 
    {
        if(!array_key_exists($start, static::$_markers)) {
            throw new InvalidArgumentException('Marker ['.$start.'] does not exist.');
        }

        // If the marker doesn't exist use now as a marker.
        if(!array_key_exists($end, static::$_marker)) {
            static::$_markers[$end] = microtime();
        }

        list($sm, $ss) = explode(' ', static::$_markers[$start]);
        list($em, $es) = explode(' ', static::$_markers[$end]);

        return (string) number_format(($em + $es) - ($sm + $ss), $decimals);
    }
    
    /**
     * Calculate the time between a marker and now.
     *
     * @access public
     * @param string $start
     * @param integer $decimals
     * @return string
     */
    public static function calculateMarkerToNow($start, $decimals = 4) 
    {
        return static::calculateMarkerToMarker($start, null, $decimals);
    }

}