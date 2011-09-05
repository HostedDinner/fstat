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
include $prefolder."functions/backend_functions.php";

$br_arr = array();
$os_arr = array();
$bot_arr = array();
$ref_arr = array();
$key_arr = array();
$cou_arr = array();
$cot_arr = array();
	$cot_arr['all']['bots'] = 0;
	$cot_arr['all']['people'] = 0;
$tim_arr = array();
	for($i = 0; $i < 24; $i++){
		$tim_arr[str_pad($i,2,"0",STR_PAD_LEFT).":00-".str_pad(($i+1),2,"0",STR_PAD_LEFT).":00"]['bots'] = 0;
		$tim_arr[str_pad($i,2,"0",STR_PAD_LEFT).":00-".str_pad(($i+1),2,"0",STR_PAD_LEFT).":00"]['people'] = 0;
	}
	$tim_arr['all']['bots'] = 0;
	$tim_arr['all']['people'] = 0;

for($y = gmdate("Y", $fstat_backend_start_timestamp); $y <= gmdate("Y", $fstat_backend_end_timestamp); $y++){
	if($y == gmdate("Y", $fstat_backend_start_timestamp)){
		$m = gmdate("n", $fstat_backend_start_timestamp);
	}else{
		$m = 1;
	}
	while($m <= 12){
		$m_pad = str_pad($m,2,"0",STR_PAD_LEFT);
		$timestr_m_start = gmmktime(0, 0, 0, $m, 1, $y);
		
		for($i = 1; $i <= gmdate("t", $timestr_m_start); $i++){
			$i_pad = str_pad($i,2,"0",STR_PAD_LEFT);
			
			$filename = $prefolder.$fstat_data_dir."stat/".$y."/".$m_pad."/".$i_pad.".xml";
			
			$cot_arr[$y."-".$m_pad."-".$i_pad]['bots'] = 0;
			$cot_arr[$y."-".$m_pad."-".$i_pad]['people'] = 0;
			
			if(is_file($filename)){
				$xmldoc = new DOMDocument();
				$xmldoc->load($filename);
				
				$nodelist = $xmldoc->getElementsByTagName("visitor");
				
				foreach($nodelist as $visitor){
					$typ = @$visitor->getElementsByTagName("typ")->item(0)->nodeValue;
					if(($fstat_show_bots_as_visitors) or ($typ != "Robot" and $typ != "Validator")){
						FamAndSub_Build($visitor, "u", $br_arr);
						FamAndSub_Build($visitor, "o", $os_arr);
						
						Normal_Build($visitor, "rdom", $ref_arr);
						Normal_Build($visitor, "rkey", $key_arr);
						Normal_Build($visitor, "ucon", $cou_arr, "ucoi", true);
						
						$cot_arr[$y."-".$m_pad."-".$i_pad]['people'] = $cot_arr[$y."-".$m_pad."-".$i_pad]['people'] + 1;
						$cot_arr['all']['people'] = $cot_arr['all']['people'] + 1;
					}else{
						FamAndSub_Build($visitor, "u", $bot_arr);
						
						$cot_arr[$y."-".$m_pad."-".$i_pad]['bots'] = $cot_arr[$y."-".$m_pad."-".$i_pad]['bots'] + 1;
						$cot_arr['all']['bots'] = $cot_arr['all']['bots'] + 1;
					}
					Time_Build($visitor, $tim_arr, $typ);
				}
				
			}
		}
		$m++;
		if($y == gmdate("Y", $fstat_backend_end_timestamp) and $m > gmdate("n", $fstat_backend_end_timestamp)){
			$m = 13;//just bigger than 12
		}
	}
}


FamAndSub_Sort($br_arr);
FamAndSub_Sort($os_arr);
FamAndSub_Sort($bot_arr);
Normal_Sort($ref_arr);
Normal_Sort($key_arr);
Normal_Sort($cou_arr);
BotPeople_Sort($tim_arr);

//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);

//extra kind of roots ("subroots" :D )
$br_root  = $root->appendChild($xmlausgabe->createElement("browser"));
$os_root  = $root->appendChild($xmlausgabe->createElement("os"));
$bot_root = $root->appendChild($xmlausgabe->createElement("bot"));
$ref_root = $root->appendChild($xmlausgabe->createElement("referer"));
$sea_root = $root->appendChild($xmlausgabe->createElement("search"));
$cou_root = $root->appendChild($xmlausgabe->createElement("country"));
$cot_root = $root->appendChild($xmlausgabe->createElement("counter"));
$tim_root = $root->appendChild($xmlausgabe->createElement("times"));

//adding to root
FamAndSub_DOM($xmlausgabe, $br_root, $br_arr);
FamAndSub_DOM($xmlausgabe, $os_root, $os_arr);
FamAndSub_DOM($xmlausgabe, $bot_root, $bot_arr);
   Normal_DOM($xmlausgabe, $ref_root, $ref_arr, "ref", "domain");
   Normal_DOM($xmlausgabe, $sea_root, $key_arr, "ref", "keywords");
   Normal_DOM($xmlausgabe, $cou_root, $cou_arr, "cou", "name", true);
BotPeople_DOM($xmlausgabe, $cot_root, $cot_arr, "day", "id");
BotPeople_DOM($xmlausgabe, $tim_root, $tim_arr, "time", "period");

echo $xmlausgabe->saveXML();

?>