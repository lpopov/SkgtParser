<?php
namespace SkgtParser;

use SkgtParser\Parser;
use SkgtParser\Interfaces\ParserInterface;

use DOMDocument;

/**
 * Specific parser functionality for the By Stop page
 */
class ByStopParser implements ParserInterface
{
    // url to the By Stop page
    protected $url = 'http://gps.skgt-bg.com/Web/SelectByStop.aspx';
    
    // parameters that would be parsed
    protected $params = array(
        Parser::STOP_CODE_INPUT         =>  false,
        Parser::LINE_SELECT             =>  false
    );
    
    /**
     * Returns the url that will be parsed
     * 
     * @return string web page url
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Returns the parameters that would be used and their default values
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Returns initial values for the fields that would contain the parsed data
     */
    public function getInitData()
    {
        return array(
            'selects'   =>  array(
                Parser::STOP_CODE_INPUT =>  array(),
                Parser::LINE_SELECT     =>  array()
            ),
            'hidden'    =>  array(
                'ctl00$ContentPlaceHolder1$btnSearchLine.x' => 0,
                'ctl00$ContentPlaceHolder1$btnSearchLine.y' => 0
            ),
            'inputs'    =>  array(),
        );
    }
    
    /**
     * Parses specific values for this parser
     * 
     * @param DOMDocument $dom
     * @param array $data Array with the general fields values
     * @return array $data array plus the specific values
     */
    public function getFieldsData(DOMDocument $dom, array $data)
    {
        $attrNode = $dom->getElementById('ctl00_ContentPlaceHolder1_tbStopCode');
        $name = $attrNode->getAttribute('name');
        $value = $attrNode->getAttribute('value');
        $data['inputs'][$name] = $value;
        $data['hidden'][$name] = $value;

        return $data;
    }
    
    /**
     * Should captcha be parsed/shown on this parameter
     * 
     * @param boolean $ocrCaptcha Is OCR enabled
     * @param array $params Current parameter values
     * @param type $param Current parameter that is checked
     * @return boolean
     */
    public function shouldRecognizeCaptcha($ocrCaptcha, $params, $param)
    {
        if($ocrCaptcha && $param == Parser::STOP_CODE_INPUT){
            return true;
        }else if(!$ocrCaptcha && $param == Parser::LINE_SELECT 
                && !empty($params[Parser::STOP_CODE_INPUT])){
            return true;
        }
        
        return false;
    }
}



