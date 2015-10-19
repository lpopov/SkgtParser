<?php
namespace SkgtParser;


use DOMDocument;

use SkgtParser\ByLineParser;
use SkgtParser\ByStopParser;
use SkgtParser\TesseractOCR as OCR;
use SkgtParser\CurlHTTPClient as HTTPClient;
use SkgtParser\BaseArgsFilter;

class Parser
{
    const BY_LINE                   =   1;
    const BY_STOP                   =   2;
    
    const OCR_CAPTCHA               =   true;
    
    const TRANSPORT_TYPE_SELECT     =   'ctl00$ContentPlaceHolder1$ddlTransportType';
    const LINES_SELECT              =   'ctl00$ContentPlaceHolder1$ddlLines';
    const ROUTE_SELECT              =   'ctl00$ContentPlaceHolder1$rblRoute';
    const STOPS_SELECT              =   'ctl00$ContentPlaceHolder1$ddlStops';
    
    const STOP_CODE_INPUT           =   'ctl00$ContentPlaceHolder1$tbStopCode';
    const LINE_SELECT               =   'ctl00$ContentPlaceHolder1$ddlLine';
    
    const CAPTCHA_INPUT             =   'ctl00$ContentPlaceHolder1$CaptchaInput';
    
    const TIMES_PLACEHOLDER         =   'ctl00_ContentPlaceHolder1_gvTimes';
    const MAP_PLACEHOLDER           =   'ctl00_ContentPlaceHolder1_imgMap';
    
    // Parameter names for the form
    protected $paramsShort = array(
        Parser::TRANSPORT_TYPE_SELECT   =>  'tt',
        Parser::LINES_SELECT            =>  'l',
        Parser::ROUTE_SELECT            =>  'r',
        Parser::STOPS_SELECT            =>  's',
        Parser::STOP_CODE_INPUT         =>  'sc',
        Parser::LINE_SELECT             =>  'l',
        Parser::CAPTCHA_INPUT           =>  'c'
    );
    
    // Parameter labels
    protected $paramsNames = array(
        Parser::TRANSPORT_TYPE_SELECT   =>  'Транспорт',
        Parser::LINES_SELECT            =>  'Линия',
        Parser::ROUTE_SELECT            =>  'Маршрут',
        Parser::STOPS_SELECT            =>  'Спирка',
        Parser::STOP_CODE_INPUT         =>  'Спирка',
        Parser::LINE_SELECT             =>  'Линия',
        Parser::CAPTCHA_INPUT           =>  'Код'
    );

    
    private $type;
    private $params;
    private $data;
    private $times;
    private $ocrCaptcha;
    private $ocrError;
    
    private $parser;
    private $ocr;
    private $httpClient;
    
    /**
     * Class contructor
     * 
     * @param int $type Sets if the By Line or By Stop parser will be used
     * @param type $ocrCaptcha Sets if OCR should be used on the captcha
     */
    public function __construct($type = Parser::BY_LINE, $ocrCaptcha = false)
    {
        $this->type = $type;
        switch($this->type){
            case Parser::BY_LINE:
            default:
                $this->parser = new ByLineParser();
                break;
            
            case Parser::BY_STOP:
                $this->parser = new ByStopParser();
                break;
        }
        
        $this->params = $this->parser->getParams();
        
        $this->ocrCaptcha = $ocrCaptcha;
        
        if($this->ocrCaptcha){
            $this->ocr = new OCR();
            if(!$this->ocr->ocrWorks()){
                $this->ocrCaptcha = false;
                $this->ocrError = 'OCR not found!';
            }
        }
        
        $this->setHttpClient(new HTTPClient($this->parser->getUrl(), $this->ocrCaptcha, $this->ocr));
    }
    
    /**
     * Returns values for the select and input fields
     * 
     * @param DOMDocument $dom
     * @param array $data Data array with initial values
     * @return array $data array filled in
     */
    private function getFieldsData(DOMDocument $dom, array $data)
    {
        
        foreach ($dom->getElementsByTagName('input') as $attrName => $attrNode) {
            if($attrNode->getAttribute('type') == 'hidden'){
                $name = $attrNode->getAttribute('name');
                $value = $attrNode->getAttribute('value');
                $data['hidden'][$name] = $value;
            }
        }

        foreach ($dom->getElementsByTagName('select') as $attrNode) {
            $name = $attrNode->getAttribute('name');
            $data['selects'][$name] = array();
            $code = '';
            foreach ($attrNode->childNodes as $atName => $atNode) {
                if($atNode->getAttribute('selected')=='selected'){
                    $code = $atNode->getAttribute('value');
                }
                $data['selects'][$name][$atNode->getAttribute('value')] = utf8_decode($atNode->nodeValue);
            }

            $data['hidden'][$name] = $code;
        }
        
        return $data;
    }
    
    /**
     * Parses the page for the needed field values
     * 
     * @param DOMDocument $dom Web page DOMDocument
     * @return type
     */
    private function getData(DOMDocument $dom)
    {
        $data = $this->parser->getInitData();
        
        $data = $this->getFieldsData($dom, $data);
        
        $data = $this->parser->getFieldsData($dom, $data);

        return $data;
    }

    /**
     * Parses the arrival times into an array
     * 
     * @param DOMDocument $dom Web page DOMDocument
     * @return array
     */
    private function _getTimesData(DOMDocument $dom)
    {
        
        $timesArr = array();
        
        $table = $dom->getElementById(Parser::TIMES_PLACEHOLDER);
        
        if(empty($table)){
            return $timesArr;
        }
        
        $i=0;
        foreach($table->childNodes as $node){
            
            if($i++ == 0){
                continue;
            }
            $timesArr[] = $this->_parseTimes($node->nodeValue);
            
        }
        
        return $timesArr;
    }
    
    /**
     * Parses simgle text line containing arrival time to array
     * 
     * @param string $textLine Arrival time string
     * @return array Arrival time array
     */
    private function _parseTimes($textLine)
    {
        $tr_data = explode(" ", utf8_decode(trim($textLine)));

        $timeBus = strtotime($tr_data[0]);
        $timeCalculated = strtotime($tr_data[3]);

        if($timeCalculated > $timeBus){
            $timeBus = strtotime("+1 day", $timeBus);
        }

        $time = ($timeBus-$timeCalculated)/60;
        
        return array(
            'time_calculated'   =>  date('H:i', $timeCalculated),
            'minutes'           =>  $time,
            'time'              =>  date('H:i',$timeBus)
        );
    }
    
    /**
     * Runs the parser
     */
    public function run()
    {
        $dom = new DOMDocument();
        
        if(!empty($this->params[Parser::CAPTCHA_INPUT]) && !empty($_SESSION['data'])){
            $data = $_SESSION['data'];
            unset($_SESSION['data']);
            
            foreach($this->params as $param => $value){
                $data['hidden'][$param] = $value;
            }
            
            $page = $this->httpClient->getWebPage($data['hidden'], true);
            $dom->loadHTML($page['content']);
            $captcha = $page['captcha'];
            $_SESSION['data'] = $data;
            $data['captchaImg'] = $captcha['img'];
            
        }else{
            $data = $this->getParametersValues($dom);
        }

        $this->data = $data;
        $this->times = $this->_getTimesData($dom);
        
    }
    
    /**
     * Returns the parameters values
     * 
     * @param DOMDocument $dom Web page DOMDocument
     * @return array
     */
    protected function getParametersValues(DOMDocument $dom)
    {
        $page = $this->httpClient->getWebPage();
        $dom->loadHTML($page['content']);
        $captcha = array('img' => '', 'code' => '');

        foreach($this->params as $param => $value){
            if($value === "") { break; }

            $data = $this->getData($dom);
            $data['captchaImg'] = '';

            $data['hidden'][$param] = $value;
            $data['hidden'][Parser::CAPTCHA_INPUT] = $captcha['code'];

            $recognizeCaptcha = $this->parser->shouldRecognizeCaptcha($this->ocrCaptcha, $this->params, $param);

            $page = $this->httpClient->getWebPage($data['hidden'], $recognizeCaptcha);
            $dom->loadHTML($page['content']);
            $captcha = $page['captcha'];

            if($recognizeCaptcha && $captcha['code'] === false){
                $_SESSION['data'] = $data;
                $data['captchaImg'] = $captcha['img'];
                break;
            }
        }
        
        return $data;
    }
    /**
     * Returns the arrival times array
     * 
     * @return array
     */
    public function getTimes()
    {
        return $this->times;
    }
    
    /**
     * Sets parameter values 
     * 
     * @param BaseArgsFilter $argsFilter
     */
    public function setParams(BaseArgsFilter $argsFilter)
    {
        $params = $argsFilter->filter();
        
        foreach($this->params as $key => $value){
            $this->params[$key] = $params[$this->paramsShort[$key]];
            if($this->paramsShort[$key] === $params['lc']){
                break;
            }
        }
        if(!empty($params[$this->paramsShort[Parser::CAPTCHA_INPUT]])){
            $this->params[Parser::CAPTCHA_INPUT] = $params[$this->paramsShort[Parser::CAPTCHA_INPUT]];
        }
    }
    
    /**
     * Returns selects data
     * 
     * @return array
     */
    public function getSelectsData()
    {
        return $this->data['selects'];
    }
    
    /**
     * Returns the captcha image if not recognized by OCR
     * 
     * @return string
     */
    public function getCaptchaImg()
    {
        return $this->data['captchaImg'];
    }
    
    /**
     * Return the parameter values
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Returns form field names
     * 
     * @return array
     */
    public function getParamsShort()
    {
        return $this->paramsShort;
    }
    
    /**
     * Returns form field labels
     * 
     * @return array
     */
    public function getParamsNames()
    {
        return $this->paramsNames;
    }
    
    /**
     * Returns parser type
     * 
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Return if OCR should be used on the captcha
     * 
     * @return boolean
     */
    public function getOcrCaptcha()
    {
        return $this->ocrCaptcha;
    }
    
    /**
     * Sets the HTTP Client
     */
    protected function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }
}

