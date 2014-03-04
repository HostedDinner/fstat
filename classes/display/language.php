<?php

/**
 * Language
 *
 * @author Fabian
 */
class Language {
    
    public function __construct() {
        ;
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
