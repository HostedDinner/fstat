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

$cou_arr = array();
$tmp_sort_c1 = array();

for($i = 1; $i <= gmdate("t", $fstat_backend_timestamp); $i++){
	$filename = $prefolder.$fstat_data_dir."stat/".$fstat_backend_year."/".str_pad($fstat_backend_month,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
	
	if(is_file($filename)){
		$xmldoc = new DOMDocument();
		$xmldoc->load($filename);
		
		$nodelist = $xmldoc->getElementsByTagName("visitor");
		
		foreach($nodelist as $visitor){
			$typ = $visitor->getElementsByTagName("typ")->item(0)->nodeValue;
			if(($fstat_show_bots_as_visitors) or ($typ != "Robot")){
				$name = $visitor->getElementsByTagName("ucon")->item(0)->nodeValue;
				$icon = $visitor->getElementsByTagName("ucoi")->item(0)->nodeValue;

				if(isset($name) and $name != ""){
					if(!isset($cou_arr[$name]['count'])){
						$cou_arr[$name]['count'] = 1;
					}else{
						$cou_arr[$name]['count'] = $cou_arr[$name]['count'] + 1;
					}
					$cou_arr[$name]['icon'] = $icon;
				}
			}
		}
		
	}
}

//nach Anzahl sortieren
foreach($cou_arr as $key => $row) {
	$tmp_sort_c1[$key]  = $row['count'];
}
array_multisort($tmp_sort_c1, SORT_DESC, $cou_arr);

//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);


foreach($cou_arr as $name => $sub){
	$refadd = $xmlausgabe->createElement("cou");
	$refadd->setAttribute("name", $name);
		$refadd->appendChild($xmlausgabe->createElement("count", $sub['count']));
		$refadd->appendChild($xmlausgabe->createElement("icon", $sub['icon']));
	$root->appendChild($refadd);
}



echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <cou name="Germany">
//    <count>6</count>
//    <icon>de.png</icon>
//  </cou>
//  <cou name="Great Britian">
//    <count>8</count>
//    <icon>gb.png</icon>
//  </cou>
//</list>

?>