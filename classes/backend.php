<?php

/**
 * Description of Backend
 *
 * @author Fabian
 */
class Backend {
    
    public function __construct() {
        
    }
    
    
    public static function getXML($filename, $year = null, $month = null){
        if (is_file($filename)) {

            if ($year != null) {
                $fstat_backend_year = $year;
            }
            if ($month != null) {
                $fstat_backend_month = $month;
            }

            $is_include = true;
            ob_start();
                include $filename;
                $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        } else {
            return false;
        }
    }
}
