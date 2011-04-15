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

$ref_arr = array();

for($i = 1; $i <= gmdate("t", $fstat_backend_timestamp); $i++){
	$filename = $prefolder.$fstat_data_dir."stat/".$fstat_backend_year."/".str_pad($fstat_backend_month,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
	
	if(is_file($filename)){
		$xmldoc = new DOMDocument();
		$xmldoc->load($filename);
		
		$nodelist = $xmldoc->getElementsByTagName("visitor");
		
		foreach($nodelist as $visitor){
			$typ = @$visitor->getElementsByTagName("typ")->item(0)->nodeValue;
			if(($fstat_show_bots_as_visitors) or ($typ != "Robot")){
				$domain = @$visitor->getElementsByTagName("rdom")->item(0)->nodeValue;
	
				if((isset($domain)) and ($domain != "")){
					if(!isset($ref_arr[$domain])){
						$ref_arr[$domain] = 1;
					}else{
						$ref_arr[$domain] = $ref_arr[$domain] + 1;
					}
				}
			}
		}
	}
}

arsort($ref_arr);


//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);


foreach($ref_arr as $ref => $count){
	$refadd = $xmlausgabe->createElement("ref");
	$refadd->setAttribute("domain", $ref);
		$refadd->appendChild($xmlausgabe->createElement("count", $count));
	$root->appendChild($refadd);
}



echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <ref domain="localhost">
//    <count>3</count>
//  </ref>
//  <ref domain="www.google.com">
//    <count>5</count>
//  </ref>
//</list>

?>