<?php

class Input
{

    protected $errors = [];

    protected $messages = [
        'isBetween' => '(:name) must be less than (:min) and greater than (:max)',
        'isInteger' => '(:name) must be an integer',
        'isNumeric' => '(:name) must be numeric',

        'notInteger' => '(:name) must not be an integer',
        'notNumeric' => '(:name) must not be numeric'
    ];

    protected $name;

    protected $value;

    public function __construct($id, $name, $value)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isBetween($min, $max, $message = null)
    {
        if ($this->value < $min || $this->value > $max) {
            $this->setError('isBetween', $message === null ? $this->messages['isBetween'] : $message, [$min, $max]);
        }

        return $this;
    }

    public function isInteger($message = null)
    {
        if ($this->checkInteger() === false) {
            $this->setError('isInteger', $message === null ? $this->messages['isInteger'] : $message);
        }

        return $this;
    }

    public function isNumeric($message = null)
    {
        if ($this->checkNumeric() === false) {
            $this->setError('isNumeric', $message === null ? $this->messages['isNumeric'] : $message);
        }

        return $this;
    }

    public function notInteger($message = null)
    {
        if ($this->checkInteger() === true) {
            $this->setError('notInteger', $message === null ? $this->messages['notInteger'] : $message);
        }

        return $this;
    }

    public function notNumeric($message = null)
    {
        if ($this->checkNumeric() === true) {
            $this->setError('notNumeric', $message === null ? $this->messages['notNumeric'] : $message);
        }

        return $this;
    }

    protected function checkInteger()
    {
        return filter_var($this->value, FILTER_VALIDATE_INT);
    }

    protected function checkNumeric()
    {
        return is_numeric($this->value);
    }

    protected function formatErrorIsBetween($message, $rule, $parameters)
    {
        return str_replace(array('(:min)', '(:max)'), $parameters, $message);
    }

    protected function setError($rule, $message, $parameters = [])
    {
        $message = str_replace('(:name)', $this->name, $message);

        if (method_exists($this, $method = 'formatError' . ucwords($rule))) {
            $message = call_user_func_array([$this, $method], [$message, $rule, $parameters]);
        }

        $this->errors[] = $message;
    }

}

$validator = new Input('number', 'Age', 25);

$validator->isInteger()->isBetween(30, 100);

print_r($validator->getErrors());