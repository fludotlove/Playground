<?php
/**
 * Copyright 2013 Nathan Marshall
 *
 * @author Nathan Marshall (FDL) <nathan@fludotlove.com>
 * @copyright (c) 2013, Nathan Marshall
 */

namespace FDL;

ini_set('display_errors', true);
error_reporting(-1);

date_default_timezone_set('Europe/London');

require 'Validation.php';

/**
 * Handles validation of user input.
 *
 * Extends the Validator class.
 *
 * @author Nathan Marshall
 */
class CustomValidator extends Validator {

    /**
     * Creates a custom validator instance.
     *
     * @access public
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     */
    public function __construct(array $inputs, array $rules, array $messages = [])
    {
        parent::__construct($inputs, $rules, $messages);

        $this->_defaultErrorMessages['custom'] = '(:input) must be foobar';
    }

    /**
     * Determines if the value of the input is 'foobar'.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateCustom($input, $value)
    {
        return $value === 'foobar';
    }

}

$inputs = [
    'name' => 'Nathan Marshall',
    'nickname' => 'FDL',
    'age' => '25',
    'location' => 'Manchester',
    'password' => 'foo123',
    'password_confirmation' => 'bar321',
    'url' => 'http://fludotlove.com',
    'description' => '',

];

$rules = [
    'name' => 'required|custom',
    'nickname' => 'alphanumeric',
    'age' => 'required|numeric|minimum:30',
    'location' => 'required|alphanumeric',
    'password' => 'confirmed',
    'url' => 'url',
    'description' => 'required'
];

$validation = new CustomValidator($inputs, $rules, [
    'name|custom' => 'Name must be foobar!',
    'age|minimum' => 'Sorry, you need to be atleast 30'
]);

if($validation->valid()) {
    // Woooo!;
} else {
    $errors = $validation->getErrors();

    echo '<pre>'.print_r($errors, true).'</pre>';
}