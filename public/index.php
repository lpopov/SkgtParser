<?php
session_start();

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('SkgtParser', __DIR__.'/../src/');

use SkgtParser\Parser;
use SkgtParser\ByLineGetArgsFilter;
use SkgtParser\ByStopGetArgsFilter;

$templates = new \League\Plates\Engine(__DIR__.'/../src/SkgtParser/Templates/');


if(isset($_GET['bystop'])){
    $skgtParser = new Parser(Parser::BY_STOP);//, Parser::OCR_CAPTCHA);
    $skgtParser->setParams(new ByStopGetArgsFilter());
    $template = 'by_stop';
}else{
    $skgtParser = new Parser(Parser::BY_LINE);//, Parser::OCR_CAPTCHA);
    $skgtParser->setParams(new ByLineGetArgsFilter());
    $template = 'by_line';
}

$skgtParser->run();

echo $templates->render($template,array(
    'selectsData'   =>  $skgtParser->getSelectsData(),
    'params'        =>  $skgtParser->getParams(),
    'times'         =>  $skgtParser->getTimes(),
    'paramsShort'   =>  $skgtParser->getParamsShort(),
    'paramsNames'   =>  $skgtParser->getParamsNames(),
    'captchaImg'    =>  $skgtParser->getCaptchaImg()
));
        
        
