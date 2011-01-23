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

$os_arr = array();
$tmp_sort_o1 = array();

for($i = 1; $i <= gmdate("t", $fstat_backend_timestamp); $i++){
	$filename = $prefolder.$fstat_data_dir."stat/".$fstat_backend_year."/".str_pad($fstat_backend_month,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
	
	if(is_file($filename)){
		$xmldoc = new DOMDocument();
		$xmldoc->load($filename);
		
		$xpath = new  DOMXPath($xmldoc);
		
		//People
		$query = '//typ[.!="Robot"]/..'; //Parent Node von typ!=Robot (<visitor>)
		$nodelist = $xpath->query($query);
		
		foreach($nodelist as $visitor){
			$os_fam = $visitor->getElementsByTagName("ofam")->item(0)->nodeValue;
			$os_name = $visitor->getElementsByTagName("onam")->item(0)->nodeValue;
			$os_icon = $visitor->getElementsByTagName("oico")->item(0)->nodeValue;
			
			//mit Subverson
			if(!isset($os_arr[$os_fam][$os_name]['count'])){
				$os_arr[$os_fam][$os_name]['count'] = 1;
			}else{
				$os_arr[$os_fam][$os_name]['count'] = $os_arr[$os_fam][$os_name]['count'] + 1;
			}
			
			//Ohne Sub, nur Anzahl
			if(!isset($os_arr[$os_fam]['all']['count'])){
				$os_arr[$os_fam]['all']['count'] = 1;
			}else{
				$os_arr[$os_fam]['all']['count'] = $os_arr[$os_fam]['all']['count'] + 1;
			}
			//letztes Icon!
			$os_arr[$os_fam]['all']['icon'] = $os_icon;
			$os_arr[$os_fam][$os_name]['icon'] = $os_icon;
		}
		
	}
}

//Sortieren nach Anzahl:
foreach($os_arr as $key => $row) {
	$tmp_sort_o1[$key]  = $row['all']['count'];
}
array_multisort($tmp_sort_o1, SORT_DESC, $os_arr);

//Sortiern der Versionen:
foreach($os_arr as $arr2 => $temp2){
	krsort($os_arr[$arr2]);
}


//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);

//extra kind of root for OS
$os_root = $xmlausgabe->createElement("os");
$os_root = $root->appendChild($os_root);

foreach($os_arr as $fam => $inhalt){
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
	$os_root->appendChild($typadd);
}



echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <os>
//    <typ name="Windows">
//      <sub version="Windows Vista">
//        <count>7</count>
//        <icon>windowsvista.png</icon>
//      </sub>
//      <sub version="Windows 98">
//        <count>1</count>
//        <icon>windows.png</icon>
//      </sub>
//      <all>
//        <count>8</count>
//        <icon>windows.png</icon>
//      </all>
//    </typ>
//    <typ name="Linux">
//      <sub version="Ubuntu">
//        <count>2</count>
//        <icon>ubuntu.png</icon>
//      </sub>
//      <all>
//        <count>2</count>
//        <icon>ubuntu.png</icon>
//      </all>
//    </typ>
//  </os>
//</list>

?>