<?php

/**
 * Parses a reference
 *
 * @author Fabian
 */
class Reference {
    
    private $referer_url;
    private $keywords = "";
    private $domain = "";
    
    
    function __construct($referer_url) {
        //The & is for the regex, that there is in every case a end identifier
        $this->referer_url = $referer_url."&";
    }
    
    public function parse(){ 
        
        $preg = "#(?:\?|&)(?:query|text|words|eingabe|q|p|r)=.+?(?=&)#is";
        preg_match($preg, $this->referer_url, $text);
        
        if(isset($text[0])){
            $this->keywords = strstr($text[0], "=");
            $this->keywords = trim($this->keywords, "=&");
            $this->keywords = htmlspecialchars(urldecode($this->keywords));//Hope it's safe now, too
        }
        //else nothing found...
        
        
        $preg2 = "#http(?:s)?://.+?/#i";
        preg_match($preg2, $this->referer_url, $text2);
        
        if(isset($text2[0])){
            $tmp = $text2[0];
            $this->domain = strstr($text2[0], "/");
            $this->domain = trim($this->domain, "/");
            $this->domain = htmlspecialchars(urldecode($this->domain));//Hope it's safe now, too
        }
        //else nothing found...
    }
    
    
    //Basic Getter
    public function getKeywords() {
        return $this->keywords;
    }
    
    public function getDomain() {
        return $this->domain;
    }
    
}
