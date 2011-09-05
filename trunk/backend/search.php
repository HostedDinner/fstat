<?php
if(!isset($is_include)){
	header('Content-type: text/xml');
	error_reporting(0); //keine Fehler anzeigen
	//error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
	//error_reporting(E_ALL); // alle Fehler anzeigen
	$prefolder = "./../";
}else{
	$prefolder = "./";
}

$fstat_backend_get_list = "search";

ob_start();
	include $prefolder."backend/all.php";
	$contents = ob_get_contents();
ob_end_clean();
echo $contents;
?>