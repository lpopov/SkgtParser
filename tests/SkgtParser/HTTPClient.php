<?php

namespace Tests\SkgtParser;

use DOMDocument;

class HTTPClient
{
    public function getWebPage($post = array(), $decodeCaptcha = false)
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

        return array(
            'errno'     =>  '',
            'errmsg'    =>  '',
            'content'   =>  $dom->saveXML(),
            'captcha'   =>  array(
                'img'       =>  '',
                'code'      =>  false
            ),
        );
    }
}