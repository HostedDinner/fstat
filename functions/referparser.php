<?php
	function ReferParse(){
		$returnar = array();
		if(isset($_SERVER['HTTP_REFERER'])){
			$refstring = $_SERVER['HTTP_REFERER'] . "&"; //& damit die Suche erleichtert wird!
			$preg = "#(?:\?|&)(?:query|text|words|eingabe|q|p|r)=.+?(?=&)#is";
			
			preg_match($preg, $refstring, $text);
			
			//var_dump($text);
			
			if(isset($text[0])){
				$tmp = $text[0];
				//$tmp = rtrim($tmp, "&");
				//$tmp = preg_replace("/^.+?=/", "", $tmp);
				$tmp = strstr($tmp, "=");
				$tmp = trim($tmp, "=&");
				$tmp = str_replace("+", " ", $tmp);
				//$tmp = addslashes($tmp);
				
				$returnar["searchkeys"] = $tmp;
			}else{
				$returnar["searchkeys"] = "";
			}
			
			$preg = "#http(?:s)?://.+?/#i";
			preg_match($preg, $refstring, $text);
			
			if(isset($text[0])){
				$tmp = $text[0];
				$tmp = strstr($tmp, "/");
				$tmp = trim($tmp, "/");
				$returnar["domain"] = $tmp;
			}else{
				$returnar["domain"] = "";
			}
		}else{
			$returnar["searchkeys"] = "";
			$returnar["domain"] = "";
		}
		return $returnar;
	}
?>