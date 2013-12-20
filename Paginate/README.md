Paginate
========

A simple and elegant pagination class for PHP 5.4 or above.

Basic Usage
-------------
Include the paginate class into your PHP code.

    require '../path/to/paginate.php';
    use FDL\Paginate;
    
Create a new instance of the paginate class including the items to paginate, the requested page, and the number of results per page.

    $items = array('one', 'two', 'three');
    $paginate = new Paginate($items, isset($_GET['page']) ? $_GET['page'] : 1, 5);

Now use the methods detailed below to render your pagination links and items into a view or PHP page (some basic examples are included in the `examples` directory).

Methods
-------

#### Get Current Page ####
`getCurrentPage()` - Get the current page number.

    $currentPageNumber = $paginate->getCurrentPage();

#### Get Last Page ####
`getLastPage()` - Get the last page number of the pagination.

    $lastPageNumber = $paginate->getLastPage();

#### Get Next Page ####
`getNextPage()` - Get the next page number of the pagination.

    $nextPageNumber = $paginate->getNextPage();

#### Get Previous Page ####
`getPreviousPage()` - Get the previous page number of the pagination.

    $previousPageNumber = $paginate->getPreviousPage();

#### Get Items ####
`getItems()` - Get all of the items in the pagination.

    $items = $paginate->getItems();

#### Get Current Page Items ####
`getPageItems()` - Get the items for the current page of the pagination.

    $pageItems = $paginate->getPageItems();
    
#### Get First Item ####
`getFromItem()` or `getFirstItem()` - Get the first item number of the current page.

    $firstItemNumber = $paginate->getFromItem();

#### Get Last Item ####
`getToItem()` or `getLastItem` - Get the last item number of the current page.

    $lastItemNumber = $paginate->getToItem();
    
#### Get Items per Page ####
`getPerPage()` - Get the number of items being displayed per page.

    $perPage = $paginate->getPerPage();
    
#### Get Total ####
`getTotal()` - Get total number of items for pagination.

    $totalItems = $paginate->getTotal();

#### Add Query ####
`addQuery($key, $value)` - Register a query string value to the pagination links.

    $paginate->addQuery('order', 'ascending');
    
Pagination link query string becomes: `?page=1&order=ascending`

You can chain this method to add more than one additional query string key, or use the `addQueryArray()` method. An example of chaining this method:

    $paginate->addQuery('order', 'ascending')
             ->addQuery('category', 'dvds')
             ->addQuery('genre', 'horror');
    
#### Add Query by Array ####
`addQueryArray($array)` - Register an array of query string values to the pagination links.

    $pagination->addQueryArray([
        'order' => 'descending', 
        'category' => 'games'
    ]);
    
Pagination link query string becomes: `?page=1&order=descending&category=games`

#### Get Query Parameters ####
`getQueryParameters()` - Get the registered query string parameters as an array.

    $params = $paginate->getQueryParameters();

#### Get Query String ####
`getQueryString($page)` - Get a URL querystring for the specified page (with additional registered parameters).

    $pageOneQueryString = $paginate->getQueryString(1);

#### Set Items ####
`setItems($array)` - Set the pagination items (override those from `__construct`).

    $paginate->setItems(['one', 'two', 'three', 'four']);
