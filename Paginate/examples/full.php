<?php

ini_set('display_errors', true);
error_reporting(-1);

require '../Paginate.php';
use FDL\Paginate;

$data = [
    ['name' => 'Mickey Mouse'],
    ['name' => 'Donald Duck'],
    ['name' => 'Betty Boop'],
    ['name' => 'Buzz Lightyear'],
    ['name' => 'Lightning McQueen'],
    ['name' => 'Rusty Mator'],
    ['name' => 'Goofy'],
    ['name' => 'Spider-Man'],
    ['name' => 'Batman'],
    ['name' => 'Bart Simpson'],
    ['name' => 'Lisa Simpson'],
    ['name' => 'Homer Simpson'],
    ['name' => 'Bugs Bunny'],
    ['name' => 'Elmer Fudd'],
    ['name' => 'Bobby Hill'],
    ['name' => 'Felix the Cat'],
    ['name' => 'Woody Woodpecker'],
    ['name' => 'George Jetson'],
    ['name' => 'Pink Panther'],
    ['name' => 'Underdog'],
    ['name' => 'Winnie the Pooh'],
    ['name' => 'Scooby-Doo'],
    ['name' => 'Porky Pig'],
    ['name' => 'Daffy Duck'],
    ['name' => 'Eric Cartman'],
    ['name' => 'SpongeBob SquarePants'],
    ['name' => 'Wile E. Coyote'],
    ['name' => 'Popeye'],
    ['name' => 'Fred Flintstone'],
    ['name' => 'Charlie Brown']
];

$pages = new Paginate($data, isset($_GET['page']) ? $_GET['page'] : 1, 7);

if(isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc'])) {
    switch ($_GET['order']) {
        case 'asc':
            asort($data);
            break;
        case 'desc':
            arsort($data);
            break;
    }

    $pages->addQuery('order', $_GET['order']);
    $pages->setItems($data); // Set re-ordered data.
}

?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Paginate</title>

        <style type="text/css">
            .page-navigation {
                list-style: none;
                padding-left: 0;
            }
            .page-navigation li {
                float: left;
                margin-right: 0.5em;
            }
        </style>
    </head>
    <body>
        <h1>Paginate</h1>

        <ul class="page-items">
            <?php foreach ($pages->getPageItems() as $item): ?>
                <li><?php echo $item['name']; ?></li>
            <?php endforeach; ?>
        </ul>

        <p>Order: <a href="?page=<?php echo $pages->getCurrentPage(); ?>&order=asc">ASC</a> | <a href="?page=<?php echo $pages->getCurrentPage(); ?>&order=desc">DESC</a></p>

        <?php if ($pages->getLastPage() > 1): ?>

            <span class="page-description">Showing items <?php echo $pages->getFromItem(); ?> - <?php echo $pages->getToItem(); ?> of <?php echo $pages->getTotal(); ?> (page <?php echo $pages->getCurrentPage(); ?> of <?php echo $pages->getLastPage(); ?>)</span>

            <ul class="pagination page-navigation">
                <?php if ($pages->getCurrentPage() <= 1): ?>
                    <li class="disabled">First</li>
                    <li class="disabled">Previous</li>
                <?php else: ?>
                    <li><a href="<?php echo $pages->getQueryString(1); ?>">First</a></li>
                    <li><a href="<?php echo $pages->getQueryString($pages->getPreviousPage()); ?>">Previous</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $pages->getLastPage(); $i++): ?>
                    <?php if ($pages->getCurrentPage() == $i): ?>
                        <li class="active"><?php echo $i; ?></li>
                    <?php else: ?>
                        <li><a href="<?php echo $pages->getQueryString($i); ?>"><?php echo $i; ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($pages->getCurrentPage() >= $pages->getLastPage()): ?>
                    <li class="disabled">Next</li>
                    <li class="disabled">Last</li>
                <?php else: ?>
                    <li><a href="<?php echo $pages->getQueryString($pages->getNextPage()); ?>">Next</a></li>
                    <li><a href="<?php echo $pages->getQueryString($pages->getLastPage()); ?>">Last</a></li>
                <?php endif; ?>
            </ul>

        <?php endif; ?>

    </body>
</html>