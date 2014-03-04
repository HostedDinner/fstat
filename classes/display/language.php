<?php

/**
 * Language
 *
 * @author Fabian
 */

require_once __DIR__ . "/../../config/settings.php";

class Language {
    
    private static $languageSet = false; //protect double setting the language
    private $current;
    
    public function __construct($lang = null) {
        $this->setLanguage($lang);
    }
    
    
    
    private function setLanguage($lang){
        global $fstat_default_lang;
        
        if(self::$languageSet == false){
            self::$languageSet = true;
                    
            if($lang == null){
                $this->current = $fstat_default_lang;
            }else{
                $lang = preg_replace('#[^0-9a-z]#i', '' ,$lang);
                $this->current = $lang;
            }
            
            
            
            $filename = __DIR__ . "/../../lang/lang.".$this->current.".php";
            if(file_exists($filename)){
                include_once $filename;
            }else{
                //if this fails, then the config should be changed...
                include_once __DIR__ . "/../../lang/lang.".$fstat_default_lang.".php";
            }
        }
    }
    
    
    public function getLanguage(){
        return $this->current;
    }


    public static function lookupLang($short){
        $longname = $short;
        if (file_exists(__DIR__ . "/../../lang/list.ini")) {
            $listIni = parse_ini_file(__DIR__ . "/../../lang/list.ini");
            $longname = isset($listIni[$short]) ? $listIni[$short] : $short;
        }        
        return $longname;
    }
}
