<?php
header('Content-type: application/atom+xml');

error_reporting(0); //keine Fehler anzeigen
//error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
//error_reporting(E_ALL); // alle Fehler anzeigen


//The language class is special, it includes the language defines at init time of the first var
//therefor "new Language" must be called once!
require_once "./classes/display/language.php";
$lang = new Language(isset($_GET['lang']) ? $_GET['lang'] : null);

include "./config/settings.php";
include "./config/information.php";
include_once "./functions/main_include.php";//defines get_xml_backend


$tmp_month = gmdate("m");
$tmp_year = gmdate("Y");
if($tmp_month == 1){
	$tmp_month = 12;
	$tmp_year = $tmp_year - 1;
}else{
	$tmp_month = $tmp_month - 1;
}


function WriteLastMonth($filename){
	global $tmp_month, $tmp_year;
	
	$handle = @fopen($filename, "w");
	if($handle){
		fputs($handle, get_xml_backend("./backend/counter.php", $tmp_year, $tmp_month));
	}
	@fclose($handle);
}


$c_filename = $fstat_cache_dir."lastmonth.xml";

//check how old that file is
	
	
if(!is_file($c_filename)){
	//create that stuff
	WriteLastMonth($c_filename);
}else{
	$modtime = filemtime($c_filename);
	$mod_month = gmdate("m", $modtime);
	$mod_year = gmdate("Y", $modtime);
	
	if(($mod_month < gmdate("m") and $mod_year == gmdate("Y")) or ($mod_year < gmdate("Y"))){
		//re-create that stuff
		WriteLastMonth($c_filename);
	}
}

//is file now really there?
if(is_file($c_filename)){
	$xmlausgabe = new DOMDocument('1.0', 'UTF-8');
	//$xmlausgabe->xmlStandalone = true;
	$xmlausgabe->preserveWhiteSpace = false;
	$xmlausgabe->formatOutput = true;
	$root = $xmlausgabe->createElementNS("http://www.w3.org/2005/Atom", "feed");
	$root = $xmlausgabe->appendChild($root);
	
	
	//--------
	//ID
	$tmp = "tag:".$_SERVER['HTTP_HOST'].",2011:".trim(dirname($_SERVER['PHP_SELF']), '/\\');
	$root->appendChild($xmlausgabe->createElement("id", $tmp));
	//Title
	$tmp = "FStat Report";
	$root->appendChild($xmlausgabe->createElement("title", $tmp));
	//Subtitle
	$tmp = "FStat (Statistic) Feeds for ".$_SERVER['HTTP_HOST'];
	$root->appendChild($xmlausgabe->createElement("subtitle", $tmp));
	//Author
	$tmp = "FStat";
	$authornode = $xmlausgabe->createElement("author");
		$authornode->appendChild($xmlausgabe->createElement("name", $tmp));
	$root->appendChild($authornode);
	//Link Self
	$tmp = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$linknodeSelf = $xmlausgabe->createElement("link");
		$linknodeSelf->setAttribute("rel", "self");
		$linknodeSelf->setAttribute("href", $tmp);
	$root->appendChild($linknodeSelf);
	//Link
	$tmp = "http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']));
	$linknode = $xmlausgabe->createElement("link");
		$linknode->setAttribute("rel", "alternate");
		$linknode->setAttribute("href", $tmp);
	$root->appendChild($linknode);
	//Generator
	$tmp = "FStat";
	$generatornode = $xmlausgabe->createElement("generator", $tmp);
		$generatornode->setAttribute("version", $fstat_fstat_version);
	$root->appendChild($generatornode);
	//updated
	$tmp = gmdate("c", gmmktime(0, 0, 0, date("n"), 1, date("Y")));
	$root->appendChild($xmlausgabe->createElement("updated", $tmp));
	
	
	//Entry
	$entry_root = $root->appendChild($xmlausgabe->createElement("entry"));
	//------
	//EntryID
	$tmp = "tag:".$_SERVER['HTTP_HOST'].",".$tmp_year."-".str_pad($tmp_month,2,"0",STR_PAD_LEFT).":".trim(dirname($_SERVER['PHP_SELF']), '/\\')."#entry";
	$entry_root->appendChild($xmlausgabe->createElement("id", $tmp));
	//Title
	$tmp = htmlspecialchars(html_entity_decode(FLANG_H_STATFOR." ".$lang->monthnames[$tmp_month-1]." ".$tmp_year, ENT_COMPAT, "UTF-8"));
	$titlenode = $xmlausgabe->createElement("title", $tmp);
		$titlenode->setAttribute("type", "html");
	$entry_root->appendChild($titlenode);
	//updated
	$tmp = gmdate("c", filemtime($c_filename));
	$entry_root->appendChild($xmlausgabe->createElement("updated", $tmp));
	//link
	$tmp = "http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\')."/?site=overview&amp;year=".$tmp_year."&amp;month=".$tmp_month;
	$linknode2 = $xmlausgabe->createElement("link");
		$linknode2->setAttribute("rel", "alternate");
		$linknode2->setAttribute("href", $tmp);
	$entry_root->appendChild($linknode2);
	//summary
		$tmp_desc = "";
		$xmldoc = new DOMDocument();
		$xmldoc->load($c_filename);
		
		$node = $xmldoc->getElementsByTagName("counter")->item(0)->getElementsByTagName("total")->item(0);
		$count_bots = $node->getElementsByTagName("bots")->item(0)->nodeValue;
		$count_people = $node->getElementsByTagName("people")->item(0)->nodeValue;
		
		$tmp_desc .= FLANG_VISITOR_L.": ".$count_people."<br>";
		$tmp_desc .= FLANG_BOT_L.": ".$count_bots."<br>";
		
		$tmp_desc = htmlspecialchars($tmp_desc);
	$summarynode = $xmlausgabe->createElement("summary", $tmp_desc);
		$summarynode->setAttribute("type", "html");
	$entry_root->appendChild($summarynode);
	
	
	echo $xmlausgabe->saveXML();
}

	

?>