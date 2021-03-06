<?php

/**
 * TestCase class to inherit from to allow Unit Testing based checking of
 * HTML validity.
 *
 * @author Neil Crosby
 * @copyright 2008
 * @license http://creativecommons.org/licenses/by-sa/2.0/uk/
 **/

class TheCodeTrainBaseValidatorTestCase extends PHPUnit_Framework_TestCase {
    protected function getCurlResponse( $url, $aOptions = array() ) {

        $session = curl_init();
        curl_setopt( $session, CURLOPT_URL, $url );
        
        $showHeader = ( isset($aOptions['headers']) && $aOptions['headers'] ) ? true : false;
        
        curl_setopt( $session, CURLOPT_HEADER, $showHeader );
        curl_setopt( $session, CURLOPT_RETURNTRANSFER, 1 );
        
        if ( isset($aOptions['post']) ) {
            curl_setopt( $session, CURLOPT_POST, 1 );
            curl_setopt( $session, CURLOPT_POSTFIELDS, $aOptions['post'] );
        }

        $result = curl_exec( $session );

        curl_close( $session );
        
        return $result;
    }
   
    protected function getValidationError($htmlChunk) {
        $this->markTestSkipped('No validator');
        return "oh noes, no validator existed";

		$html = <<< HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head><title>title</title></head>
<body>
$htmlChunk
</body></html>
HTML;

        $validator = new HtmlValidator(Config::W3C_SOAP_API);
        if ( $validator->isValid($html) ) {
            return false;
        }
        
        $result = $validator->getErrors();
        
        return $result[0];
    }
    
}
?>