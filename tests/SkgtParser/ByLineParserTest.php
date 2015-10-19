<?php

namespace Tests\SkgtParser;

use SkgtParser\Parser;
use SkgtParser\ByLineParser;
use PHPUnit_Framework_TestCase;
use DOMDocument;

class ByLineParserTest extends PHPUnit_Framework_TestCase
{
    public function testGetFieldsData()
    {
        $domDoc = new DOMDocument();

        $html = $domDoc->createElement("html");
        $body = $domDoc->createElement("body");
    
        
        
        for($i=0;$i<2;$i++){
            $radio = $domDoc->createElement("input");
            $radio->setAttribute("id", 'ctl00_ContentPlaceHolder1_rblRoute_'.$i);
            $radio->setAttribute("name", 'ctl00$ContentPlaceHolder1$rblRoute');
            $radio->setAttribute("type", 'radio');
            $radio->setAttribute("value", 100+$i);
            
            if($i===0){
                $radio->setAttribute("checked", 'checked');
            }
            
            $label = $domDoc->createElement("label", 'label'.$i);
            
            $body->appendChild($radio);
            $body->appendChild($label);
            
        }
        
        
        $html->appendChild($body);
        $domDoc->appendChild($html);

        $htmlText = $domDoc->saveXML();
        
        $dom = new DOMDocument();
        $dom->loadHTML($htmlText);
        $data = array();
        
        $byLineParser = new ByLineParser();
        $result = $byLineParser->getFieldsData($dom, $data);
        
        $expectedResult = array(
            'hidden'    =>  array(
                Parser::ROUTE_SELECT    =>  '100',
            ),
            'selects'    =>  array(
                Parser::ROUTE_SELECT    =>  array(
                    100 =>  'label0',
                    101 =>  'label1',
                ),
            ),
        );
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testShouldRecognizeCaptcha()
    {
        $byLineParser = new ByLineParser();
        
        $ocrCaptcha = true;
        $params = array(
            Parser::TRANSPORT_TYPE_SELECT   =>  1,
            Parser::LINES_SELECT            =>  2,
            Parser::ROUTE_SELECT            =>  3,
            Parser::STOPS_SELECT            =>  4
        );
        $param = Parser::TRANSPORT_TYPE_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 1');
        
        $param = Parser::LINES_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 2');
        
        $param = Parser::ROUTE_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertTrue($result, 'assert 3');
        
        $param = Parser::STOPS_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 4');
        
        $ocrCaptcha = false;
        
        $param = Parser::TRANSPORT_TYPE_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 5');
        
        $param = Parser::LINES_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 6');
        
        $param = Parser::ROUTE_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 7');
        
        $param = Parser::STOPS_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertTrue($result, 'assert 8');
        
        $params[Parser::ROUTE_SELECT] = '';
        $param = Parser::STOPS_SELECT;
        $result = $byLineParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 9');
    }
    
}

