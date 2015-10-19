<?php

namespace Tests\SkgtParser;

use SkgtParser\Parser;
use Tests\SkgtParser\HTTPClient;
use PHPUnit_Framework_TestCase;
use DOMDocument;
use ReflectionClass;

require_once 'HTTPClient.php';

class ParserTest extends PHPUnit_Framework_TestCase
{
    public function testGetFieldsData()
    {
        $skgtParser = new Parser();
        $this->assertEquals(Parser::BY_LINE, $skgtParser->getType());
        $this->assertEquals(false, $skgtParser->getOcrCaptcha());
        unset($skgtParser);
        
        $skgtParser = new Parser(Parser::BY_LINE, Parser::OCR_CAPTCHA);
        $this->assertEquals(Parser::BY_LINE, $skgtParser->getType());
        $this->assertEquals(true, $skgtParser->getOcrCaptcha());
        unset($skgtParser);
        
        $skgtParser = new Parser(Parser::BY_STOP, Parser::OCR_CAPTCHA);
        $this->assertEquals(Parser::BY_STOP, $skgtParser->getType());
        $this->assertEquals(true, $skgtParser->getOcrCaptcha());
        unset($skgtParser);
    }
    
    public function testGetFieldData()
    {
        $dom = new DOMDocument();

        $html = $dom->createElement("html");
        $body = $dom->createElement("body");
        
        $input = $dom->createElement("input");
        $input->setAttribute("name", "input_name");
        $input->setAttribute("value", "input_value");
        $input->setAttribute("type", "hidden");
        
        $select = $dom->createElement("select");
        $select->setAttribute("name", "select_name");
        
        $option = $dom->createElement("option", "field1");
        $option->setAttribute("value", "option_value1");
        $option->setAttribute("selected", "selected");
        $select->appendChild($option);
        
        $option2 = $dom->createElement("option", "field2");
        $option2->setAttribute("value", "option_value2");
        $select->appendChild($option2);

        $body->appendChild($input);
        $body->appendChild($select);
        $html->appendChild($body);
        $dom->appendChild($html);

//        print $dom->saveXML();
        
        $class = new ReflectionClass('SkgtParser\\Parser');
        $method = $class->getMethod('getFieldsData');
        $method->setAccessible(true);
        $skgtParser = new Parser();
        
        $data = array();
        
        $result = $method->invokeArgs($skgtParser, array($dom, $data));
        
        $expectedResult = array(
            'hidden'    =>  array(
                'input_name'    =>  'input_value',
                'select_name'   =>  'option_value1',
            ),
            'selects'    =>  array(
                'select_name'   =>  array(
                    'option_value1' =>  'field1',
                    'option_value2' =>  'field2',
                ),
            ),
        );
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testParseTimes()
    {
        $class = new ReflectionClass('SkgtParser\\Parser');
        $method = $class->getMethod('_parseTimes');
        $method->setAccessible(true);
        $skgtParser = new Parser();
        
        $textLine = "15:04 изчислено в: 14:55 19.10.2015";
        
        $result = $method->invokeArgs($skgtParser, array($textLine));
        
        $expectedResult = array(
            'time_calculated'   =>  '14:55',
            'minutes'           =>  '9',
            'time'              =>  '15:04',
        );
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testGetTimesDataWithData()
    {
        $domDoc = new DOMDocument();

        $html = $domDoc->createElement("html");
        $body = $domDoc->createElement("body");
        
        $table = $domDoc->createElement("table");
        $table->setAttribute("id",Parser::TIMES_PLACEHOLDER);
        $tr = $domDoc->createElement("tr");
        
        $th = $domDoc->createElement("th", 'should be passed by');
        $tr->appendChild($th);
        
        $th = $domDoc->createElement("th", 'should be passed by2');
        $tr->appendChild($th);
        
        $table->appendChild($tr);
        
        
        $tr = $domDoc->createElement("tr");
        
        $td = $domDoc->createElement("td");
        $img = $domDoc->createElement("img");
        $td->appendChild($img);
        $tr->appendChild($td);
        
        $td = $domDoc->createElement("td", "15:04 изчислено в: 14:55 19.10.2015");
        $tr->appendChild($td);
        
        $table->appendChild($tr);
        
        $body->appendChild($table);
        $html->appendChild($body);
        $domDoc->appendChild($html);

        $htmlText = $domDoc->saveXML();
        
        $dom = new DOMDocument();
        $dom->loadHTML($htmlText);
        
        $class = new ReflectionClass('SkgtParser\\Parser');
        $method = $class->getMethod('_getTimesData');
        $method->setAccessible(true);
        $skgtParser = new Parser();
        
        $result = $method->invokeArgs($skgtParser, array($dom));
        
        $expectedResult = array();
        $expectedResult[] = array(
            'time_calculated'   =>  '14:55',
            'minutes'           =>  '9',
            'time'              =>  '15:04',
        );
        
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testGetTimesDataWithoutData()
    {
        $domDoc = new DOMDocument();

        $html = $domDoc->createElement("html");
        $body = $domDoc->createElement("body");
        
        $html->appendChild($body);
        $domDoc->appendChild($html);

        $htmlText = $domDoc->saveXML();
        
        $dom = new DOMDocument();
        $dom->loadHTML($htmlText);
        
        $class = new ReflectionClass('SkgtParser\\Parser');
        $method = $class->getMethod('_getTimesData');
        $method->setAccessible(true);
        $skgtParser = new Parser();
        
        $result = $method->invokeArgs($skgtParser, array($dom));
        
        $expectedResult = array();
        
        $this->assertEquals($expectedResult, $result);
    }

    
}

