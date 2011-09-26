<?php
	if(!defined(FSTAT_PATH)){
		define(FSTAT_PATH, "./");
	}
	include FSTAT_PATH."config/settings.php";
	
	include FSTAT_PATH."functions/referparser.php";
	include FSTAT_PATH."functions/countryparser.php";
	require FSTAT_PATH."functions/UASparser.php";
	
	// Creates a new UASparser object and set cache dir (this php script must right write to cache dir)
	$parser = new UASparser();
	$parser->SetCacheDir(FSTAT_PATH.$fstat_cache_dir);
	$parser->updateInterval = $fstat_update_interval;
	
	function CheckDir($dirname){
		if(!is_dir($dirname)){
			if(mkdir($dirname) == false){
				echo "Konnte Verzeichnis ".$dirname." f&uuml;r F-Stat nicht &ouml;ffnen/erstellen!";
				return false;
			}
		}
		return true;
	}
	
	if(CheckDir(FSTAT_PATH.$fstat_data_dir) == false){
		return 0;
		exit;//zur Absicherung
	}
	if(CheckDir(FSTAT_PATH.$fstat_cache_dir."ip/") == false){
		return 0;
		exit;//zur Absicherung
	}
	
	$year = gmdate("Y");
	$month = gmdate("m");
	$day = gmdate("d");
	
	$is_new = true;
	$ip = getenv("REMOTE_ADDR");
	
	//"Alte" Dateien im IP.Verzeichniss loeschen:
	//+ gleichzeitig neu finden
	$ipdir = opendir(FSTAT_PATH.$fstat_cache_dir."ip/");
	while (($file = readdir($ipdir)) !== FALSE){
		if((substr($file, -3) == ".ip")){
			//$file;
			$f_cont = @fopen(FSTAT_PATH.$fstat_cache_dir."ip/".$file,'r');
			$timestamp = trim(fgets($f_cont));//erste Zeile Timestamp
			
			//loeschen, wenn zu alt
			if($timestamp < (time() - $fstat_new_user)){
				fclose($f_cont);
				unlink(FSTAT_PATH.$fstat_cache_dir."ip/".$file);
				continue;
			//wenn ip übereinstimmt
			}elseif($file == $ip.".ip"){
				$user_timestamp = $timestamp;
				$ua = trim(fgets($f_cont));//zweite zeile UA
				$browser_typ = trim(fgets($f_cont));//dritte Zeile Typ (Browser, Bot, ...)
				fclose($f_cont);
				if($ua == $_SERVER['HTTP_USER_AGENT']){//und wenn der Useragent stimmt:
					$is_new = false;
				}
			}else{
				fclose($f_cont);
			}
		}
	}
	closedir($ipdir);
	
	if($is_new == true){
	//Daten auswerten
		$user_timestamp = time();
		
		if(CheckDir(FSTAT_PATH.$fstat_data_dir."stat") == false){
			return 0;
			exit;//zur Absicherung
		}
		if(CheckDir(FSTAT_PATH.$fstat_data_dir."stat/".$year) == false){
			return 0;
			exit;//zur Absicherung
		}
		if(CheckDir(FSTAT_PATH.$fstat_data_dir."stat/".$year."/".str_pad($month,2,"0",STR_PAD_LEFT)) == false){
			return 0;
			exit;//zur Absicherung
		}//fragt nicht warum man das nicht in einem machen kann
		
		//User Agent Parser
		$uaa = $parser->Parse();
		//ReferParser
		$ra = ReferParse();
		//Country Parser
		$country = CountryParse();
	
	//Daten in XML schreiben:
	
		$tmp_filename = FSTAT_PATH.$fstat_data_dir."stat/".$year."/".str_pad($month,2,"0",STR_PAD_LEFT)."/".$day.".xml";
		
		if(file_exists($tmp_filename)){
			$xmldoc = new DOMDocument();
			$xmldoc->preserveWhiteSpace = false;
			$xmldoc->formatOutput = true;
			$xmldoc->load($tmp_filename);
			$root = $xmldoc->documentElement;
		}else{
			$xmldoc = new DOMDocument('1.0', 'UTF-8');
			$xmldoc->xmlStandalone = true;
			$xmldoc->preserveWhiteSpace = false;
			$xmldoc->formatOutput = true;
			$root = $xmldoc->createElement("list");
			$root = $xmldoc->appendChild($root);
		}
			
		$newvisitor = $xmldoc->createElement("visitor");
			$newvisitor->appendChild($xmldoc->createElement('typ', htmlspecialchars($uaa['typ'])));
			$newvisitor->appendChild($xmldoc->createElement('uas', htmlspecialchars($_SERVER['HTTP_USER_AGENT'])));
			$newvisitor->appendChild($xmldoc->createElement('uip', htmlspecialchars($ip)));
			$newvisitor->appendChild($xmldoc->createElement('uhost', htmlspecialchars(gethostbyaddr($ip))));
			$newvisitor->appendChild($xmldoc->createElement('uti', $user_timestamp));
			$newvisitor->appendChild($xmldoc->createElement('ufam', htmlspecialchars($uaa['ua_family'])));
			$newvisitor->appendChild($xmldoc->createElement('unam', htmlspecialchars($uaa['ua_name'])));
			$newvisitor->appendChild($xmldoc->createElement('uico', htmlspecialchars($uaa['ua_icon'])));
			$newvisitor->appendChild($xmldoc->createElement('ofam', htmlspecialchars($uaa['os_family'])));
			$newvisitor->appendChild($xmldoc->createElement('onam', htmlspecialchars($uaa['os_name'])));
			$newvisitor->appendChild($xmldoc->createElement('oico', htmlspecialchars($uaa['os_icon'])));
			$newvisitor->appendChild($xmldoc->createElement('ucoi', htmlspecialchars($country['icon'])));
			$newvisitor->appendChild($xmldoc->createElement('ucon', htmlspecialchars($country['name'])));
			$newvisitor->appendChild($xmldoc->createElement('rkey', htmlspecialchars($ra['searchkeys'])));
			$newvisitor->appendChild($xmldoc->createElement('rdom', htmlspecialchars($ra['domain'])));
		$root->appendChild($newvisitor);
		
		$xmldoc->formatOutput = true;
		$xmldoc->save($tmp_filename, LIBXML_NOEMPTYTAG);
		
		//Browser Typ ist bis jetzt nicht deklariert
		$browser_typ = $uaa['typ'];
		
	//IP Cache schreiben:
		$f_cont = @fopen(FSTAT_PATH.$fstat_cache_dir."ip/".$ip.".ip", 'w');
		$tmp = $user_timestamp."\n".$_SERVER['HTTP_USER_AGENT']."\n".$browser_typ;
		fputs($f_cont, $tmp);
		fclose($f_cont);
	}
	//Daten ausgewertet...
	
	//Pfade notieren
	if(CheckDir(FSTAT_PATH.$fstat_data_dir."paths") == false){
		return 0;
		exit;//zur Absicherung
	}
	if(CheckDir(FSTAT_PATH.$fstat_data_dir."paths/".$year) == false){
		return 0;
		exit;//zur Absicherung
	}
	if(CheckDir(FSTAT_PATH.$fstat_data_dir."paths/".$year."/".str_pad($month,2,"0",STR_PAD_LEFT)) == false){
		return 0;
		exit;//zur Absicherung
	}
	if($browser_typ == "Robot" or $browser_typ == "Validator"){
		$f_cont = @fopen(FSTAT_PATH.$fstat_data_dir."paths/".$year."/".str_pad($month,2,"0",STR_PAD_LEFT)."/bot_".$ip."_".gmdate("d_H",$user_timestamp).".path", 'a');
	}else{
		$f_cont = @fopen(FSTAT_PATH.$fstat_data_dir."paths/".$year."/".str_pad($month,2,"0",STR_PAD_LEFT)."/".$ip."_".gmdate("d_H",$user_timestamp).".path", 'a');
	}
	
	if($fstat_use_site_var){
		$site_name = $fstat_default_site_name;//default
		
		$tmp_sitevar_array = explode(",", $fstat_site_variable);
		foreach($tmp_sitevar_array as $tmp_sitevar){
			$tmp_sitevar = trim($tmp_sitevar);
			if(isset($_GET[$tmp_sitevar])){
				$site_name = htmlspecialchars($_GET[$tmp_sitevar]);
				$site_name = str_replace("|", "&#124;", $site_name); //to prevent corruption of table ;)
				break;//use first one from config
			}
		}
		fputs($f_cont, time()."|".basename($_SERVER['SCRIPT_FILENAME'])."|".$site_name."\n");
	}else{
		fputs($f_cont, time()."|".basename($_SERVER['SCRIPT_FILENAME'])."\n");
	}
	fclose($f_cont);
	//Pfade notiert
?>