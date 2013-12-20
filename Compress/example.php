<?php
/**
 * Copyright 2013 Nathan Marshall
 *
 * @author     Nathan Marshall (FDL) <nathan@fludotlove.com>
 * @copyright  (c) 2013, Nathan Marshall
 */

namespace FDL;

date_default_timezone_set('Europe/London');

ini_set('display_errors', true);
error_reporting(-1);

require_once('Compress.php');

$compress = new Compress('../www/assets/css');

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title>Compress Demo</title>
        <link rel="stylesheet" type="text/css" href="../path/to/css/<?php $compress->build(['reset.css', 'fonts.css', 'content.css'], 7);?>" />
    </head>
    <body>

        <!-- Content here -->

    </body>
</html>