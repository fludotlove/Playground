<?php

date_default_timezone_set('Europe/London');

chdir(__DIR__);

require 'parsedown.php';

function store_load()
{
    if (file_exists('storage/store.json')) {
        return file_get_contents('storage/store.json');
    }

    return json_encode(array());
}

function output_view($name, $data = array())
{
    if (file_exists('views/' . $name . '.tpl.php')) {
        $contents = file_get_contents('views/' . $name . '.tpl.php');
        ob_start();

        extract($data);

        echo eval('?>' . str_replace('<?=', '<?php echo ', $contents));

        $buffer = ob_get_contents();
        @ob_end_clean();

        return $buffer;
    }

    return null;
}

function update_store($store) {
    return file_put_contents('storage/store.json', json_encode($store), LOCK_EX);
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = substr($url, 0, strrpos($url, '?'));

$store = store_load();
$store = json_decode($store, true);

if (!empty($_POST)) {
    if (isset($_POST['item_description']) && $_POST['item_description'] !== '') {
        $hash = hash('sha256', time() . mt_rand(0, 999));

        $store[$hash] = array(
            'id' => $hash,
            'description' => $_POST['item_description'],
            'complete' => false,
            'added' => date('Y-m-d H:i:s ', time())
        );
    } elseif (isset($_POST['done'])) {
        $store[$_POST['done']]['complete'] = date('Y-m-d H:i:s', time());
    } elseif (isset($_POST['undo'])) {
        $store[$_POST['undo']]['complete'] = false;
    }

    update_store($store);
}

$complete = $incomplete = array();

foreach ($store as $item) {
    $item['description'] = preg_replace('/\#(\w+)/', '<a class="tag" href="?tag=$1">$1</a>', $item['description']);

    if (isset($_GET['tag'])) {
        if (strpos($item['description'], $_GET['tag']) === false) {
            continue;
        }
    }

    if ($item['complete'] === false) {
        $incomplete[$item['id']] = $item;
    } else {
        $complete[$item['id']] = $item;
    }
}

echo output_view('header');
echo output_view('list', array(
    'pd' => Parsedown::instance(),
    'store' => array(
        'complete' => $complete,
        'incomplete' => $incomplete
    ),
    'url' => $url
));
echo output_view('footer');