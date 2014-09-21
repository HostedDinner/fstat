<?php
    include __DIR__ . "/settings.default.php";
    
    if(is_file(__DIR__ . "/settings.user.php")){
        include __DIR__ . "/settings.user.php";
    }
?>