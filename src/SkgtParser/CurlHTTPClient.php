<?php

namespace SkgtParser;

use DOMDocument;

use SkgtParser\Interfaces\HTTPClientInterface;

/**
 * Curl interface class
 */

class CurlHTTPClient implements HTTPClientInterface
{
    protected $url;
    protected $ocrCaptcha;
    protected $ocr;
    
    /**
     * Contructor
     * 
     * @param string $url The url that the page is on
     * @param boolean $ocrCaptcha Should OCR be used on the captcha
     * @param OCRInterface $ocr
     */
    public function __construct($url, $ocrCaptcha, $ocr){
        $this->url = $url;
        $this->ocrCaptcha = $ocrCaptcha;
        $this->ocr = $ocr;
    }
    
    /**
     * Gets the webpage content and parses the captcha if enabled
     * 
     * @return array Web page data
     */
    public function getWebPage($post = array(), $decodeCaptcha = false)
    {
//        session_write_close();
        $cookieFile = "/tmp/cookie_".session_id().".txt";
        $options = array(
            CURLOPT_RETURNTRANSFER  =>  true,     // return web page
            CURLOPT_COOKIE          =>  1,
            CURLOPT_COOKIEFILE      =>  $cookieFile,
            CURLOPT_COOKIEJAR       =>  $cookieFile,
            CURLOPT_HEADER          =>  false,    // don't return headers
            CURLOPT_FOLLOWLOCATION  =>  true,     // follow redirects
            CURLOPT_ENCODING        =>  "",       // handle all encodings
            CURLOPT_USERAGENT       =>  "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.10) Gecko/20050716 Thunderbird/1.0.6", // who am i
            CURLOPT_CONNECTTIMEOUT  =>  120,      // timeout on connect
            CURLOPT_TIMEOUT         =>  120,      // timeout on response
            CURLOPT_MAXREDIRS       =>  10,       // stop after 10 redirects,
        );

        if(!empty($post)){
            $post_vars = '';
            foreach($post as $k => $v){
                if(is_array($v)) continue;
                $post_vars.="&$k=".urlencode($v);
            }
            $post_vars = substr($post_vars,1);
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $post_vars;
        }

        $ch      = curl_init( $this->url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );

        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        $header['captcha'] = array(
            'img'   =>  '',
            'code'  =>  false
        );
//        var_dump($content);
        if($decodeCaptcha){
            $header['captcha'] = $this->getCaptchaData($content, $options);
        }

        if ( $header['errno'] != 0 || $header['http_code'] != 200 ){
            die("Error!!!");
        }

        return $header;
    }
    
    /**
     * Return captcha image or captcha code
     * 
     * @param string $content Web page content
     * @param array $options Curl options
     * @return array Image data or captcha code
     */
    protected function getCaptchaData($content, $options)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $dom->preserveWhiteSpace = false;
        $captchaCode = false;
        $img = $dom->getElementById('id_Captcha');
        if(!empty($img)){
            $captchaSrc = "http://gps.skgt-bg.com/Web/".$img->getAttribute('src');

            $ch = curl_init ($captchaSrc);
            $options[CURLOPT_BINARYTRANSFER] = true;
            curl_setopt_array( $ch, $options );

            $rawdata=curl_exec($ch);
            curl_close ($ch);

            if($this->ocrCaptcha) {
                $captchaCode = $this->ocr->ocrCaptchaCode($rawdata);
            }
        }
        
        return array(
            'img'   =>  ($captchaCode===false)?$rawdata:'',
            'code'  =>  $captchaCode
        );
    }
}