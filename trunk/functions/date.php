<?php
	$monthnames = array(FLANG_JAN, FLANG_FEB, FLANG_MAR, FLANG_APR, FLANG_MAY, FLANG_JUN, FLANG_JUL, FLANG_AUG, FLANG_SEP, FLANG_OKT, FLANG_NOV, FLANG_DEC);

	if(isset($_GET['year'])){
		$show_year = preg_replace('#[^0-9]#i','',$_GET['year']);//alles ausser 0-9 mit nichts ersetzten
	}else{
		$show_year = gmdate("Y");
	}
	
	if(isset($_GET['month'])){
		$show_month = preg_replace('#[^0-9]#i','',$_GET['month']);//alles ausser 0-9 mit nichts ersetzten
		if(($show_month > 12) || ($show_month==0)){
			$show_month = gmdate("n");
		}
	}else{
		$show_month = gmdate("n");
	}
	
	$show_timestamp = gmmktime(1, 1, 1, $show_month, 1, $show_year);
	
	
	//lastupdate of uasdata
	if (file_exists($fstat_cache_dir."cache.ini")) {
		$cacheIni = parse_ini_file($fstat_cache_dir."cache.ini");
		$uasdata_date = date($fstat_date_format, $cacheIni['lastupdate']);
	}else{
		$uasdata_date = "";
	}
?>