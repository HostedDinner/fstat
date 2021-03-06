<?php
if(!isset($is_include)){
    header('Content-type: text/xml');
    error_reporting(0); //keine Fehler anzeigen
    //error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
    //error_reporting(E_ALL); // alle Fehler anzeigen
}

include __DIR__ . "/../config/settings.php";
include __DIR__ . "/../functions/backend_include.php"; //(re)defines $backend

require_once __DIR__ . "/../classes/dirHelper.php";

$dirhelper = new DirHelper(__DIR__ . "/../");

$use_cached = false;

if($backend->getModus() == Backend::MODUS_MONTH){
        $path = $fstat_data_dir."paths/".$backend->getTime()->getStartYear();
        $dirhelper->checkExists($path, false); //actually does nothing than creating, if not exists
        $path = $path."/".str_pad($backend->getTime()->getStartMonth(), 2, "0", STR_PAD_LEFT);
        $dirhelper->checkExists($path, false); //actually does nothing than creating, if not exists
	$cache_filename = __DIR__ . "/../".$path."/cache.xml";
	if(is_file($cache_filename)){$use_cached = true;}
}elseif($backend->getModus() == Backend::MODUS_YEAR){
        $path = $fstat_data_dir."paths/".$backend->getTime()->getStartYear();
        $dirhelper->checkExists($path, false); //actually does nothing than creating, if not exists
	$cache_filename = __DIR__ . "/../".$path."/cache.xml";
	if(is_file($cache_filename)){$use_cached = true;}
}//else MODUS_DAY (not cached)

//force refresh
if($backend->getForceRefresh() == true){
	$use_cached = false;
}

if($use_cached == false){
	$site_arr = array();
	$site_total["bots"] = 0;
	$site_total["people"] = 0;
	
	if($backend->getModus() >= Backend::MODUS_YEAR){
		$m = 1;
		$m_end = 12;
	}else{
		$m = $backend->getTime()->getStartMonth();
		$m_end = $backend->getTime()->getStartMonth();
	}
	
	for(; $m <= $m_end; $m++){
		$pathname = __DIR__ . "/../".$fstat_data_dir."paths/".$backend->getTime()->getStartYear()."/".str_pad($m, 2, "0", STR_PAD_LEFT)."/";
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
						
						//test if modus year/month OR if it's the right day
						if(($backend->getModus() > Backend::MODUS_DAY) || (gmdate("j", $time) == $backend->getTime()->getStartDay())){
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
		}
	}
	
	//sortieren
	foreach($site_arr as $sitename2 => $tmp){
		$tmp_sort1 = array();//reset
		foreach($tmp as $sitevar2 => $tmp2){
			if(isset($tmp2['people'])){
				$tmp_sort1[$sitevar2] = $tmp2['people'];
			}else{
				$tmp_sort1[$sitevar2] = 0;
			}
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
	
	//extra kind of root for S-Counter
	$sit_root = $xmlausgabe->createElement("sites");
	$sit_root = $root->appendChild($sit_root);
	
	$sit_root->setAttribute("update", date("c"));
	
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
		$sit_root->appendChild($siteadd);
	}
	
	$gesamtadd = $xmlausgabe->createElement("total");
		$gesamtadd->appendChild($xmlausgabe->createElement('bots', $site_total["bots"]));
		$gesamtadd->appendChild($xmlausgabe->createElement('people', $site_total["people"]));
	$sit_root->appendChild($gesamtadd);
	
	if(isset($cache_filename)){
		$xmlausgabe->save($cache_filename);
	}
	
	echo $xmlausgabe->saveXML();
}else{
	echo file_get_contents($cache_filename);
}
//Aufbau der XML Datei
//<list>
//  <sites update="2012-03-04T11:49:24+01:00">
//    <site name="index.php">
//      <sub name="home">
//        <bots>1</bots>
//        <people>5</people>
//      </sub>
//      <sub name="project">
//        <bots>3</bots>
//        <people>15</people>
//      </sub>
//    </site>
//    <site name="other.php">
//      <sub name="newsite">
//        <bots>1</bots>
//        <people>9</people>
//      </sub>
//    </site>
//    ...
//    <total>
//      <bots>10/bots>
//      <people>27</people>
//    </total>
//  </sites>
//</list>

?>