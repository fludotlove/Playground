<?php
/**
 * Copyright 2013 Nathan Marshall
 *
 * @author Nathan Marshall (FDL) <nathan@fludotlove.com>
 * @copyright (c) 2013, Nathan Marshall
 */

namespace FDL;

/**
 * Handles validation of user input.
 *
 * @author Nathan Marshall
 */
class Validator {

    /**
     * Default error messages to use if no custom error message is set.
     *
     * @access protected
     * @var array
     */
    protected $_defaultErrorMessages = [
        'absent' => '(:input) must not contain (:value)',
        'accepted' => '(:input) must be accepted',
        'after' => '(:input) must be a date after (:date)',
        'alpha' => '(:input) must only contain letters',
        'alphanumeric' => '(:input) must only contain letters and numbers',
        'array' => '(:input) must have selected elements',
        'before' => '(:input) must be a date before (:date)',
        'between' => [
            'numeric' => '(:input) must be a value between (:minimum) and (:maximum)',
            'string' => '(:input) should be between (:minimum) and (:maximum) characters long'
        ],
        'confirmed' => '(:input) confirmation does not match',
        'contains' => '(:input) must contain (:value)',
        'count' => '(:input) must have exactly (:count) elements selected',
        'count_between' => '(:input) must have between (:minimum) and (:maximum) elements selected',
        'count_maximum' => '(:input) must have less than (:maximum) elements selected',
        'count_minimum' => '(:input) must have more than (:minimum) elements selected',
        'different' => '(:input) and (:other) must be different',
        'email' => '(:input) must contain a valid e-mail address',
        'exact' => '(:input) must contain the exact value (:value)',
        'future' => '(:input) must be a date in the future',
        'in' => '(:input) contains an invalid value',
        'integer' => '(:input) must be an integer',
        'ip' => '(:input) must be a valid IP address',
        'match' => '(:input) format is invalid',
        'maximum' => [
            'numeric' => '(:input) should be less than (:maximum)',
            'string' => '(:input) should be a maximum of (:maximum) characters long'
        ],
        'minimum' => [
            'numeric' => '(:input) should be more than (:minimum)',
            'string' => '(:input) should be a minimum of (:minimum) characters long'
        ],
        'not_in' => '(:input) contains an invalid value',
        'numeric' => '(:input) should contain a numeric value',
        'past' => '(:input) must be a date in the past',
        'pattern' => '(:input) format is invalid',
        'phone' => '(:input) must be a valid UK landline (including area code) or mobile number',
        'postcode' => '(:input) must contain a valid UK postcode',
        'required' => '(:input) is required',
        'same' => '(:input) and (:other) must be the same',
        'size' => [
            'numeric' => '(:input) should be (:size)',
            'string' => '(:input) should be (:size) characters long'
        ],
        'text' => '(:input) can only contain letters, numbers and basic punctuation',
        'url' => '(:input) is not a valid URL'
    ];

    /**
     * Errors encountered during validation.
     *
     * @access protected
     * @var array
     */
    protected $_errors = [];

    /**
     * Input field values.
     *
     * @access protected
     * @var array
     */
    protected $_inputs;

    /**
     * Custom error messages to use instead of the defaults.
     *
     * @access protected
     * @var array
     */
    protected $_messages;

    /**
     * Validation rules for numeric inputs.
     *
     * @access protected
     * @var array
     */
    protected $_numericRules = ['integer', 'numeric'];

    /**
     * Validation rules to be run against user inputs.
     *
     * @access protected
     * @var array
     */
    protected $_rules;

    /**
     * Validation rules for size rules.
     *
     * @access protected
     * @var array
     */
    protected $_sizeRules = ['size', 'between', 'minimum', 'maximum'];

    /**
     * Creates an instance of the validator.
     *
     * @access public
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     */
    public function __construct(array $inputs, array $rules, array $messages = [])
    {
        foreach($rules as $key => &$rule) {
            $rule = is_string($rule) ? explode('|', $rule) : $rule;
        }

        $this->_inputs = $inputs;
        $this->_messages = $messages;
        $this->_rules = $rules;
    }

    /**
     * Get error messages encountered during validation.
     *
     * @access public
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Determine if the validation passed.
     *
     * @access public
     * @return boolean
     */
    public function valid()
    {
        $this->_errors = [];

        foreach($this->_rules as $input => $rules) {
            foreach($rules as $rule) {
                $this->_checkRule($input, $rule);
            }
        }

        return count($this->_errors) === 0;
    }

    /**
     * Determine if an input is validatable.
     *
     * Checks if the input is required or is implicitly required.
     *
     * @access protected
     * @param string $rule
     * @param string $value
     * @return boolean
     */
    protected function _checkInputIsValidatable($rule, $value)
    {
        return $this->_validateRequired(null, $value) || $this->_implicitlyRequired($rule);
    }

    /**
     * Check if a input and rule passes.
     *
     * @access protected
     * @param string $input
     * @param string $rule
     */
    protected function _checkRule($input, $rule)
    {
        list($rule, $parameters) = $this->_parseRule($rule);

        $value = $this->_inputs[$input];

        if($this->_checkInputIsValidatable($rule, $value) && !call_user_func_array([$this, '_validate'.ucwords($rule)], [$input, $value, $parameters])) {
            $this->_setError($input, $rule, $parameters);
        }
    }

    /**
     * Get an error message to display.
     *
     * @access protected
     * @param type $input
     * @param type $rule
     * @return type
     */
    protected function _getErrorMessage($input, $rule)
    {
        $custom = $input.'|'.$rule;

        if(array_key_exists($custom, $this->_messages)) {
            return $this->_messages[$custom];
        } elseif(array_key_exists($rule, $this->_messages)) {
            return $this->_messages[$rule];
        } elseif(in_array($rule, $this->_sizeRules)) {
            return $this->_getSizeErrorMessage($input, $rule);
        } else {
            return $this->_defaultErrorMessages[$rule];
        }
    }

    /**
     * Get a size error message.
     *
     * @access protected
     * @return string
     */
    protected function _getSizeErrorMessage()
    {
        if($this->_hasRule($input, $this->_numericRules)) {
            $line = 'numeric';
        } else {
            $line = 'string';
        }

        return $this->_defaultErrorMessages[$rule][$line];
    }

    /**
     * Determine if and input has a validation rule.
     *
     * @access protected
     * @param string $input
     * @param string $rules
     * @return boolean
     */
    protected function _hasRule($input, $rules)
    {
        foreach($this->_rules[$input] as $rule) {
            list($rule, $parameters) = $this->_parseRule($rule);

            if(in_array($rule, $rules)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if an input is implicitly required.
     *
     * @access protected
     * @param string $rule
     * @return boolean
     */
    protected function _implicitlyRequired($rule)
    {
        return $rule == 'required' || $rule == 'accepted';
    }

    /**
     * Parse a rule and parameters from a rule string.
     *
     * @access protected
     * @param string $rule
     * @return array
     */
    protected function _parseRule($rule)
    {
        $parameters = [];

        if(($colon = strpos($rule, ':')) !== false) {
            $parameters = str_getcsv(substr($rule, $colon + 1));
        }

        return [is_numeric($colon) ? substr($rule, 0, $colon) : $rule, $parameters];
    }

    /**
     * Replace placeholders for absent rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceAbsent($message, $input, $rule, $parameters)
    {
        return str_replace('(:value)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for after rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceAfter($message, $input, $rule, $parameters)
    {
        return str_replace('(:date)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for before rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceBefore($message, $input, $rule, $parameters)
    {
        return str_replace('(:date)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for between rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceBetween($message, $input, $rule, $parameters)
    {
        return str_replace(Array('(:minimum)', '(:maximum)'), $parameters, $message);
    }

    /**
     * Replace placeholders for contains rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceContains($message, $input, $rule, $parameters)
    {
        return str_replace('(:value)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for count rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceCount($message, $input, $rule, $parameters)
    {
        return str_replace('(:count)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for count between rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceCountBetween($message, $input, $rule, $parameters)
    {
        return str_replace(array('(:minimum)', '(:maximum)'), $parameters, $message);
    }

    /**
     * Replace placeholders for count maximum rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceCountMaximum($message, $input, $rule, $parameters)
    {
        return str_replace('(:maximum)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for count minimum rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceCountMinimum($message, $input, $rule, $parameters)
    {
        return str_replace('(:minimum)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for different rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceDifferent($message, $input, $rule, $parameters)
    {
        return str_replace('(:other)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for exact rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceExact($message, $input, $rule, $parameters)
    {
        return str_replace('(:value)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for in rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceIn($message, $input, $rule, $parameters)
    {
        return str_replace('(:values)', implode(', ', $parameters), $message);
    }

    /**
     * Replace placeholders for maximum rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceMaximum($message, $input, $rule, $parameters)
    {
        return str_replace('(:maximum)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for minimum rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceMinimum($message, $input, $rule, $parameters)
    {
        return str_replace('(:minimum)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for not in rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceNotIn($message, $input, $rule, $parameters)
    {
        return str_replace('(:values)', implode(', ', $parameters), $message);
    }

    /**
     * Replace input placeholder and invokes a custom replacement rule if it exists.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replacePlaceholdersInErrorMessage($message, $input, $rule, $parameters)
    {
        $message = str_replace('(:input)', ucwords($input), $message);

        if(method_exists(__CLASS__, $replacer = '_replace'.ucwords($rule))) {
            $message = call_user_func_array([__CLASS__, $replacer], [$message, ucwords($input), $rule, $parameters]);
        }

        return $message;
    }

    /**
     * Replace placeholders for same rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceSame($message, $input, $rule, $parameters)
    {
        return str_replace('(:other)', $parameters[0], $message);
    }

    /**
     * Replace placeholders for size rule.
     *
     * @access protected
     * @param string $message
     * @param string $input
     * @param string $rule
     * @param array $parameters
     * @return string
     */
    protected function _replaceSize($message, $input, $rule, $parameters)
    {
        return str_replace('(:size)', $parameters[0], $message);
    }

    /**
     * Sets an error message and replaces placeholders.
     *
     * @access protected
     * @param string $input
     * @param string $rule
     * @param array $parameters
     */
    protected function _setError($input, $rule, $parameters)
    {
        $this->_errors[$input] = $this->_replacePlaceholdersInErrorMessage($this->_getErrorMessage($input, $rule), $input, $rule, $parameters);
    }

    /**
     * Gets the size of the input.
     *
     * Uses the actual numeric value if numeric or is a numeric rule, otherwise
     * uses the length of the value.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return mixed
     */
    protected function _size($input, $value)
    {
        if(is_numeric($value) && $this->_hasRule($input, $this->_numericRules)) {
            return $this->_inputs[$input];
        } else {
            return strlen(trim($value));
        }
    }

    /**
     * Performs a pattern match on a string.
     *
     * @access protected
     * @param string $pattern
     * @param string $value
     * @return boolean
     */
    protected function _stringMatch($pattern, $value)
    {
        $pattern = ($pattern !== '/' ? str_replace('*', '(.*)', $pattern).'\z' : '^/$');

        return preg_match('#'.str_replace('#', '\#', $pattern).'#', $value);
    }

    /**
     * Performs the absent validation rule.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateAbsent($input, $value, $parameters)
    {
        return !$this->_stringMatch('*'.$parameters[0].'*', $value);
    }

    /**
     * Determines if a value is accepted.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateAccepted($input, $value)
    {
        return $this->_validateRequired($input, $value) && ($value == 'yes' || $value == '1');
    }

    /**
     * Determines if the value is after a specified date.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateAfter($input, $value, $parameters)
    {
        return strtotime($value) > strtotime($parameters[0]);
    }

    /**
     * Determines if the value contains alpha characters only.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateAlpha($input, $value)
    {
        return preg_match('#^([a-z])+$#i', $value);
    }

    /**
     * Determines if the value contains alphanumeric characters only.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateAlphanumeric($input, $value)
    {
        return preg_match('#^([a-z0-9])+$#i', $value);
    }

    /**
     * Determines if the value is an array.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateArray($input, $value)
    {
        return is_array($value);
    }

    /**
     * Determines if the value is before a specified date.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateBefore($input, $value, $parameters)
    {
        return strtotime($value) < strtotime($parameters[0]);
    }

    /**
     * Determines if the value is between 2 values.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateBetween($input, $value, $parameters)
    {
        $size = $this->_size($input, $value);

        return $size >= $parameters[0] && $size <= $parameters[1];
    }

    /**
     * Determines if the value is confirmed.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateConfirmed($input, $value)
    {
        return $this->_validateSame($input, $value, [$input.'_confirmation']);
    }

    /**
     * Determines if the value contains a string.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateContains($input, $value, $parameters)
    {
        return $this->_stringMatch('*'.$parameters[0].'*', $value);
    }

    /**
     * Determines if the value is an array and has the specified number of elements.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateCount($input, $value, $parameters)
    {
        return is_array($value) && count($value) == $parameters[0];
    }

    /**
     * Determines if the value is an array and has a number of elements between 2 values.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateCountBetween($input, $value, $parameters)
    {
        return is_array($value) && count($value) >= $parameters[0] && count($value) <= $parameters[1];
    }

    /**
     * Determines if the value is an array and contains less elements than specified.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateCountMaximum($input, $value, $parameters)
    {
        return is_array($value) && count($value) <= $parameters[0];
    }

    /**
     * Determines if the value is an array and contains more elements than specified.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateCountMinimum($input, $value, $parameters)
    {
        return is_array($value) && count($value) >= $parameters[0];
    }

    /**
     * Determines if the value is different to another input value.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateDifferent($input, $value, $parameters)
    {
        return isset($this->_inputs[$parameters[0]]) && $value != $this->_inputs[$parameters[0]];
    }

    /**
     * Determines if the value contains a valid e-mail address.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateEmail($input, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Determines if the value contains an exact string.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateExact($input, $value, $parameters)
    {
        return $value === $parameters[0];
    }

    /**
     * Determines if the value is in the future.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateFuture($input, $value)
    {
        return strtotime($value) > time();
    }

    /**
     * Determines if the value is in an array of allowed values.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateIn($input, $value, $parameters)
    {
        return in_array($value, $parameters);
    }

    /**
     * Determines if the value is an integer.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateInteger($input, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Determines if the value contains an IP address.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateIp($input, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Determines if the value matches a given regular expression.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateMatch($input, $value, $parameters)
    {
        return preg_match($parameters[0], $value);
    }

    /**
     * Determines if the value is less than a specified maximum.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateMaximum($input, $value, $parameters)
    {
        return $this->_size($input, $value) <= $parameters[0];
    }

    /**
     * Determines if the value is more than a specified minimum.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateMinimum($input, $value, $parameters)
    {
       return static::_size($input, $value) >= $parameters[0];
    }

    /**
     * Determines if the value is not in an array of disallowed values.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateNotIn($input, $value, $parameters)
    {
        return !in_array($value, $parameters);
    }

    /**
     * Determines if the value is numeric.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateNumeric($input, $value)
    {
        return is_numeric($value);
    }

    /**
     * Determines if the value is in the past.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validatePast($input, $value)
    {
        return strtotime($value) < time();
    }

    /**
     * Determines if the value matches a given regular expression.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validatePattern($input, $value, $parameters)
    {
        return preg_match($parameters[0], $value);
    }

    /**
     * Determines if the value is a valid phone number.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validatePhone($input, $value)
    {
        $value = preg_replace('#\s+|-#', '', $value);

        return preg_match('#^(?:(?:(?:00\s?|\+)44\s?|0)(?:1\d{8,9}|[23]\d{9}|7(?:[45789]\d{8}|624\d{6})))$#', $value);
    }

    /**
     * Determines if the value is a valid UK postcode.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validatePostcode($input, $value)
    {
        $patterns = [
            '([^QZ][^IJZ]{0,1}\d{1,2})(\d[^CIKMOV]{2})',
            '([^QV]\d[ABCDEFGHJKSTUW])(\d[^CIKMOV]{2})',
            '([^QV][^IJZ]\d[ABEHMNPRVWXY])(\d[^CIKMOV]{2})',
            '(GIR)(0AA)',
            '(BFPO)(\d{1,4})',
            '(BFPO)(C\/O\d{1,3})'
        ];

        return preg_match('#^('.implode('|', $patterns).')$#', strtoupper(str_replace(' ', '', $value)));
    }

    /**
     * Determines if the value exists.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateRequired($input, $value)
    {
	if(is_null($value)) {
            return false;
        } elseif(is_string($value) && trim($value) === '') {
            return false;
        }

        return true;
    }

    /**
     * Determines if the value is the same as another input (useful for password fields).
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateSame($input, $value, $parameters)
    {
        $other = $parameters[0];

        return isset($this->_inputs[$other]) && $value == $this->_inputs[$other];
    }

    /**
     * Determines if the value is a given size.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @param array $parameters
     * @return boolean
     */
    protected function _validateSize($input, $value, $parameters)
    {
        return $this->_size($input, $value) == $parameters[0];
    }

    /**
     * Determines if the value only contains text and a given list of punctuation.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateText($input, $value)
    {
        return preg_match('#^[a-z0-9\-.,()£$¥€*%\#@\?\'"\s]+$#i', $value);
    }

    /**
     * Determines if the value is a valid URL.
     *
     * @access protected
     * @param string $input
     * @param string $value
     * @return boolean
     */
    protected function _validateUrl($input, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

}