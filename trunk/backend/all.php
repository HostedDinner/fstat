<?php
if(!isset($is_include)){
	if(!headers_sent()){
		header('Content-type: text/xml');
	}
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


$all_show_br  = false; //Browser
$all_show_os  = false; //OS
$all_show_bot = false; //Bots
$all_show_ref = false; //Referer
$all_show_key = false; //Search
$all_show_cou = false; //Country
$all_show_cot = false; //Counter
$all_show_tim = false; //Time

$all_shows = explode("|", $fstat_backend_get_list);

foreach($all_shows as $show){
	$show = trim($show);
	
	switch($show){
		case "br":
		case "browser":
			$all_show_br  = true;
			break;
		case "os":
			$all_show_os  = true;
			break;
		case "bot":
		case "bots":
			$all_show_bot = true;
			break;
		case "ref":
		case "referer":
			$all_show_ref = true;
			break;
		case "key":
		case "search":
			$all_show_key = true;
			break;
		case "cou":
		case "country":
			$all_show_cou = true;
			break;
		case "cot":
		case "counter":
			$all_show_cot = true;
			break;
		case "tim":
		case "time":
			$all_show_tim = true;
			break;
		case "all":
			$all_show_br  = true;
			$all_show_os  = true;
			$all_show_bot = true;
			$all_show_ref = true;
			$all_show_key = true;
			$all_show_cou = true;
			$all_show_cot = true;
			$all_show_tim = true;
			break;
	}
}


if($all_show_br  == true){$br_arr = array();}
if($all_show_os  == true){$os_arr = array();}
if($all_show_bot == true){$bot_arr = array();}
if($all_show_ref == true){$ref_arr = array();}
if($all_show_key == true){$key_arr = array();}
if($all_show_cou == true){$cou_arr = array();}
if($all_show_cot == true){
	$cot_arr = array();
	$cot_arr['all']['bots'] = 0;
	$cot_arr['all']['people'] = 0;
}
if($all_show_tim == true){
	$tim_arr = array();
	for($i = 0; $i < 24; $i++){
		$tim_arr[str_pad($i,2,"0",STR_PAD_LEFT).":00-".str_pad(($i+1),2,"0",STR_PAD_LEFT).":00"]['bots'] = 0;
		$tim_arr[str_pad($i,2,"0",STR_PAD_LEFT).":00-".str_pad(($i+1),2,"0",STR_PAD_LEFT).":00"]['people'] = 0;
	}
	$tim_arr['all']['bots'] = 0;
	$tim_arr['all']['people'] = 0;
}

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
			
			if($all_show_cot == true){
				$cot_arr[$y."-".$m_pad."-".$i_pad]['bots'] = 0;
				$cot_arr[$y."-".$m_pad."-".$i_pad]['people'] = 0;
			}
			
			if(is_file($filename)){
				$xmldoc = new DOMDocument();
				$xmldoc->load($filename);
				
				$nodelist = $xmldoc->getElementsByTagName("visitor");
				
				foreach($nodelist as $visitor){
					$typ = @$visitor->getElementsByTagName("typ")->item(0)->nodeValue;
					if(($fstat_show_bots_as_visitors) or ($typ != "Robot" and $typ != "Validator")){
						if($all_show_br  == true){FamAndSub_Build($visitor, "u", $br_arr);}
						if($all_show_os  == true){FamAndSub_Build($visitor, "o", $os_arr);}
						
						if($all_show_ref == true){Normal_Build($visitor, "rdom", $ref_arr);}
						if($all_show_key == true){Normal_Build($visitor, "rkey", $key_arr);}
						if($all_show_cou == true){Normal_Build($visitor, "ucon", $cou_arr, "ucoi", true);}
						
						if($all_show_cot == true){
							$cot_arr[$y."-".$m_pad."-".$i_pad]['people'] = $cot_arr[$y."-".$m_pad."-".$i_pad]['people'] + 1;
							$cot_arr['all']['people'] = $cot_arr['all']['people'] + 1;
						}
					}else{
						if($all_show_bot == true){FamAndSub_Build($visitor, "u", $bot_arr);}
						
						if($all_show_cot == true){
							$cot_arr[$y."-".$m_pad."-".$i_pad]['bots'] = $cot_arr[$y."-".$m_pad."-".$i_pad]['bots'] + 1;
							$cot_arr['all']['bots'] = $cot_arr['all']['bots'] + 1;
						}
					}
					if($all_show_tim == true){Time_Build($visitor, $tim_arr, $typ);}
				}
				
			}
		}
		$m++;
		if($y == gmdate("Y", $fstat_backend_end_timestamp) and $m > gmdate("n", $fstat_backend_end_timestamp)){
			$m = 13;//just bigger than 12
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


if($all_show_br  == true){
	FamAndSub_Sort($br_arr);
	$br_root  = $root->appendChild($xmlausgabe->createElement("browser"));
	FamAndSub_DOM($xmlausgabe, $br_root, $br_arr);
}

if($all_show_os  == true){
	FamAndSub_Sort($os_arr);
	$os_root  = $root->appendChild($xmlausgabe->createElement("os"));
	FamAndSub_DOM($xmlausgabe, $os_root, $os_arr);
}

if($all_show_bot == true){
	FamAndSub_Sort($bot_arr);
	$bot_root = $root->appendChild($xmlausgabe->createElement("bot"));
	FamAndSub_DOM($xmlausgabe, $bot_root, $bot_arr);
}

if($all_show_ref == true){
	Normal_Sort($ref_arr);
	$ref_root = $root->appendChild($xmlausgabe->createElement("referer"));
	Normal_DOM($xmlausgabe, $ref_root, $ref_arr, "ref", "domain");
}

if($all_show_key == true){
	Normal_Sort($key_arr);
	$sea_root = $root->appendChild($xmlausgabe->createElement("search"));
	Normal_DOM($xmlausgabe, $sea_root, $key_arr, "ref", "keywords");
}

if($all_show_cou == true){
	Normal_Sort($cou_arr);
	$cou_root = $root->appendChild($xmlausgabe->createElement("country"));
	Normal_DOM($xmlausgabe, $cou_root, $cou_arr, "cou", "name", true);
}

if($all_show_cot == true){
	$cot_root = $root->appendChild($xmlausgabe->createElement("counter"));
	BotPeople_DOM($xmlausgabe, $cot_root, $cot_arr, "day", "id");
}

if($all_show_tim == true){
	BotPeople_Sort($tim_arr);
	$tim_root = $root->appendChild($xmlausgabe->createElement("times"));
	BotPeople_DOM($xmlausgabe, $tim_root, $tim_arr, "time", "period");
}

echo $xmlausgabe->saveXML();
?>