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

$count_bots[24] = 0;//alle
$count_people[24] = 0;//alle

//0-> 00:00 - 01:00; 1->01:00-02:00; usw; 24->alle

for($i = 1; $i <= gmdate("t", $fstat_backend_timestamp); $i++){
	$filename = $prefolder.$fstat_data_dir."stat/".$fstat_backend_year."/".str_pad($fstat_backend_month,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
	
	if(is_file($filename)){
		$xmldoc = new DOMDocument();
		$xmldoc->load($filename);
		
		$nodelist = $xmldoc->getElementsByTagName("visitor");
		
		foreach($nodelist as $visitor){
			$typ = $visitor->getElementsByTagName("typ")->item(0)->nodeValue;
			$timestr = $visitor->getElementsByTagName("uti")->item(0)->nodeValue;
			$time = date("G", $timestr);//hier kein gmdate, da hier das erste mal konvertiert wird :P
			
			if($typ == "Robot"){
				if(!isset($count_bots[$time])){
					$count_bots[$time] = 1;
				}else{
					$count_bots[$time] = $count_bots[$time] + 1;
				}
				$count_bots[24] = $count_bots[24] + 1;
			}else{
				if(!isset($count_people[$time])){
					$count_people[$time] = 1;
				}else{
					$count_people[$time] = $count_people[$time] + 1;
				}
				$count_people[24] = $count_people[24] + 1;
			}
		}
	}
}


//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);

for($i = 0; $i < 24; $i++){
	if(isset($count_bots[$i])){
		$bot_tmp = $count_bots[$i];
	}else{
		$bot_tmp = 0;
	}
	
	if(isset($count_people[$i])){
		$people_tmp = $count_people[$i];
	}else{
		$people_tmp = 0;
	}
	
	$timestring = str_pad($i,2,"0",STR_PAD_LEFT).":00-".str_pad(($i+1),2,"0",STR_PAD_LEFT).":00";
	
	$timeadd = $xmlausgabe->createElement("time");
		$timeadd->setAttribute("period", $timestring);
		$timeadd->appendChild($xmlausgabe->createElement('bots', $bot_tmp));
		$timeadd->appendChild($xmlausgabe->createElement('people', $people_tmp));
	$root->appendChild($timeadd);
}


$gesamtadd = $xmlausgabe->createElement("total");
	$gesamtadd->appendChild($xmlausgabe->createElement('bots', $count_bots[24]));
	$gesamtadd->appendChild($xmlausgabe->createElement('people', $count_people[24]));
$root->appendChild($gesamtadd);

echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <time period="00:00-01:00">
//    <bots>3</bots>
//    <people>3</people>
//  </time>
//  <time period="01:00-02:00">
//    <bots>4</bots>
//    <people>1</people>
//  </time>
//  ...
//  <time period="17:00-18:00">
//    <bots>1</bots>
//    <people>6</people>
//  </time>
//  ...
//  <total>
//    <bots>15</bots>
//    <people>27</people>
//  </total>
//</list>

?>