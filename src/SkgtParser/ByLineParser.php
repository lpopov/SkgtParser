<?php
namespace SkgtParser;

use SkgtParser\Parser;
use SkgtParser\Interfaces\ParserInterface;

use DOMDocument;

/**
 * Specific parser functionality for the By Line page
 */
class ByLineParser implements ParserInterface
{
    // url to the By Line page
    protected $url = 'http://gps.skgt-bg.com/Web/SelectByLine.aspx';
    
    // parameters that would be parsed
    protected $params = array(
        Parser::TRANSPORT_TYPE_SELECT   =>  false,
        Parser::LINES_SELECT            =>  false,
        Parser::ROUTE_SELECT            =>  false,
        Parser::STOPS_SELECT            =>  false
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
                Parser::TRANSPORT_TYPE_SELECT   =>  array(),
                Parser::LINES_SELECT            =>  array(),
                Parser::ROUTE_SELECT            =>  array('' => '-- Избери --'),
                Parser::STOPS_SELECT            =>  array()
            ),
            'hidden'    =>  array(),
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
        for($i=0;$i<2;$i++){
            $radio = $dom->getElementById('ctl00_ContentPlaceHolder1_rblRoute_'.$i);

            if(empty($radio)){
                continue;
            }

            $name = $radio->getAttribute('name');
            $value = $radio->getAttribute('value');
            if($radio->getAttribute('checked') == 'checked'){
                $data['hidden'][$name]=$value;
            }
            $j=0;
            $desc = '';
            foreach ($dom->getElementsByTagName('label') as $attrNode) {
                if($j++ === $i){
                    $desc = utf8_decode($attrNode->nodeValue);
                    break;
                }
            }
            $data['selects'][$name][$value] = $desc;
        }
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
        if($ocrCaptcha && $param == Parser::ROUTE_SELECT){
            return true;
        }else if(!$ocrCaptcha && $param == Parser::STOPS_SELECT 
                && !empty($params[Parser::ROUTE_SELECT])){
            return true;
        }
        
        return false;
    }
}



