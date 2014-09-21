<?php
    include __DIR__ . "/settings.default.php";
    
    if(isfile(__DIR__ . "/settings.user.php")){
        include __DIR__ . "/settings.user.php";
    }
?>