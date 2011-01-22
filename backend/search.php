<?php
if(!isset($is_include)){
	header('Content-type: text/xml');
	error_reporting(E_NONE); //keine Fehler anzeigen
	//error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
	//error_reporting(E_ALL); // alle Fehler anzeigen
	$prefolder = "./../";
}else{
	$prefolder = "./";
}

include $prefolder."config/settings.php";
include $prefolder."functions/backend_include.php";

$key_arr = array();

for($i = 1; $i <= date("t", $fstat_backend_timestamp); $i++){
	$filename = $prefolder.$fstat_data_dir."stat/".$fstat_backend_year."/".str_pad($fstat_backend_month,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
	
	if(is_file($filename)){
		$xmldoc = new DOMDocument();
		$xmldoc->load($filename);
		
		
		$nodelist = $xmldoc->getElementsByTagName("rkey");
		
		foreach($nodelist as $keys){
			$key = $keys->nodeValue;

			if(isset($key) and $key != ""){
				if(!isset($key_arr[$key])){
					$key_arr[$key] = 1;
				}else{
					$key_arr[$key] = $key_arr[$key] + 1;
				}
			}
		}
		
	}
}

arsort($key_arr);


//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);


foreach($key_arr as $ref => $count){
	$refadd = $xmlausgabe->createElement("ref");
	$refadd->setAttribute("keywords", $ref);
		$refadd->appendChild($xmlausgabe->createElement("count", $count));
	$root->appendChild($refadd);
}



echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <ref keywords="RWCP">
//    <count>6</count>
//  </ref>
//  <ref keywords="Witzbox">
//    <count>3</count>
//  </ref>
//</list>

?>