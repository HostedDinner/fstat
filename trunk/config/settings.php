<?php
	date_default_timezone_set('Europe/Berlin');
	
	$fstat_default_lang = "de";//like in lang dir
	
	$fstat_cache_dir = "./cache/";
	$fstat_data_dir = "./stat_data/";
	$fstat_ico_dir = "./icons/";
	
	$fstat_new_user = 14400;//in sec: 4 Hours
	$fstat_date_format = "j.n.Y";//or maybe "Y-n-j"
	$fstat_time_format = "H:m:s";
	
	$fstat_use_site_var = true;
	$fstat_site_variable = "site,file";//seperate with comma, for use with multiple site var sources
	$fstat_default_site_name = "start";
	
	$fstat_last_length = 50;
	
	$fstat_show_bots_as_visitors = false;
	
	$fstat_update_interval = 604800;//in sec: 7 Days
?>