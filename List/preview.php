<?php

if(empty($_POST)) {
    exit;
}

require 'parsedown.php';

$pd = Parsedown::instance();
$item = isset($_POST['item']) ? $_POST['item'] : '';

$parsed = $pd->parse($item);
$parsed = preg_replace('/\#(\w+)/', '<a class="tag" href="?tag=$1">$1</a>', $parsed);

echo $parsed;