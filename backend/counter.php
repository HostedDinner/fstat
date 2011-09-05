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

$count_bots = 0;
$count_people = 0;

//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);

//extra kind of root for Counter
$cot_root = $xmlausgabe->createElement("counter");
$cot_root = $root->appendChild($cot_root);

for($y = gmdate("Y", $fstat_backend_start_timestamp); $y <= gmdate("Y", $fstat_backend_end_timestamp); $y++){
	if($y == gmdate("Y", $fstat_backend_start_timestamp)){
		$m = gmdate("n", $fstat_backend_start_timestamp);
	}else{
		$m = 1;
	}
	while($m <= 12){
		for($i = 1; $i <= 31; $i++){//exact days of month would be only slightly faster
			$filename = $prefolder.$fstat_data_dir."stat/".$y."/".str_pad($m,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";
		
			$tmp_count_bots = 0;
			$tmp_count_bots2 = 0;
			$tmp_count_people = 0;
			
			if(is_file($filename)){
				$xmldoc = new DOMDocument();
				$xmldoc->load($filename);
				
				$xpath = new  DOMXPath($xmldoc);
				
				//Robots:
				$query = 'count(//typ[.="Robot"])';
				$tmp_count_bots = $xpath->evaluate($query, $xmldoc);
				$count_bots = $count_bots + $tmp_count_bots;
				$query = 'count(//typ[.="Validator"])';
				$tmp_count_bots2 = $xpath->evaluate($query, $xmldoc);
				$count_bots = $count_bots + $tmp_count_bots2;
				
				//People
				$query = 'count(//visitor)';
				$tmp_count = $xpath->evaluate($query, $xmldoc);
				$tmp_count_people = $tmp_count - $tmp_count_bots - $tmp_count_bots2;
				$count_people = $count_people + $tmp_count_people;
				
			}
			
			$dayadd = $xmlausgabe->createElement("day");
				$dayadd->setAttribute("id", $i);
				$dayadd->appendChild($xmlausgabe->createElement('bots', ($tmp_count_bots + $tmp_count_bots2)));
				$dayadd->appendChild($xmlausgabe->createElement('people', $tmp_count_people));
			
			$cot_root->appendChild($dayadd);
		}
		$m++;
		if($y == gmdate("Y", $fstat_backend_end_timestamp) and $m > gmdate("n", $fstat_backend_end_timestamp)){
			$m = 13;
		}
	}
}


$gesamtadd = $xmlausgabe->createElement("total");
	$gesamtadd->appendChild($xmlausgabe->createElement('bots', $count_bots));
	$gesamtadd->appendChild($xmlausgabe->createElement('people', $count_people));

$cot_root->appendChild($gesamtadd);

echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <counter>
//    <day id="1">
//      <bots>3</bots>
//      <people>3</people>
//    </day>
//    <day id="2">
//      <bots>4</bots>
//      <people>1</people>
//    </day>
//    ...
//    <total>
//      <bots>15</bots>
//      <people>27</people>
//    </total>
//  </counter>
//</list>

?>