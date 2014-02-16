<?php

/**
 * Description of dir_helper
 *
 * @author Fabian
 */
class DirHelper {
    
    public static function checkExists($dir){
        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                //echo "Konnte Verzeichnis " . $dir . " f&uuml;r F-Stat nicht &ouml;ffnen/erstellen!";
                return false;
            }
        }
        return true;
    }
    
}
