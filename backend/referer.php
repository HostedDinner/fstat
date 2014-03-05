<?php
if(!isset($is_include)){
    header('Content-type: text/xml');
    error_reporting(0); //keine Fehler anzeigen
    //error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
    //error_reporting(E_ALL); // alle Fehler anzeigen
}

require_once __DIR__ . "/../classes/backend.php";

$backend = new Backend();
$backend->setShowList('ref');

ob_start();
    include __DIR__ . "/all.php";
    $contents = ob_get_contents();
ob_end_clean();
echo $contents;
?>