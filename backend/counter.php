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

$count_bots = 0;
$count_people = 0;

//Ausgabe vorbereiten:
$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);


for($i = 1; $i <= gmdate("t", $fstat_backend_timestamp); $i++){
	$filename = $prefolder.$fstat_data_dir."stat/".$fstat_backend_year."/".str_pad($fstat_backend_month,2,"0",STR_PAD_LEFT)."/".str_pad($i,2,"0",STR_PAD_LEFT).".xml";

	$tmp_count_bots = 0;
	$tmp_count_people = 0;
	
	if(is_file($filename)){
		$xmldoc = new DOMDocument();
		$xmldoc->load($filename);
		
		$xpath = new  DOMXPath($xmldoc);
		
		//Robots:
		$query = 'count(//typ[.="Robot"])';
		$tmp_count_bots = $xpath->evaluate($query, $xmldoc);
		$count_bots = $count_bots + $tmp_count_bots;
		
		//People
		$query = 'count(//typ[.!="Robot"])';
		$tmp_count_people = $xpath->evaluate($query, $xmldoc);
		$count_people = $count_people + $tmp_count_people;
		
	}
	
	$dayadd = $xmlausgabe->createElement("day");
		$dayadd->setAttribute("id", $i);
		$dayadd->appendChild($xmlausgabe->createElement('bots', $tmp_count_bots));
		$dayadd->appendChild($xmlausgabe->createElement('people', $tmp_count_people));
	
	$root->appendChild($dayadd);
}


$gesamtadd = $xmlausgabe->createElement("total");
	$gesamtadd->appendChild($xmlausgabe->createElement('bots', $count_bots));
	$gesamtadd->appendChild($xmlausgabe->createElement('people', $count_people));

$root->appendChild($gesamtadd);

echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list>
//  <day id="1">
//    <bots>3</bots>
//    <people>3</people>
//  </day>
//  <day id="2">
//    <bots>4</bots>
//    <people>1</people>
//  </day>
//  ...
//  <total>
//    <bots>15</bots>
//    <people>27</people>
//  </total>
//</list>

?>