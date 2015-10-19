<?php

namespace Tests\SkgtParser;

use SkgtParser\Parser;
use SkgtParser\ByStopParser;
use PHPUnit_Framework_TestCase;
use DOMDocument;

class ByStopParserTest extends PHPUnit_Framework_TestCase
{
    public function testGetFieldsData()
    {
        $domDoc = new DOMDocument();

        $html = $domDoc->createElement("html");
        $body = $domDoc->createElement("body");
    
        $input = $domDoc->createElement("input");
        $input->setAttribute("id", 'ctl00_ContentPlaceHolder1_tbStopCode');
        $input->setAttribute("name", 'input1');
        $input->setAttribute("value", 'value1');

        $body->appendChild($input);
        
        $html->appendChild($body);
        $domDoc->appendChild($html);

        $htmlText = $domDoc->saveXML();
        
        $dom = new DOMDocument();
        $dom->loadHTML($htmlText);
        $data = array();
        
        $byStopParser = new ByStopParser();
        $result = $byStopParser->getFieldsData($dom, $data);
        
        $expectedResult = array(
            'hidden'    =>  array(
                'input1'    =>  'value1',
            ),
            'inputs'    =>  array(
                'input1'    =>  'value1',
            ),
        );
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testShouldRecognizeCaptcha()
    {
        $byStopParser = new ByStopParser();
        
        $ocrCaptcha = true;
        $params = array(
            Parser::STOP_CODE_INPUT =>  1,
            Parser::LINE_SELECT     =>  2
        );
        
        $param = Parser::STOP_CODE_INPUT;
        $result = $byStopParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertTrue($result, 'assert 1');
        
        $param = Parser::LINE_SELECT;
        $result = $byStopParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 2');
        
        $ocrCaptcha = false;
        
        $param = Parser::STOP_CODE_INPUT;
        $result = $byStopParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 3');
        
        $param = Parser::LINE_SELECT;
        $result = $byStopParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertTrue($result, 'assert 4');
        
        $params[Parser::STOP_CODE_INPUT] = '';
        
        $param = Parser::LINE_SELECT;
        $result = $byStopParser->shouldRecognizeCaptcha($ocrCaptcha, $params, $param);
        $this->assertFalse($result, 'assert 5');
        
    }
    
}

