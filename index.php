<?php
	error_reporting(0); //keine Fehler anzeigen
	//error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
	//error_reporting(E_ALL); // alle Fehler anzeigen
	$startzeit = explode(" ", microtime());
	$startzeit = $startzeit[0]+$startzeit[1];
	
        require_once "./classes/country.php";
        require_once "./classes/displayTime.php";
        
        
	include "./config/settings.php";
	include "./config/lang.php";
	include "./config/information.php";
        
        $displayTime = new DisplayTime();
        $displayTime->setUnsafeStartDate(isset($_GET['year']) ? $_GET['year'] : null, isset($_GET['month']) ? $_GET['month'] : null);
        $displayTime->setUnsafeEndDate(isset($_GET['year']) ? $_GET['year'] : null, isset($_GET['month']) ? $_GET['month'] : null);
        
	include_once "./functions/main_include.php";//defines $show_cat and needs $displayTime variable
        
        
        //TODO nur zum Übergang hier:
        $monthnames = array(FLANG_JAN, FLANG_FEB, FLANG_MAR, FLANG_APR, FLANG_MAY, FLANG_JUN, FLANG_JUL, FLANG_AUG, FLANG_SEP, FLANG_OKT, FLANG_NOV, FLANG_DEC);
	
        
        
	if(isset($_GET['length'])){
		$fstat_last_length = preg_replace('#[^0-9]#i','',$_GET['length']);//alles ausser 0-9 mit nichts ersetzten
		if($fstat_last_length > 200){$fstat_last_length = 200;}//Schutz vor zu langen Listen
	}
	
	
	switch ($show_cat){
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
			$fstat_title = FLANG_H_STATFOR." ".$monthnames[$displayTime->getStartMonth()-1]." ".$displayTime->getStartYear();
			break;
	}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
	
	switch ($show_cat){
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