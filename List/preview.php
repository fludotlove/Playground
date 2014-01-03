<?php

if(empty($_POST)) {
    exit;
}

require 'parsedown.php';

$pd = Parsedown::instance();
$item = isset($_POST['item']) ? $_POST['item'] : '';

$parsed = $pd->parse($item);

echo $parsed;