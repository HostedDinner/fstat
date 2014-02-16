<?php

/**
 * Some kind of struct
 * for holding user information
 *
 * @author Fabian
 */
class User {
    public $agent;
    public $ip;
    public $escapedIP;
    public $time;
    public $type;
    public $is_new;


    public function __construct($ip, $uas) {
        $this->ip = $ip;
        $this->escapedIP = str_replace(":", "_", $ip); //IPv6 adresses have ":" (no ":" allowed in directory names)
        $this->agent = $uas;
        $this->time = time();
        $this->type = ""; // dont know yet
        $this->is_new = true; //first assumption is, that it is a new user
    }
    
    
    public function writeToCache($cache_folder){
        $f_cont = @fopen($cache_folder."/". $this->escapedIP . ".ip", 'w');
        $tmp = $this->time . "\n" . $this->agent . "\n" . $this->type;
        fputs($f_cont, $tmp);
        fclose($f_cont);
    }
    
    public function getFromCache($cache_folder){
        if(file_exists($cache_folder . "/" . $this->escapedIP . ".ip")){
            $f_cont = @fopen($cache_folder . "/" . $this->escapedIP . ".ip", 'r');
            $tmp_time = trim(fgets($f_cont)); //first row is the time
            $tmp_uas = trim(fgets($f_cont)); //second one the UAS
            $tmp_type = trim(fgets($f_cont)); //third the type (Browser, Bot, ...)
            fclose($f_cont);
            if ($tmp_uas == $this->agent) {//und wenn der Useragent stimmt:
                $this->type = $tmp_type;
                $this->time = $tmp_time;
                $this->is_new = false;
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
