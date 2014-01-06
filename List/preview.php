<?php

if(empty($_POST)) {
    exit;
}

require 'parsedown.php';

$pd = Parsedown::instance();
$item = isset($_POST['item']) ? $_POST['item'] : '';
$item = preg_replace('/\#(\w+)/', '<a class="tag" href="?tag=$1">$1</a>', $item);

$parsed = $pd->parse($item);

echo $parsed;