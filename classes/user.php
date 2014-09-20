<?php

//pre:
// session_start();

require_once __DIR__ . "/../config/settings.php";

/**
 * Description of user
 *
 * @author Fabian
 */
class User {
    
    private $auth;
    private $username;
    private $level;
    


    public function __construct($username) {
        $this->auth = false;
        $this->username = $username;
        $this->level = 0;
        
        //try to validate with session
        $this->validateWithSession();
    }
    
    
    
    public function getLevel(){
        return $this->level;
    }
    
    public function isMinLevel($target){
        return ($this->level >= $target);
    }
    
    public function getName(){
        return $this->username;
    }
    
    public function isAuth(){
        return $this->auth;
    }
    
    
    
    public function login($pass){
        global $fstat_password_file;
        $succ = validateWithPassword($fstat_password_file, $pass);
        
        if($succ){
            //store in session
            $_SESSION['user_name'] = $this->username;
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['user_level'] = $this->level;
        }
        return $succ;
    }
    
    
    private function validateWithSession(){
        //TODO also check ip?
        if(isset($_SESSION['user_agent'])){
            if($_SESSION['user_agent'] == $_SERVER['HTTP_USER_AGENT']){
                $this->auth = true;
                $this->level = (int) $_SESSION['user_level'];
                $this->username = $_SESSION['user_name'];
            }else{
                $this->auth = false;
            }
        }else{
            $this->auth = false;
        }
        
        return $this->auth;
    }
    
    private function validateWithPassword($passfile, $pass){
        $hashes = file($passfile);
        
        $this->auth = false;
        
        foreach ($hashes as $entry){
            list($level, $name, $hash) = explode(":", $entry, 3);
            if($this->username == $name){
                if(password_verify($pass, $hash)){
                    $this->auth = true;
                    $this->level = (int) $level;
                }else{
                    $this->auth = false;
                }
                break;
            }//else continue
        }
        return $this->auth;
    }
}

?>