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
	
	$year = date("Y");
	$month = date("m");
	$day = date("d");
	
	//"Alte" Dateien im IP.Verzeichniss loeschen:
	$ipdir = opendir(FSTAT_PATH.$fstat_cache_dir."ip/");
	while (($file = readdir($ipdir)) !== FALSE){
		if((substr($file, -3) == ".ip")){
			//$file;
			$f_cont = @fopen(FSTAT_PATH.$fstat_cache_dir."ip/".$file,'r');
			$timestamp = trim(fgets($f_cont));//erste Zeile Timestamp
			fclose($f_cont);
			//loeschen, wenn zu alt
			if($timestamp < time() - $fstat_new_user){
				unlink(FSTAT_PATH.$fstat_cache_dir."ip/".$file);
			}
		}
	}
	closedir($ipdir);
	//fertig mit loeschen
	
	//IP lesen und evt vorhandene finden
	$is_new = true;
	$ip = getenv("REMOTE_ADDR");
	
	$ipdir = opendir(FSTAT_PATH.$fstat_cache_dir."ip/");
	while (($file = readdir($ipdir)) !== FALSE){
		if((substr($file, -3) == ".ip")){
			//$file;
			if($file == $ip.".ip"){//wenn die Ip ubereinstimmt
				$f_cont = @fopen(FSTAT_PATH.$fstat_cache_dir."ip/".$file,'r');
				$user_timestamp = trim(fgets($f_cont));//erste Zeile Timestamp, nicht von belang, weiter unten erst
				$ua = trim(fgets($f_cont));//zweite zeile UA
				$browser_typ = trim(fgets($f_cont));//dritte Zeile Typ (Browser, Bot, ...) ab V1.0
				fclose($f_cont);
				if($ua == $_SERVER['HTTP_USER_AGENT']){//und wenn der Useragent stimmt:
					$is_new = false;
				}
				break;
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
	if($browser_typ == "Robot"){
		$f_cont = @fopen(FSTAT_PATH.$fstat_data_dir."paths/".$year."/".str_pad($month,2,"0",STR_PAD_LEFT)."/bot_".$ip."_".date("d_H",$user_timestamp).".path", 'a');
	}else{
		$f_cont = @fopen(FSTAT_PATH.$fstat_data_dir."paths/".$year."/".str_pad($month,2,"0",STR_PAD_LEFT)."/".$ip."_".date("d_H",$user_timestamp).".path", 'a');
	}
	
	if($fstat_use_site_var){
		if(isset($_GET[$fstat_site_variable])){
			$tmp = $_GET[$fstat_site_variable];
		}else{
			$tmp = $fstat_default_site_name;
		}
		fputs($f_cont, time()."|".basename($_SERVER['SCRIPT_FILENAME'])."|".$tmp."\n");
	}else{
		fputs($f_cont, time()."|".basename($_SERVER['SCRIPT_FILENAME'])."\n");
	}
	fclose($f_cont);
	//Pfade notiert
?>