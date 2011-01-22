<?php
	$monthnames = array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

	if(isset($_GET['year'])){
		$show_year = preg_replace('#[^0-9]#i','',$_GET['year']);//alles ausser 0-9 mit nichts ersetzten
	}else{
		$show_year = date("Y");
	}
	
	if(isset($_GET['month'])){
		$show_month = preg_replace('#[^0-9]#i','',$_GET['month']);//alles ausser 0-9 mit nichts ersetzten
		if(($show_month > 12) || ($show_month==0)){
			$show_month = date("m");
		}
	}else{
		$show_month = date("m");
	}
	
	$show_timestamp = mktime(1, 1, 1, $show_month, 1, $show_year);
?>