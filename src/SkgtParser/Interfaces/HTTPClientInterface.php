<?php

namespace SkgtParser\Interfaces;

use SkgtParser\Interfaces\OCRInterface;

/**
 * Interfce for HTTP clients
 */

interface HTTPClientInterface 
{
    
    /**
     * Contructor
     * 
     * @param string $url The url that the page is on
     * @param boolean $ocrCaptcha Should OCR be used on the captcha
     * @param OCRInterface $ocr
     */
    public function __construct($url, $ocrCaptcha, $ocr);
    
    /**
     * Gets the webpage content and parses the captcha if enabled
     * 
     * @return array Web page data
     */
    public function getWebPage();
}