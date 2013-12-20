<?php
/**
* Copyright 2013 Nathan Marshall
*
* @author Nathan Marshall (FDL) <nathan@fludotlove.com>
* @copyright (c) 2013, Nathan Marshall
*/

namespace FDL;

date_default_timezone_set('Europe/London');

ini_set('display_errors', true);
error_reporting(-1);

require 'Outline.php';

$outline = new Outline(__DIR__.'\\templates\\', 24);

$output = $outline->renderView('header', [
    'key' => 'value',
]);

echo $output;