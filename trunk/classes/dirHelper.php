<?php

/**
 * Description of DirHelper
 *
 * @author Fabian
 */
class DirHelper {
    
    private $base_dir;

    public function __construct($base) {
        $this->base_dir = rtrim($base, "/\\");
    }

    
    public function checkExists($dir, $otherbase = false){
        if (!is_dir(($otherbase ? "" : $this->base_dir."/").$dir)) {
            if (!mkdir(($otherbase ? "" : $this->base_dir."/").$dir)) {
                //echo "Konnte Verzeichnis " . $dir . " f&uuml;r F-Stat nicht &ouml;ffnen/erstellen!";
                return false;
            }
        }
        return true;
    }
    
    
    public function deleteOldIPs($ip_cache_dir, $new_user_time){
        $ipdir = opendir($this->base_dir."/".$ip_cache_dir);
        while (($file = readdir($ipdir)) !== FALSE) {
            if ((substr($file, -3) == ".ip")) {
                //$file;
                $f_cont = @fopen($this->base_dir."/".$ip_cache_dir."/".$file, 'r');
                $timestamp = trim(fgets($f_cont)); //first row is the timestamp
                //delete if too old
                if ($timestamp < (time() - $new_user_time)) {
                    fclose($f_cont);
                    unlink($this->base_dir."/".$ip_cache_dir."/".$file);
                } else {
                    fclose($f_cont);
                }
            }
        }
        closedir($ipdir);
    }
}
