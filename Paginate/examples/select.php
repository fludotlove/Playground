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
asort($data);

$pages = new Paginate($data, isset($_GET['page']) ? $_GET['page'] : 1, 7);

?>

<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Paginate</title>

        <style type="text/css">
            span.page-description, span.page-previous, .page-navigation-dropdown, span.page-next {
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

        <?php if ($pages->getLastPage() > 1): ?>

            <span class="page-description">Showing page <?php echo $pages->getCurrentPage(); ?> of <?php echo $pages->getLastPage(); ?></span>

            <?php if ($pages->getCurrentPage() <= 1): ?>
                <span class="page-previous disabled">Previous</span>
            <?php else: ?>
                <span class="page-previous"><a href="<?php echo $pages->getQueryString($pages->getPreviousPage()); ?>">Previous</a></span>
            <?php endif; ?>

            <form class="pagination page-navigation-dropdown" method="GET" action="#">
                <?php foreach ($pages->getQueryParameters() as $key => $value): ?>
                    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
                <?php endforeach; ?>

                <select name="page">
                    <?php for ($i = 0; $i <= $pages->getLastPage(); $i++): ?>
                        <option value="<?php echo $i; ?>"<?php echo $i == $pages->getCurrentPage() ? ' selected="selected"' : ''; ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <input type="submit" value="Go" />
            </form>

            <?php if ($pages->getCurrentPage() >= $pages->getLastPage()): ?>
                <span class="page-next disabled">Next</span>
            <?php else: ?>
                <span class="page-next"><a href="<?php echo $pages->getQueryString($pages->getNextPage()); ?>">Next</a></span>
            <?php endif; ?>

        <?php endif; ?>

    </body>
</html>