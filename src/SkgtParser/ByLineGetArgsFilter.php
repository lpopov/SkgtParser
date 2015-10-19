<?php

namespace SkgtParser;

use SkgtParser\BaseArgsFilter;

/**
 * Argument filter for the By Line page
 */
class ByLineGetArgsFilter extends BaseArgsFilter
{
    // contains the filter data for the arguments
    protected $args;
    
    /**
     * Sets the filter data for the arguments
     */
    public function __construct() 
    {
        parent::__construct();
        
        $this->args = array(
            'tt'    => array(
                'filter'    => FILTER_VALIDATE_INT,
                'flags'     => FILTER_REQUIRE_SCALAR, 
                'options'   => array('min_range' => 0, 'max_range' => 2)
            ),
            'l'    => array(
                'filter'    => FILTER_VALIDATE_INT,
                'flags'     => FILTER_REQUIRE_SCALAR, 
            ),
            'r'    => array(
                'filter'    => FILTER_VALIDATE_INT,
                'flags'     => FILTER_REQUIRE_SCALAR, 
            ),
            's'    => array(
                'filter'    => FILTER_VALIDATE_INT,
                'flags'     => FILTER_REQUIRE_SCALAR, 
            ),
            'lc'    => array(
                'filter'    => FILTER_CALLBACK,
                'options'   => array($this, 'getLastChangedField'),
            ),
            'c'    => array(
                'filter'    => FILTER_VALIDATE_INT
            ),

        );
    }
    
}

