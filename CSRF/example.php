<?php
/**
 * Copyright 2013 Nathan Marshall
 *
 * @author     Nathan Marshall (FDL) <nathan@fludotlove.com>
 * @copyright  (c) 2013, Nathan Marshall
 */
 
namespace FDL;

session_start();
date_default_timezone_set('Europe/London');

ini_set('display_errors', true);
error_reporting(-1);

require_once('CSRF.php');

$confirmation = '';
$csrf = new CSRF;

if(!empty($_POST)) {
    if($csrf->checkToken('myFormToken', $_POST['myFormToken'], 2)) {
        $confirmation = 'Token was <strong>correct</strong>!';
    } else {
        $reason = $csrf->getFailureReason('myFormToken');
        $confirmation = 'Token was incorrect ['.$reason.'].';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title>CSRF Demo</title>
    </head>
    <body>

        <p><?php echo $confirmation;?></p>

        <form action="#" method="POST">
            <input type="hidden" name="myFormToken" value="<?php echo $csrf->generateToken('myFormToken');?>" />

            <input type="submit" value="Check Token" />
        </form>

        <pre><?php echo print_r($_SESSION, true);?></pre>

    </body>
</html>