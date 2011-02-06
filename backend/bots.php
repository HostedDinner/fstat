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

$bot_arr = array();
$tmp_sort_o1 = array();

for($i = 1; $i <= gmdate("t", $fstat_backend_timestamp); $i++){
	$filename = $prefolder.$fstat_data_dir."stat/".$fstat_backend_year."/".str_pad($fstat_backend_month,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
	
	if(is_file($filename)){
		$xmldoc = new DOMDocument();
		$xmldoc->load($filename);
		
		$nodelist = $xmldoc->getElementsByTagName("visitor");
		
		foreach($nodelist as $visitor){
			$typ = $visitor->getElementsByTagName("typ")->item(0)->nodeValue;
			if($typ == "Robot"){
				$bot_fam = $visitor->getElementsByTagName("ufam")->item(0)->nodeValue;
				$bot_name = $visitor->getElementsByTagName("unam")->item(0)->nodeValue;
				$bot_icon = $visitor->getElementsByTagName("uico")->item(0)->nodeValue;
				
				//mit Subverson
				if(!isset($bot_arr[$bot_fam][$bot_name]['count'])){
					$bot_arr[$bot_fam][$bot_name]['count'] = 1;
				}else{
					$bot_arr[$bot_fam][$bot_name]['count'] = $bot_arr[$bot_fam][$bot_name]['count'] + 1;
				}
			
				//Ohne Sub, nur Anzahl
				if(!isset($bot_arr[$bot_fam]['all']['count'])){
					$bot_arr[$bot_fam]['all']['count'] = 1;
				}else{
					$bot_arr[$bot_fam]['all']['count'] = $bot_arr[$bot_fam]['all']['count'] + 1;
				}
				//letztes Icon!
				$bot_arr[$bot_fam]['all']['icon'] = $bot_icon;
				$bot_arr[$bot_fam][$bot_name]['icon'] = $bot_icon;
			}
		}
	}
}

//Sortieren nach Anzahl:
foreach($bot_arr as $key => $row) {
	$tmp_sort_o1[$key]  = $row['all']['count'];
}
array_multisort($tmp_sort_o1, SORT_DESC, $bot_arr);

//Sortiern der Versionen:
foreach($bot_arr as $arr2 => $temp2){
	krsort($bot_arr[$arr2]);
}


//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);

//extra kind of root for Bots
$bot_root = $xmlausgabe->createElement("bot");
$bot_root = $root->appendChild($bot_root);

foreach($bot_arr as $fam => $inhalt){
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
	$bot_root->appendChild($typadd);
}



echo $xmlausgabe->saveXML();
//Aufbau der XML Datei
//<list>
//  <bot>
//    <typ name="MSN Bot">
//      <sub version="MSN Bot 1.2">
//        <count>1</count>
//        <icon>bot_msn.png</icon>
//      </sub>
//      <sub version="MSN Bot 1.3">
//        <count>1</count>
//        <icon>bot_msn.png</icon>
//      </sub>
//      <all>
//        <count>2</count>
//        <icon>bot_msn.png</icon>
//      </all>
//    </typ>
//    <typ name="Googlebot">
//      <sub version="GoogleBot 2.1">
//        <count>2</count>
//        <icon>bot_google.png</icon>
//      </sub>
//      <all>
//        <count>2</count>
//        <icon>bot_google.png</icon>
//      </all>
//    </typ>
//  </bot>
//</list>

?>