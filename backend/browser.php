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

$br_arr = array();
$tmp_sort_b1 = array();

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
						$br_fam = @$visitor->getElementsByTagName("ufam")->item(0)->nodeValue;
						$br_name = @$visitor->getElementsByTagName("unam")->item(0)->nodeValue;
						$br_icon = @$visitor->getElementsByTagName("uico")->item(0)->nodeValue;
						
						//mit Subverson
						if(!isset($br_arr[$br_fam][$br_name]['count'])){
							$br_arr[$br_fam][$br_name]['count'] = 1;
						}else{
							$br_arr[$br_fam][$br_name]['count'] = $br_arr[$br_fam][$br_name]['count'] + 1;
						}
						
						//Ohne Sub, nur Anzahl
						if(!isset($br_arr[$br_fam]['all']['count'])){
							$br_arr[$br_fam]['all']['count'] = 1;
						}else{
							$br_arr[$br_fam]['all']['count'] = $br_arr[$br_fam]['all']['count'] + 1;
						}
						//letztes Icon!
						$br_arr[$br_fam]['all']['icon'] = $br_icon;
						$br_arr[$br_fam][$br_name]['icon'] = $br_icon;
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

//Sortieren nach Anzahl:
foreach($br_arr as $key => $row) {
	$tmp_sort_b1[$key]  = $row['all']['count'];
}
array_multisort($tmp_sort_b1, SORT_DESC, $br_arr);

//Sortiern der Versionen:
foreach($br_arr as $arr2 => $temp2){
	krsort($br_arr[$arr2]);
}


//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);

//extra kind of root for Browser
$br_root = $xmlausgabe->createElement("browser");
$br_root = $root->appendChild($br_root);

foreach($br_arr as $fam => $inhalt){
	$typadd = $xmlausgabe->createElement("typ");
	$typadd->setAttribute("name", $fam);
		foreach($inhalt as $subname => $data){
			if($subname == "all"){continue;}
			$subtypadd = $xmlausgabe->createElement("sub");
			$subtypadd->setAttribute("version", $subname);
				$subtypadd->appendChild($xmlausgabe->createElement("count", $data["count"]));
				$subtypadd->appendChild($xmlausgabe->createElement("icon", $data["icon"]));
			$typadd->appendChild($subtypadd);
		}
			$alladd = $xmlausgabe->createElement("all");
			$alladd->appendChild($xmlausgabe->createElement("count", $inhalt["all"]["count"]));
			$alladd->appendChild($xmlausgabe->createElement("icon", $inhalt["all"]["icon"]));
		$typadd->appendChild($alladd);
	$br_root->appendChild($typadd);
}



echo $xmlausgabe->saveXML();


//Aufbau der XML Datei
//<list>
//  <browser>
//    <typ name="Firefox">
//      <sub version="Firefox 3.6.3">
//        <count>7</count>
//        <icon>firefox.png</icon>
//      </sub>
//      <sub version="Firefox 3.6.2">
//        <count>3</count>
//        <icon>firefox.png</icon>
//      </sub>
//      <all>
//        <count>10</count>
//        <icon>firefox.png</icon>
//      </all>
//    </typ>
//    <typ name="Chrome">
//      <sub version="Chrome 6.0.472.59">
//        <count>1</count>
//        <icon>chrome.png</icon>
//      </sub>
//      <all>
//        <count>1</count>
//        <icon>chrome.png</icon>
//      </all>
//    </typ>
//  </browser>
//</list>
?>