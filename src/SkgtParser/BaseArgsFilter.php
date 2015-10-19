<?php

namespace SkgtParser;

/**
 * Base funtionality for the argument filters
 */
class BaseArgsFilter
{
    // contains the filter data for the arguments
    protected $args;
    
    /**
     * Sets the filter data for the arguments
     */
    public function __construct() 
    {
        $this->args = array(
            'lc'    => array(
                'filter'    => FILTER_CALLBACK,
                'options'   => array($this, 'getLastChangedField'),
            ),
            'captcha'    => array(
                'filter'    => FILTER_VALIDATE_INT
            ),
        );
    }

    /**
     * Filters $_GET data
     * 
     * @return array filtered values
     */
    public function filter()
    {
        return filter_input_array(INPUT_GET, $this->args, true);
    }
    
    /**
     * Checks if this field is set in the $args array
     * 
     * @param string $field field to be checked
     * @return string Returns the field or empty string if not found
     */
    public function getLastChangedField($field)
    {
        if(in_array($field, array_keys($this->args)) && $field !== 'lc'){
            return $field;
        }
        
        return '';
    }
   
}

