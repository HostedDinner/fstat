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

$cou_arr = array();
$tmp_sort_c1 = array();

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
					if(($fstat_show_bots_as_visitors) or ($typ != "Robot" and $typ != "Validator")){
						$name = @$visitor->getElementsByTagName("ucon")->item(0)->nodeValue;
						$icon = @$visitor->getElementsByTagName("ucoi")->item(0)->nodeValue;
		
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
		$m++;
		if($y == gmdate("Y", $fstat_backend_end_timestamp) and $m > gmdate("n", $fstat_backend_end_timestamp)){
			$m = 13;
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

//extra kind of root for Country
$cou_root = $xmlausgabe->createElement("country");
$cou_root = $root->appendChild($cou_root);

foreach($cou_arr as $name => $sub){
	$refadd = $xmlausgabe->createElement("cou");
	$refadd->setAttribute("name", $name);
		$refadd->appendChild($xmlausgabe->createElement("count", $sub['count']));
		$refadd->appendChild($xmlausgabe->createElement("icon", $sub['icon']));
	$cou_root->appendChild($refadd);
}



echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <country>
//    <cou name="Germany">
//      <count>6</count>
//      <icon>de.png</icon>
//    </cou>
//    <cou name="Great Britian">
//      <count>8</count>
//      <icon>gb.png</icon>
//    </cou>
//  </dountry>
//</list>

?>