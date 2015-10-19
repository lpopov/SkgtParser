<?php

namespace SkgtParser;

use SkgtParser\BaseArgsFilter;

/**
 * Argument filter for the By Stop page
 */
class ByStopGetArgsFilter extends BaseArgsFilter
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
        
            'l'    => array(
                'filter'    => FILTER_VALIDATE_INT,
                'flags'     => FILTER_REQUIRE_SCALAR, 
            ),
            'sc'    => array(
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

