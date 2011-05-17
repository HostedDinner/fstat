<?php
	error_reporting(0); //keine Fehler anzeigen
	//error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
	//error_reporting(E_ALL); // alle Fehler anzeigen
	include "./config/settings.php";
	include "./config/lang.php";
	include "./config/information.php";
	include_once "./functions/date.php";//defines $monthnames, $show_year, $show_month, $show_timestamp
	include_once "./functions/main_include.php";//defines $show_cat and needs date.php
	
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
		case "overview":
		default:
			$fstat_title = FLANG_H_STATFOR." ".$monthnames[$show_month-1]." ".$show_year;
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