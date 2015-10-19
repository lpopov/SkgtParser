<?php

namespace SkgtParser\Interfaces;

use DOMDocument;

/**
 * Interface for web page parsers
 */
interface ParserInterface
{
    /**
     * Returns the url that will be parsed
     * 
     * @return string web page url
     */
    public function getUrl();
    
    /**
     * Returns the parameters that would be used and their default values
     * 
     * @return array
     */
    public function getParams();
    
    /**
     * Returns initial values for the fields that would contain the parsed data
     */
    public function getInitData();
    
    /**
     * Parses specific values for this parser
     * 
     * @param DOMDocument $dom
     * @param array $data Array with the general fields values
     * @return array $data array plus the specific values
     */
    public function getFieldsData(DOMDocument $dom, array $data);
    
    /**
     * Should captcha be parsed/shown on this parameter
     * 
     * @param boolean $ocrCaptcha Is OCR enabled
     * @param array $params Current parameter values
     * @param type $param Current parameter that is checked
     * @return boolean
     */
    public function shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
}
