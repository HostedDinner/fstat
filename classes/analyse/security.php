<?php
/**
 * can check if connection is secured
 *
 * @author Fabian
 */
class Security {
    public static function isHTTPS(){
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    }
}

?>