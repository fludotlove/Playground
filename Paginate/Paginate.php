<?php
/**
* Copyright 2013 Nathan Marshall
*
* @author Nathan Marshall (FDL) <nathan@fludotlove.com>
* @copyright (c) 2013, Nathan Marshall
*/

namespace FDL;

/**
* A simple and elegant pagination class.
*
* @author Nathan Marshall
*/
class Paginate {

    /**
     * Current page number.
     *
     * @access protected
     * @var integer
     */
    protected $_currentPage;

    /**
     * Key of the first item in this page.
     *
     * @access protected
     * @var integer
     */
    protected $_from;

    /**
     * Array of items to paginate.
     *
     * @access protected
     * @var array
     */
    protected $_items = [];

    /**
     * Last page number.
     *
     * @access protected
     * @var integer
     */
    protected $_lastPage;

    /**
     * Number of items per page.
     *
     * @access protected
     * @var integer
     */
    protected $_perPage;

    /**
     * Array of query string parameters to add to page URL.
     *
     * @access protected
     * @var array
     */
    protected $_query = [];

    /**
     * Page requested.
     *
     * @access protected
     * @var integer
     */
    protected $_requestPage;

    /**
     * Key of the last item in this page.
     *
     * @access protected
     * @var integer
     */
    protected $_to;

    /**
     * Total number of items.
     *
     * @access protected
     * @var integer
     */
    protected $_total;

    /**
     * Create an instance of the pagination class.
     *
     * @access public
     * @param  array $items
     * @param  integer|string $requestPage
     * @param  integer $perPage
     */
    public function __construct(array $items, $requestPage, $perPage)
    {
        $this->setItems($items);
        $this->_perPage = $perPage;
        $this->_requestPage = $requestPage < 1 ? 1 : (int) $requestPage;

        $this->_lastPage = $this->_calcLastPage();
        $this->_currentPage = $this->_calcCurrentPage();

        $this->_calcItemRanges();
    }

    /**
     * Add a querystring value to the pagination links.
     *
     * @access public
     * @param mixed $key
     * @param mixed $value
     * @return Paginate
     */
    public function addQuery($key, $value)
    {
        $this->_query[$key] = $value;

        return $this;
    }

    /**
     * Add an arrat of querystring values to the pagination links.
     *
     * @access public
     * @param array $keys
     * @return Paginate
     */
    public function addQueryArray(array $keys)
    {
        foreach ($keys as $key => $value) {
            $this->addQuery($key, $value);
        }

        return $this;
    }

    /**
     * Get the current page number.
     *
     * @access public
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * Get the first item number for the current page.
     *
     * @access public
     * @return integer
     */
    public function getFirstItem()
    {
        return $this->getFromItem();
    }

    /**
     * Get the first item number for the current page.
     *
     * @access public
     * @return integer
     */
    public function getFromItem()
    {
        return $this->_from;
    }

    /**
     * Get all of the pagination items.
     *
     * @access public
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Get the last item number for the current page.
     *
     * @access public
     * @return integer
     */
    public function getLastItem()
    {
        return $this->getToItem();
    }

    /**
     * Get the last page number.
     *
     * @access public
     * @return integer
     */
    public function getLastPage()
    {
        return $this->_lastPage;
    }

    /**
     * Get the next page number.
     *
     * @access public
     * @return integer
     */
    public function getNextPage()
    {
        return $this->_currentPage + 1;
    }

    /**
     * Get the items for this page.
     *
     * @access public
     * @return array
     */
    public function getPageItems()
    {
        return array_slice($this->_items, $this->_calcItemOffset(), $this->_perPage);
    }

    /**
     * Get the number of items per page.
     *
     * @access public
     * @return integer
     */
    public function getPerPage()
    {
        return $this->_perPage;
    }

    /**
     * Get the previous page number.
     *
     * @access public
     * @return integer
     */
    public function getPreviousPage()
    {
        return $this->_currentPage - 1;
    }

    /**
     * Return the querystring parameters as an array.
     *
     * @access public
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->_query;
    }

    /**
     * Get the querystring for a specified page.
     *
     * @access public
     * @param integer|string $page
     * @return string
     */
    public function getQueryString($page = 1)
    {
        $parameters = [
            'page' => $page,
        ];

        if (count($this->_query) > 0) {
            $parameters = array_merge($parameters, $this->_query);
        }

        return '?'.http_build_query($parameters, null, '&');
    }

    /**
     * Get the last item number for the current page.
     *
     * @access public
     * @return integer
     */
    public function getToItem()
    {
        return $this->_to;
    }

    /**
     * Get the total number of items.
     *
     * @access public
     * @return integer
     */
    public function getTotal()
    {
        return $this->_total;
    }

    /**
     * Set items for the pagination.
     *
     * @access public
     * @param array $items
     * @return Paginate
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
        $this->_total = count($items);

        return $this;
    }

    /**
     * Calculate the current page number.
     *
     * @access protected
     * @return integer
     */
    protected function _calcCurrentPage()
    {
        if (is_numeric($this->_requestPage) && $this->_requestPage > $this->_lastPage) {
            return $this->_lastPage > 0 ? $this->_lastPage : 1;
        }

        return $this->_isValidPage($this->_requestPage) ? (int) $this->_requestPage : 1;
    }

    /**
     * Calculate the first and last result for this page.
     *
     * @access protected
     */
    protected function _calcItemRanges()
    {
        $this->_from = $this->_total ? ($this->_currentPage - 1) * $this->_perPage + 1 : 0;
        $this->_to = min($this->_total, $this->_currentPage * $this->_perPage);
    }

    /**
     * Calculate the last page number.
     *
     * @access protected
     * @return integer
     */
    protected function _calcLastPage()
    {
        return ceil($this->_total / $this->_perPage);
    }

    /**
     * Calculate the offset of the items.
     *
     * @access protected
     * @return integer
     */
    protected function _calcItemOffset()
    {
        return $this->_perPage * ($this->_currentPage - 1);
    }

    /**
     * Determine if the page number is a valid page.
     *
     * @access protected
     * @param integer $page
     * @return boolean
     */
    protected function _isValidPage($page)
    {
        return $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false;
    }

}