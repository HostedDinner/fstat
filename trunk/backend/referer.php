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

include $prefolder."config/settings.php";
include $prefolder."functions/backend_include.php";

$ref_arr = array();

for($y = gmdate("Y", $fstat_backend_start_timestamp); $y <= gmdate("Y", $fstat_backend_end_timestamp); $y++){
	if($y == gmdate("Y", $fstat_backend_start_timestamp)){
		$m = gmdate("n", $fstat_backend_start_timestamp);
	}else{
		$m = 1;
	}
	while($m <= 12){
		for($i = 1; $i <= 31; $i++){//exact days of month would be only slightly faster
			$filename = $prefolder.$fstat_data_dir."stat/".$y."/".str_pad($m,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
			
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
		$m++;
		if($y == gmdate("Y", $fstat_backend_end_timestamp) and $m > gmdate("n", $fstat_backend_end_timestamp)){
			$m = 13;
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