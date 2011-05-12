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

$site_arr = array();
$site_total["bots"] = 0;
$site_total["people"] = 0;

for($y = gmdate("Y", $fstat_backend_start_timestamp); $y <= gmdate("Y", $fstat_backend_end_timestamp); $y++){
	if($y == gmdate("Y", $fstat_backend_start_timestamp)){
		$m = gmdate("n", $fstat_backend_start_timestamp);
	}else{
		$m = 1;
	}
	while($m <= 12){
		$pathname = $prefolder.$fstat_data_dir."paths/".$y."/".str_pad($m,2,"0",STR_PAD_LEFT)."/";
		$path_verz = @opendir($pathname);
		if(is_dir($pathname)){
			while (($file = @readdir($path_verz)) !== FALSE){
				if((substr($file, -5) == ".path")){
					$tmp_arr = @file($pathname.$file);
					if((substr($file, 0, 4) == "bot_")){
						$botpeople = "bots";
					}else{
						$botpeople = "people";
					}
					foreach($tmp_arr as $var){
						if($fstat_use_site_var){
							list($time, $sitename, $sitevar) = explode("|", trim($var));
							if(!isset($sitevar)){$sitevar = $sitename;}
						}else{
							list($time, $sitename) = explode("|", trim($var));
							$sitevar = $sitename;
						}
						
						if(!isset($site_arr[$sitename][$sitevar][$botpeople])){
							$site_arr[$sitename][$sitevar][$botpeople] = 1;
						}else{
							$site_arr[$sitename][$sitevar][$botpeople] = $site_arr[$sitename][$sitevar][$botpeople] + 1;
						}
							
						$site_total[$botpeople] = $site_total[$botpeople] + 1;
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

//sortieren
foreach($site_arr as $sitename2 => $tmp){
	$tmp_sort1 = array();//reset
	foreach($tmp as $sitevar2 => $tmp2){
		$tmp_sort1[$sitevar2] = $tmp2['people'];
	}
	array_multisort($tmp_sort1, SORT_DESC, $site_arr[$sitename2]);
}

//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);


foreach($site_arr as $name => $inhalt){
	$siteadd = $xmlausgabe->createElement("site");
	$siteadd->setAttribute("name", $name);
		foreach($inhalt as $name2 => $count){
			$subsiteadd = $xmlausgabe->createElement("sub");
			$subsiteadd->setAttribute("name", $name2);
			if(isset($count["bots"])){
				$subsiteadd->appendChild($xmlausgabe->createElement('bots', $count["bots"]));
			}else{
				$subsiteadd->appendChild($xmlausgabe->createElement('bots', 0));
			}
			if(isset($count["people"])){
				$subsiteadd->appendChild($xmlausgabe->createElement('people', $count["people"]));
			}else{
				$subsiteadd->appendChild($xmlausgabe->createElement('people', 0));
			}
			$siteadd->appendChild($subsiteadd);
		}
	$root->appendChild($siteadd);
}

$gesamtadd = $xmlausgabe->createElement("total");
	$gesamtadd->appendChild($xmlausgabe->createElement('bots', $site_total["bots"]));
	$gesamtadd->appendChild($xmlausgabe->createElement('people', $site_total["people"]));
$root->appendChild($gesamtadd);

echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <site name="index.php">
//    <sub name="home">
//      <bots>1</bots>
//      <people>5</people>
//    </sub>
//    <sub name="project">
//      <bots>3</bots>
//      <people>15</people>
//    </sub>
//  </site>
//  <site name="other.php">
//    <sub name="newsite">
//      <bots>1</bots>
//      <people>9</people>
//    </sub>
//  </site>
//  ...
//  <total>
//    <bots>10/bots>
//    <people>27</people>
//  </total>
//</list>

?>