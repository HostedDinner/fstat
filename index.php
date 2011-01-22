<?php
	//error_reporting(E_NONE); //keine Fehler anzeigen
	//error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
	error_reporting(E_ALL); // alle Fehler anzeigen
	include "./config/settings.php";
	include_once "./functions/main_include.php";
	include_once "./functions/date.php";//enthält $monthnames, $show_year, $show_month, $show_timestamp
		
	if(isset($_GET['show'])){
		$show_cat = preg_replace('#[^0-9^a-z]#i','',$_GET['show']);//alles ausser 0-9 mit nichts ersetzten
	}else{
		$show_cat = "overview";
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Statistik f&uuml;r <?php echo $monthnames[$show_month-1]." ".$show_year;?></title>
	<meta name="author" content="Fabian Neffgen">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="./style.css">
	<script type="text/javascript">
		//I will not support a IE fix!!!!
		function showhide(classmember) {
			elements = document.getElementsByClassName(classmember);
			var newstyle;
			if(elements[0].style.display == "none"){
				newstyle = "table-row";
			}else{
				newstyle = "none";
			}
			
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = newstyle;
			}
		}
	</script>
</head>
<body>
<div id="container">
	<h1>Statistik f&uuml;r <?php echo $monthnames[$show_month-1]." ".$show_year;?></h1>
	<div class="menu">
		<table>
		<tr>
		<td class="mainentry">
			<a href="<?php echo "./?year=".date("Y")."&amp;month=".date("m"); ?>">Monat</a>
			<ul>
<?php
	for($i = 0; $i < 12; $i++){
	
		if($i != ($show_month-1)){
			$string = "<a href=\"./?year=".$show_year."&amp;month=".($i+1)."\">".$monthnames[$i]."</a>";
		}else{
			$string = $monthnames[$show_month-1];
		}
		echo "\t\t\t\t<li>".$string."</li>\n";
	}
?>
			</ul>
		</td>
		<td class="mainentry">
			<a href="#">Jahr</a>
			<ul>
<?php
	for($i = -3; $i <= 3; $i++){
		if($i == 0){
			$string = $show_year;
		}else{
			$string = "<a href=\"./?year=".($show_year+$i)."&amp;month=".$show_month."\">".($show_year+$i)."</a>";
		}
		echo "\t\t\t\t<li>".$string."</li>\n";
	}
?>
			</ul>
		</td>
		</tr>
		</table>
	</div>
<?php
switch ($show_cat){
case "overview":
?>
	<div class="left">
		<div class="border">
			<h2>&Uuml;bersicht</h2>
			<table class="Auflistung" style="text-align:right;">
			<tr>
				<th>Tag:</th>
				<th>Bes.:</th>
				<th>Bots:</th>
			</tr>
<?php
	$xml_counter = new DOMDocument();
	$xml_counter->loadXML(get_xml_backend("./backend/counter.php"));
	
	$nodelist = $xml_counter->getElementsByTagName("day");
	
	$count_max_p = 0;//reset/set
	$count_max_b = 0;//reset/set
	
	foreach($nodelist as $nodeday){
		$count_b = $nodeday->getElementsByTagName("bots")->item(0)->nodeValue;
		$count_p = $nodeday->getElementsByTagName("people")->item(0)->nodeValue;
		if($count_b > $count_max_b){$count_max_b = $count_b;}
		if($count_p > $count_max_p){$count_max_p = $count_p;}
		
		$day = $nodeday->getAttribute("id");
		
		if(date("w", mktime(1, 1, 1, $show_month, $day, $show_year)) == 0){
			$td = "<td class=\"high\">";
		}else{
			$td = "<td>";
		}
		
		if($day % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		//echo "\t\t<tr>\n";
		echo "\t\t\t\t".$td.$day."</td>\n";
		echo "\t\t\t\t".$td.$count_p."</td>\n";
		echo "\t\t\t\t".$td.$count_b."</td>\n";
		echo "\t\t\t</tr>\n";
	}
	
	$count_all_b =   $xml_counter->getElementsByTagName("total")->item(0)->getElementsByTagName("bots")->item(0)->nodeValue;
	$count_all_p = $xml_counter->getElementsByTagName("total")->item(0)->getElementsByTagName("people")->item(0)->nodeValue;
	
	echo "\t\t\t<tr class=\"table_sum\">\n";
	echo "\t\t\t\t<td>G:</td>\n";
	echo "\t\t\t\t<td>".$count_all_p."</td>\n";
	echo "\t\t\t\t<td>".$count_all_b."</td>\n";
	echo "\t\t\t</tr>\n";
	
?>
			</table>
		</div>
	</div>
	<div class="right">
		<div class="border">
			<h2>Browser</h2>
			<table class="Auflistung">
			<tr>
				<th>&nbsp;</th>
				<th>Browser:</th>
				<th>Anzahl:</th>
				<th>Graph:</th>
			</tr>
<?php
	$xml_browser = new DOMDocument();
	$xml_browser->loadXML(get_xml_backend("./backend/browser.php"));
	
	$count_all = 0;
	//count all
	$nodelist = $xml_browser->getElementsByTagName("all");
	foreach($nodelist as $nodeall){
		$count_all = $count_all + $nodeall->getElementsByTagName("count")->item(0)->nodeValue;
	}
	
	$nodelist = $xml_browser->getElementsByTagName("typ");
	
	$tmpcount = 0;
	foreach($nodelist as $nodebr){
		$tmpcount++;
		$name = $nodebr->getAttribute("name");
		$icon =  $nodebr->getElementsByTagName("all")->item(0)->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodebr->getElementsByTagName("all")->item(0)->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		
		$namestrip = preg_replace('#[^a-z0-9]#i','',$name);
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td><a href=\"javascript:showhide('b_".$namestrip."');\">".$name."</a></td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
		
		$nodelist2 = $nodebr->getElementsByTagName("sub");
		
		foreach($nodelist2 as $nodesub){
			$name = $nodesub->getAttribute("version");
			$icon =  $nodesub->getElementsByTagName("icon")->item(0)->nodeValue;
			$count = $nodesub->getElementsByTagName("count")->item(0)->nodeValue;
			
			if($tmpcount % 2 == 0){echo "\t\t\t<tr class=\"b_".$namestrip."\" style=\"display:none;\">\n";}else{echo "\t\t\t<tr class=\"backhigh b_".$namestrip."\" style=\"display:none;\">\n";}
			echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\"></td>\n";
			echo "\t\t\t\t<td>".$name."</td>\n";
			echo "\t\t\t\t<td>".$count."</td>\n";
			echo "\t\t\t\t<td>&nbsp;</td>\n";
			echo "\t\t\t</tr>\n";
		}
	}
?>
			</table>
		</div>
		<div class="border">
			<h2>OS</h2>
			<table class="Auflistung">
			<tr>
				<th>&nbsp;</th>
				<th>OS:</th>
				<th>Anzahl:</th>
				<th>Graph:</th>
			</tr>
<?php
	$xml_os = new DOMDocument();
	$xml_os->loadXML(get_xml_backend("./backend/os.php"));
	
	$count_all = 0;
	//count all
	$nodelist = $xml_os->getElementsByTagName("all");
	foreach($nodelist as $nodeall){
		$count_all = $count_all + $nodeall->getElementsByTagName("count")->item(0)->nodeValue;
	}
	
	$nodelist = $xml_os->getElementsByTagName("typ");
	
	$tmpcount = 0;
	foreach($nodelist as $nodeos){
		$tmpcount++;
		$name = $nodeos->getAttribute("name");
		$icon =  $nodeos->getElementsByTagName("all")->item(0)->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodeos->getElementsByTagName("all")->item(0)->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		
		$namestrip = preg_replace('#[^a-z0-9]#i','',$name);
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir."os/".$icon."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td><a href=\"javascript:showhide('o_".$namestrip."');\">".$name."</a></td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
		
		$nodelist2 = $nodeos->getElementsByTagName("sub");
		
		foreach($nodelist2 as $nodesub){
			$name = $nodesub->getAttribute("version");
			$icon =  $nodesub->getElementsByTagName("icon")->item(0)->nodeValue;
			$count = $nodesub->getElementsByTagName("count")->item(0)->nodeValue;
			
			if($tmpcount % 2 == 0){echo "\t\t\t<tr class=\"o_".$namestrip."\" style=\"display:none;\">\n";}else{echo "\t\t\t<tr class=\"backhigh o_".$namestrip."\" style=\"display:none;\">\n";}
			echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir."os/".$icon."\" alt=\"*\"></td>\n";
			echo "\t\t\t\t<td>".$name."</td>\n";
			echo "\t\t\t\t<td>".$count."</td>\n";
			echo "\t\t\t\t<td>&nbsp;</td>\n";
			echo "\t\t\t</tr>\n";
		}
	}
?>
			</table>
		</div>
		<div class="border">
			<h2>Bots</h2>
			<table class="Auflistung">
			<tr>
				<th>&nbsp;</th>
				<th>Bots:</th>
				<th>Anzahl:</th>
				<th>Graph:</th>
			</tr>
<?php
	$xml_bot = new DOMDocument();
	$xml_bot->loadXML(get_xml_backend("./backend/bots.php"));
	
	$count_all = 0;
	//count all
	$nodelist = $xml_bot->getElementsByTagName("all");
	foreach($nodelist as $nodeall){
		$count_all = $count_all + $nodeall->getElementsByTagName("count")->item(0)->nodeValue;
	}
	
	$nodelist = $xml_bot->getElementsByTagName("typ");
	
	$tmpcount = 0;
	foreach($nodelist as $nodebot){
		$tmpcount++;
		$name = $nodebot->getAttribute("name");
		$icon =  $nodebot->getElementsByTagName("all")->item(0)->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodebot->getElementsByTagName("all")->item(0)->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		
		$namestrip = preg_replace('#[^a-z0-9]#i','',$name);
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td><a href=\"javascript:showhide('b_".$namestrip."');\">".$name."</a></td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
		
		$nodelist2 = $nodebot->getElementsByTagName("sub");
		
		foreach($nodelist2 as $nodesub){
			$name = $nodesub->getAttribute("version");
			$icon =  $nodesub->getElementsByTagName("icon")->item(0)->nodeValue;
			$count = $nodesub->getElementsByTagName("count")->item(0)->nodeValue;
			
			if($tmpcount % 2 == 0){echo "\t\t\t<tr class=\"b_".$namestrip."\" style=\"display:none;\">\n";}else{echo "\t\t\t<tr class=\"backhigh b_".$namestrip."\" style=\"display:none;\">\n";}
			echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\"></td>\n";
			echo "\t\t\t\t<td>".$name."</td>\n";
			echo "\t\t\t\t<td>".$count."</td>\n";
			echo "\t\t\t\t<td>&nbsp;</td>\n";
			echo "\t\t\t</tr>\n";
		}
	}
?>
			</table>
			<br />
<?php 		
	if($count_max_b > 0){
		echo "\t\t\t<table class=\"databoard_50\">\n";
		echo "\t\t\t\t<tr>\n";
		
		$nodelist = $xml_counter->getElementsByTagName("day");
		foreach($nodelist as $nodeday){
			$count_b = $nodeday->getElementsByTagName("bots")->item(0)->nodeValue;
			//$count_p = $nodeday->getElementsByTagName("people")->item(0)->nodeValue;
			$day = $nodeday->getAttribute("id");
			
			$height = round(($count_b/$count_max_b)*50,0);//50px max
			
			echo "\t\t\t\t\t<td title=\"".$count_b." Bots am ".$day.". ".$monthnames[$show_month-1]."\"><div style=\"height:".$height."px;\"></div></td>\n";
		}
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t</table>\n";
	}else{
		echo "\t\t\t<div class=\"text\">Keine Daten vorhanden!</div>\n";
	}
?>
		</div>
		<div class="border">
			<h2>L&auml;nder</h2>
			<table class="Auflistung">
			<tr>
				<th>&nbsp;</th>
				<th>Land:</th>
				<th>Anzahl:</th>
				<th>Graph:</th>
			</tr>
<?php
	$xml_cou = new DOMDocument();
	$xml_cou->loadXML(get_xml_backend("./backend/country.php"));
	
	
	$count_all = 0;
	//count all
	$nodelist = $xml_cou->getElementsByTagName("count");
	foreach($nodelist as $nodecount){
		$count_all = $count_all + $nodecount->nodeValue;
	}
	
	$nodelist = $xml_cou->getElementsByTagName("cou");
	
	$tmpcount = 0;
	foreach($nodelist as $nodecou){
		$tmpcount++;
		
		$country = $nodecou->getAttribute("name");
		$icon = $nodecou->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodecou->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir."country/".$icon."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td>".$country."</td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
	}
?>
			</table>
		</div>
	</div>
	<div class="middle">
		<div class="border">
			<h2>Besucherverteilung</h2>
<?php 		
	if($count_max_p > 0){
		echo "\t\t\t<table class=\"databoard_100\">\n";
		echo "\t\t\t\t<tr>\n";
		
		$nodelist = $xml_counter->getElementsByTagName("day");
		foreach($nodelist as $nodeday){
			//$count_b = $nodeday->getElementsByTagName("bots")->item(0)->nodeValue;
			$count_p = $nodeday->getElementsByTagName("people")->item(0)->nodeValue;
			$day = $nodeday->getAttribute("id");
			
			$height = round(($count_p/$count_max_p)*100,0);//100px max
			
			echo "\t\t\t\t\t<td title=\"".$count_p." Besucher am ".$day.". ".$monthnames[$show_month-1]."\"><div style=\"height:".$height."px;\"></div></td>\n";
		}
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t</table>\n";
	}else{
		echo "\t\t\t<div class=\"text\">Keine Daten vorhanden!</div>\n";
	}
?>
		</div>
		<div class="border">
			<h2>Seiten</h2>
			<table class="Auflistung">
			<tr>
				<th>Seite:</th>
				<th>Besucher:</th>
				<th>Graph:</th>
				<th>Bots:</th>
			</tr>
<?php
	$xml_sites = new DOMDocument();
	$xml_sites->loadXML(get_xml_backend("./backend/sitecounter.php"));
	
	
	//count all
	$count_all_p = 0;//reset
	$count_all_p = $xml_sites->getElementsByTagName("total")->item(0)->getElementsByTagName("people")->item(0)->nodeValue;
	
	$nodelist = $xml_sites->getElementsByTagName("sub");
	
	$tmpcount = 0;
	foreach($nodelist as $nodesite){
		$tmpcount++;
		
		$parentssite = $nodesite->parentNode->getAttribute("name");
		if($parentssite == "index.php"){
			$name = $nodesite->getAttribute("name");
		}else{
			$name = $nodesite->parentNode->getAttribute("name") . "/" . $nodesite->getAttribute("name");
		}
		$count_p = $nodesite->getElementsByTagName("people")->item(0)->nodeValue;
		$count_b = $nodesite->getElementsByTagName("bots")->item(0)->nodeValue;
		$perc_p = round(($count_p/$count_all_p)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td>".$name."</td>\n";
		echo "\t\t\t\t<td>".$count_p."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc_p." %\"><div style=\"width:".$perc_p."%;\"></div></td>\n";
		echo "\t\t\t\t<td>".$count_b."</td>\n";
		echo "\t\t\t</tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2>Referer</h2>
			<table class="Auflistung">
			<tr>
				<th>Domain:</th>
				<th>Anzahl:</th>
				<th>Graph:</th>
			</tr>
<?php
	$xml_ref = new DOMDocument();
	$xml_ref->loadXML(get_xml_backend("./backend/referer.php"));
	
	
	$count_all = 0;
	//count all
	$nodelist = $xml_ref->getElementsByTagName("count");
	foreach($nodelist as $nodecount){
		$count_all = $count_all + $nodecount->nodeValue;
	}
	
	$nodelist = $xml_ref->getElementsByTagName("ref");
	
	$tmpcount = 0;
	foreach($nodelist as $noderef){
		$tmpcount++;
		
		$domain = $noderef->getAttribute("domain");
		$count = $noderef->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td>".$domain."</td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2>Suchw&ouml;rter</h2>
			<table class="Auflistung">
			<tr>
				<th>String:</th>
				<th>Anzahl:</th>
				<th>Graph:</th>
			</tr>
<?php
	$xml_search = new DOMDocument();
	$xml_search->loadXML(get_xml_backend("./backend/search.php"));
	
	
	$count_all = 0;
	//count all
	$nodelist = $xml_search->getElementsByTagName("count");
	foreach($nodelist as $nodecount){
		$count_all = $count_all + $nodecount->nodeValue;
	}
	
	$nodelist = $xml_search->getElementsByTagName("ref");
	
	$tmpcount = 0;
	foreach($nodelist as $nodesearch){
		$tmpcount++;
		
		$string = $nodesearch->getAttribute("keywords");
		$count = $nodesearch->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td>".$string."</td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2>Zeitverteilung</h2>
<?php 		
	$xml_time = new DOMDocument();
	$xml_time->loadXML(get_xml_backend("./backend/time.php"));
	
	$nodelist = $xml_time->getElementsByTagName("time");
	
	$count_max_time_p = 0;//reset/set
	$count_max_time_b = 0;//reset/set
	
	foreach($nodelist as $nodetime){
		$count_b = $nodetime->getElementsByTagName("bots")->item(0)->nodeValue;
		$count_p = $nodetime->getElementsByTagName("people")->item(0)->nodeValue;
		if($count_b > $count_max_time_b){$count_max_time_b = $count_b;}
		if($count_p > $count_max_time_p){$count_max_time_p = $count_p;}
	}
	
	$count_all_time_b =   $xml_time->getElementsByTagName("total")->item(0)->getElementsByTagName("bots")->item(0)->nodeValue;
	$count_all_time_p = $xml_time->getElementsByTagName("total")->item(0)->getElementsByTagName("people")->item(0)->nodeValue;
	
	if($count_max_time_p > 0){
		echo "\t\t\t<table class=\"databoard_100\">\n";
		echo "\t\t\t\t<tr>\n";
		
		//$nodelist = $xml_counter->getElementsByTagName("time");
		foreach($nodelist as $nodetime){
			$count_p = $nodetime->getElementsByTagName("people")->item(0)->nodeValue;
			$period = $nodetime->getAttribute("period");
			
			$height = round(($count_p/$count_max_time_p)*100,0);//100px max
			
			echo "\t\t\t\t\t<td title=\"".$count_p." Besucher in der Zeit ".$period." Uhr\"><div style=\"height:".$height."px;\"></div></td>\n";
		}
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t</table>\n";
	}else{
		echo "\t\t\t<div class=\"text\">Keine Daten vorhanden!</div>\n";
	}
?>
		</div>
	</div>
<?php
	break;
}
?>
	<div class="footer">
		Diese Statistik benutzt die <a href="http://user-agent-string.info/">UAString Api</a>
	</div>
</div>
</body>
</html>