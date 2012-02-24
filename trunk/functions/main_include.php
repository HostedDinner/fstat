<?php
if(isset($_GET['show'])){
	$show_cat = preg_replace('#[^0-9a-z]#i','',$_GET['show']);//alles ausser 0-9 und A-Z mit nichts ersetzten
}else{
	$show_cat = "overview";
}

function get_xml_backend($filename, $year = 0, $month = 0){
    if (is_file($filename)){
	
        if($year != 0) {$fstat_backend_year  = $year;}
		if($month != 0){$fstat_backend_month = $month;}
		
		$is_include = true;
		ob_start();
			include $filename;
			$contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }else{
		return false;
	}
}


function LookupLang($short){
	switch ($short){
		case "de": return "Deutsch";
		case "en": return "English";
		case "hu": return "Magyar";
		default: return $short;
	}
}



function URL_AddLang($newlang = NULL){
	global $show_lang;
	if ($newlang === NULL){
		$newlang = $show_lang;
	}
	return "&amp;lang=".$newlang;
}

function URL_AddMonth($newmonth = NULL){
	global $show_month;
	if ($newmonth === NULL){
		$newmonth = $show_month;
	}
	return "&amp;month=".$newmonth;
}

function URL_AddYear($newyear = NULL){
	global $show_year;
	if ($newyear === NULL){
		$newyear = $show_year;
	}
	return "&amp;year=".$newyear;
}

//the only without &amp; in front
function URL_AddShow($newcat = NULL){
	global $show_cat;
	if ($newcat === NULL){
		$newcat = $show_cat;
	}
	return "show=".$newcat;
}

?>