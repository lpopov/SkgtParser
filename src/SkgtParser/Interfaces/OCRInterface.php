<?php

namespace SkgtParser\Interfaces;

/**
 * Interface for OCR classes
 */

interface OCRInterface
{
    /**
     * Checks if OCR module works
     * 
     * @return boolean 
     */
    public function ocrWorks();
    
    /**
     * Return the error string
     * 
     * @return string Error string
     */
    public function getError();
    
    /**
     * OCRs image
     * 
     * @param string $rawdata
     * @return string|boolean Returns the captcha code or false on error
     */
    public function ocrCaptchaCode($rawdata);
}
