<?php
	error_reporting(0); //keine Fehler anzeigen
	//error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
	error_reporting(E_ALL); // alle Fehler anzeigen
        
	$startzeit = explode(" ", microtime());
	$startzeit = $startzeit[0]+$startzeit[1];
	
        //The language class is special, it includes the language defines at init time of the first var
        //therefor "new Language" must be called once!
        require_once "./classes/display/language.php";
        $lang = new Language(isset($_GET['lang']) ? $_GET['lang'] : null);
        
        //including necassary classes
        require_once "./classes/analyse/country.php";
        require_once "./classes/display/displayTime.php";
        require_once "./classes/display/urlbuilder.php";
        
	include_once "./config/settings.php";
	include "./config/information.php";
        include_once "./functions/main_include.php";//defines get_xml_backend
        
        
        //instances of the classes
        $displayTime = new DisplayTime();
        $displayTime->setUnsafeStartDate(isset($_GET['year']) ? $_GET['year'] : null, isset($_GET['month']) ? $_GET['month'] : null);
        $displayTime->setUnsafeEndDate(isset($_GET['year']) ? $_GET['year'] : null, isset($_GET['month']) ? $_GET['month'] : null);
        
        $urlBuilder = new URLBuilder(isset($_GET['show']) ? $_GET['show'] : null, $lang->getLanguage());
        
	//TODO: pack in some class?
	if(isset($_GET['length'])){
		$fstat_last_length = preg_replace('#[^0-9]#i','',$_GET['length']);//alles ausser 0-9 mit nichts ersetzten
		if($fstat_last_length > 200){$fstat_last_length = 200;}//Schutz vor zu langen Listen
	}
	
	
        //set Page title
	switch ($urlBuilder->getPage()){
		case "about":
			$fstat_title = FLANG_H_ABOUT_FSTAT;
			break;
		case "last":
			$fstat_title = FLANG_H_LAST." ".$fstat_last_length." ".FLANG_VISITOR_L;
			break;
		case "lastbots":
			$fstat_title = FLANG_H_LAST." ".$fstat_last_length." ".FLANG_BOT_L;
			break;
		case "overview":
		default:
			$fstat_title = FLANG_H_STATFOR." ".$lang->monthnames[$displayTime->getStartMonth()-1]." ".$displayTime->getStartYear();
			break;
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<?php
	include("./elements/layout_header.php");
?>
</head>
<body>
<div id="container">
<?php
	include("./elements/layout_menubar.php");
	
        //set Page content
	switch ($urlBuilder->getPage()){
		case "about":
			include("./elements/about.php");
			break;
		case "last":
			include("./elements/last.php");
			break;
		case "lastbots":
			include("./elements/lastbots.php");
			break;
		case "overview":
		default:
			include("./elements/overview.php");
			break;
	}

include("./elements/layout_footer.php");
?>
</div>
</body>
</html>