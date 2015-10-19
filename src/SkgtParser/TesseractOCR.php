<?php

namespace SkgtParser;

use SkgtParser\Interfaces\OCRInterface;

/**
 * Tesseract OCR interface class
 */

class TesseractOCR implements OCRInterface
{
    protected $tesseractBin;
    protected $ocrError;
    protected $ocrWorks;
    
    public function __construct()
    {
        $this->tesseractBin = '/usr/bin/tesseract';
        $this->ocrWorks = $this->binaryExists();
    }
    
    /**
     * OCRs the image
     * 
     * @param string $rawdata
     * @return string|boolean Returns the captcha code or false on error
     */
    public function ocrCaptchaCode($rawdata)
    {
        if(!$this->ocrWorks()){
            return false;
        }
        $captchaCode = '';
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("pipe", "w")   // stderr is a pipe for the errors
        );
        
        $pipes = null;
        
        $process = proc_open($this->tesseractBin.' stdin stdout', $descriptorspec, $pipes, '/tmp');

        if (is_resource($process)) {
            
            fwrite($pipes[0], $rawdata);
            fclose($pipes[0]);

            $captchaCode = trim(stream_get_contents($pipes[1]));
            $this->ocrError = trim(stream_get_contents($pipes[2]));
            
            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);
        }
        
        return empty($this->ocrError)?$captchaCode:false;
    }
    
    /**
     * Checks if tesseract binary exists
     * 
     * @return boolean
     */
    public function binaryExists()
    {
        if(!empty($this->tesseractBin) && is_file($this->tesseractBin)){
            return true;
        }
        
        $this->ocrError = 'Binary not found!';
        return false;
    }
    
    /**
     * Returns OCR status
     * 
     * @return boolean
     */
    public function ocrWorks()
    {
        return $this->ocrWorks;
    }
    
    /**
     * Returns error string
     * 
     * @return string
     */
    
    public function getError()
    {
        return $this->ocrError;
    }
}
