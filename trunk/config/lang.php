<?php
	if(isset($_GET['lang'])){
		$lang = preg_replace('#[^0-9a-z]#i','',$_GET['lang']);
		if(file_exists("./lang/lang.".$lang.".php")){
			include "./lang/lang.".$lang.".php";
			$show_lang = $lang;
		}else{
			include "./lang/lang.en.php";
			$show_lang = "en";
		}
	}else{
		if(file_exists("./lang/lang.".$fstat_default_lang.".php")){
			include "./lang/lang.".$fstat_default_lang.".php";
			$show_lang = $fstat_default_lang;
		}else{
			include "./lang/lang.en.php";
			$show_lang = "en";
		}
	}
?>