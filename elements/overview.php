<?php
	//
	$xml_all = new DOMDocument();
	$xml_all->loadXML(get_xml_backend("./backend/all.php"));
?>
	<div class="left">
		<div class="border">
			<h2><?php echo FLANG_H_OVERVIEW; ?></h2>
			<table class="Auflistung" style="text-align:right;">
			<tr>
				<th><?php echo FLANG_DAY; ?>:</th>
				<th><?php echo FLANG_VISITOR_S; ?>:</th>
				<th><?php echo FLANG_BOT_S; ?>:</th>
			</tr>
<?php
	$count_day_max_b = 0;//reset/set
	$count_day_max_p = 0;//reset/set
	$count_days = 0;
	
	$nodelist = $xml_all->getElementsByTagName("counter")->item(0)->getElementsByTagName("day");
	
	foreach($nodelist as $nodeday){
		$count_days++;
		$count_b = $nodeday->getElementsByTagName("bots")->item(0)->nodeValue;
		$count_p = $nodeday->getElementsByTagName("people")->item(0)->nodeValue;
		if($count_b > $count_day_max_b){$count_day_max_b = $count_b;}
		if($count_p > $count_day_max_p){$count_day_max_p = $count_p;}
		
		$fulldate = $nodeday->getAttribute("id");
		$day = substr($fulldate, -2, 2);
		
		if(gmdate("Y-m-d") == $fulldate){
		//if((gmdate("j") == $day) && (gmdate("m") == $show_month) && (gmdate("Y") == $show_year)){
			$td = "<td class=\"high_today\">";
		}elseif(gmdate("w", gmmktime(1, 1, 1, $show_month, $day, $show_year)) == 6){//6 für Samstag
			$td = "<td class=\"high_Sa\">";
		}elseif(gmdate("w", gmmktime(1, 1, 1, $show_month, $day, $show_year)) == 0){//0 für Sonntag
			$td = "<td class=\"high_Su\">";
		}else{
			$td = "<td>";
		}
		
		if($day % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		if(gmdate("Y-m-d") == $fulldate){
			$day = "&#x25BA;&nbsp;".$day;
		}
		//echo "\t\t<tr>\n";
		echo "\t\t\t\t".$td.$day."</td>\n";
		echo "\t\t\t\t".$td.$count_p."</td>\n";
		echo "\t\t\t\t".$td.$count_b."</td>\n";
		echo "\t\t\t</tr>\n";
	}
	
	$count_all_b = $xml_all->getElementsByTagName("counter")->item(0)->getElementsByTagName("total")->item(0)->getElementsByTagName("bots")->item(0)->nodeValue;
	$count_all_p = $xml_all->getElementsByTagName("counter")->item(0)->getElementsByTagName("total")->item(0)->getElementsByTagName("people")->item(0)->nodeValue;
	
	if($count_days != 0){
		$counter_av_p = round($count_all_p/$count_days, 1);
		$counter_av_b = round($count_all_b/$count_days, 1);
	}else{
		$counter_av_p = 0;
		$counter_av_b = 0;
	}
	
	echo "\t\t\t<tr class=\"table_sum\">\n";
	echo "\t\t\t\t<td>&sum;</td>\n";
	echo "\t\t\t\t<td>".$count_all_p."</td>\n";
	echo "\t\t\t\t<td>".$count_all_b."</td>\n";
	echo "\t\t\t</tr>\n";
	
	echo "\t\t\t<tr class=\"table_sum2\">\n";
	echo "\t\t\t\t<td>&Oslash;</td>\n";
	echo "\t\t\t\t<td>".$counter_av_p."</td>\n";
	echo "\t\t\t\t<td>".$counter_av_b."</td>\n";
	echo "\t\t\t</tr>\n";
	
?>
			</table>
		</div>
	</div>
	<div class="right">
		<div class="border">
			<h2><?php echo FLANG_H_BROWSER; ?></h2>
			<table class="Auflistung">
			<tr>
				<th colspan="2"><?php echo FLANG_BROWSER; ?>:</th>
				<th><?php echo FLANG_COUNT; ?>:</th>
				<th><?php echo FLANG_GRAPH; ?>:</th>
			</tr>
<?php
	$count_all = 0;
	$count_max = 0;
	
	//count all browsers
	$nodelist = $xml_all->getElementsByTagName("browser")->item(0)->getElementsByTagName("all");
	foreach($nodelist as $nodeall){
		$tmp = $nodeall->getElementsByTagName("count")->item(0)->nodeValue;
		if($tmp > $count_max){$count_max = $tmp;}
		$count_all = $count_all + $tmp;
	}
	
	$nodelist = $xml_all->getElementsByTagName("browser")->item(0)->getElementsByTagName("typ");
	
	$tmpcount = 0;
	foreach($nodelist as $nodebr){
		$tmpcount++;
		$name = $nodebr->getAttribute("name");
		$icon =  $nodebr->getElementsByTagName("all")->item(0)->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodebr->getElementsByTagName("all")->item(0)->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		$perc_relative = round(($count/$count_max)*100, 1);
		
		$namestrip = preg_replace('#[^a-z0-9]#i','',$name);
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\" width=\"16\" height=\"16\"></td>\n";
		echo "\t\t\t\t<td><a href=\"javascript:showhide('b_".$namestrip."');\">".$name."</a></td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc_relative."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
		
		$nodelist2 = $nodebr->getElementsByTagName("sub");
		
		foreach($nodelist2 as $nodesub){
			$name = $nodesub->getAttribute("version");
			$icon =  $nodesub->getElementsByTagName("icon")->item(0)->nodeValue;
			$count = $nodesub->getElementsByTagName("count")->item(0)->nodeValue;
			
			if($tmpcount % 2 == 0){echo "\t\t\t<tr class=\"b_".$namestrip."\" style=\"display:none;\">\n";}else{echo "\t\t\t<tr class=\"backhigh b_".$namestrip."\" style=\"display:none;\">\n";}
			echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\" width=\"16\" height=\"16\"></td>\n";
			echo "\t\t\t\t<td>".$name."</td>\n";
			echo "\t\t\t\t<td>".$count."</td>\n";
			echo "\t\t\t\t<td>&nbsp;</td>\n";
			echo "\t\t\t</tr>\n";
		}
	}
	
	if($count_all == 0){
		echo "\t\t\t<tr><td colspan=\"4\"><div class=\"text\">".FLANG_NODATA."</div></td></tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2><?php echo FLANG_H_OS; ?></h2>
			<table class="Auflistung">
			<tr>
				<th colspan="2"><?php echo FLANG_OS; ?>:</th>
				<th><?php echo FLANG_COUNT; ?>:</th>
				<th><?php echo FLANG_GRAPH; ?>:</th>
			</tr>
<?php
	$count_all = 0;
	$count_max = 0;
	
	//count all OS's
	$nodelist = $xml_all->getElementsByTagName("os")->item(0)->getElementsByTagName("all");
	foreach($nodelist as $nodeall){
		$tmp = $nodeall->getElementsByTagName("count")->item(0)->nodeValue;
		if($tmp > $count_max){$count_max = $tmp;}
		$count_all = $count_all + $tmp;
	}
	
	$nodelist = $xml_all->getElementsByTagName("os")->item(0)->getElementsByTagName("typ");
	
	$tmpcount = 0;
	foreach($nodelist as $nodeos){
		$tmpcount++;
		$name = $nodeos->getAttribute("name");
		$icon =  $nodeos->getElementsByTagName("all")->item(0)->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodeos->getElementsByTagName("all")->item(0)->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		$perc_relative = round(($count/$count_max)*100, 1);
		
		$namestrip = preg_replace('#[^a-z0-9]#i','',$name);
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."os/".$icon."\" alt=\"*\" width=\"16\" height=\"16\"></td>\n";
		echo "\t\t\t\t<td><a href=\"javascript:showhide('o_".$namestrip."');\">".$name."</a></td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc_relative."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
		
		$nodelist2 = $nodeos->getElementsByTagName("sub");
		
		foreach($nodelist2 as $nodesub){
			$name = $nodesub->getAttribute("version");
			$icon =  $nodesub->getElementsByTagName("icon")->item(0)->nodeValue;
			$count = $nodesub->getElementsByTagName("count")->item(0)->nodeValue;
			
			if($tmpcount % 2 == 0){echo "\t\t\t<tr class=\"o_".$namestrip."\" style=\"display:none;\">\n";}else{echo "\t\t\t<tr class=\"backhigh o_".$namestrip."\" style=\"display:none;\">\n";}
			echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."os/".$icon."\" alt=\"*\" width=\"16\" height=\"16\"></td>\n";
			echo "\t\t\t\t<td>".$name."</td>\n";
			echo "\t\t\t\t<td>".$count."</td>\n";
			echo "\t\t\t\t<td>&nbsp;</td>\n";
			echo "\t\t\t</tr>\n";
		}
	}
	
	if($count_all == 0){
		echo "\t\t\t<tr><td colspan=\"4\"><div class=\"text\">".FLANG_NODATA."</div></td></tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2><?php echo FLANG_H_BOT; ?></h2>
			<table class="Auflistung">
			<tr>
				<th colspan="2"><?php echo FLANG_BOT_S; ?>:</th>
				<th><?php echo FLANG_COUNT; ?>:</th>
				<th><?php echo FLANG_GRAPH; ?>:</th>
			</tr>
<?php
	$count_all = 0;
	$count_max = 0;
	//count all bots
	$nodelist = $xml_all->getElementsByTagName("bot")->item(0)->getElementsByTagName("all");
	foreach($nodelist as $nodeall){
		$tmp = $nodeall->getElementsByTagName("count")->item(0)->nodeValue;
		if($tmp > $count_max){$count_max = $tmp;}
		$count_all = $count_all + $tmp;
	}
	
	$nodelist = $xml_all->getElementsByTagName("bot")->item(0)->getElementsByTagName("typ");
	
	$tmpcount = 0;
	foreach($nodelist as $nodebot){
		$tmpcount++;
		$name = $nodebot->getAttribute("name");
		$icon =  $nodebot->getElementsByTagName("all")->item(0)->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodebot->getElementsByTagName("all")->item(0)->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		$perc_relative = round(($count/$count_max)*100, 1);
		
		$namestrip = preg_replace('#[^a-z0-9]#i','',$name);
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\" width=\"16\" height=\"16\"></td>\n";
		echo "\t\t\t\t<td><a href=\"javascript:showhide('b_".$namestrip."');\">".$name."</a></td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc_relative."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
		
		$nodelist2 = $nodebot->getElementsByTagName("sub");
		
		foreach($nodelist2 as $nodesub){
			$name = $nodesub->getAttribute("version");
			$icon =  $nodesub->getElementsByTagName("icon")->item(0)->nodeValue;
			$count = $nodesub->getElementsByTagName("count")->item(0)->nodeValue;
			
			if($tmpcount % 2 == 0){echo "\t\t\t<tr class=\"b_".$namestrip."\" style=\"display:none;\">\n";}else{echo "\t\t\t<tr class=\"backhigh b_".$namestrip."\" style=\"display:none;\">\n";}
			echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."agent/".$icon."\" alt=\"*\" width=\"16\" height=\"16\"></td>\n";
			echo "\t\t\t\t<td>".$name."</td>\n";
			echo "\t\t\t\t<td>".$count."</td>\n";
			echo "\t\t\t\t<td>&nbsp;</td>\n";
			echo "\t\t\t</tr>\n";
		}
	}
	
	if($count_all == 0){
		echo "\t\t\t<tr><td colspan=\"4\"><div class=\"text\">".FLANG_NODATA."</div></td></tr>\n";
	}
?>
			</table>
			<br />
<?php 		
	if($count_day_max_b > 0){
		echo "\t\t\t<table class=\"databoard_50\">\n";
		echo "\t\t\t\t<tr>\n";
		
		$count_b_last = 0;
		$count_b_all = 0;
		$count_b_count = 0;
		
		$nodelist = $xml_all->getElementsByTagName("counter")->item(0)->getElementsByTagName("day");
		foreach($nodelist as $nodeday){
			$count_b = $nodeday->getElementsByTagName("bots")->item(0)->nodeValue;
			$count_b_all = $count_b_all + $count_b;
			$count_b_count++;
			$fulldate = $nodeday->getAttribute("id");
			$day = substr($fulldate, -2, 2);
			$month = substr($fulldate, -5, 2);
			
			$height = round(($count_b/$count_day_max_b)*50,0);//50px max
			$height2 = round((($count_b_all/$count_b_count)/$count_day_max_b)*50,0) - 1; //50px max //bars come down 2px ;)
			
			echo "\t\t\t\t\t<td title=\"".$count_b." ".FLANG_VISITTIME_B." ".$day.". ".$monthnames[$month-1]." (".sprintf("%+d", $count_b-$count_b_last)." ".FLANG_BOT_S.")\"><div class=\"graph\" style=\"height:".$height."px;\"></div><div class=\"dots\" style=\"top:-".$height2."px;\"></div></td>\n";
			$count_b_last = $count_b;
		}
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t</table>\n";
	}else{
		echo "\t\t\t<div class=\"text\">".FLANG_NODATA."</div>\n";
	}
?>
		</div>
		<div class="border">
			<h2><?php echo FLANG_H_COUNTRY; ?></h2>
			<table class="Auflistung">
			<tr>
				<th colspan="2"><?php echo FLANG_COUNTRY; ?>:</th>
				<th><?php echo FLANG_COUNT; ?>:</th>
				<th><?php echo FLANG_GRAPH; ?>:</th>
			</tr>
<?php
	$count_all = 0;
	$count_max = 0;
	
	//count all countrys
	$nodelist = $xml_all->getElementsByTagName("country")->item(0)->getElementsByTagName("count");
	foreach($nodelist as $nodecount){
		$tmp = $nodecount->nodeValue;
		if($tmp > $count_max){$count_max = $tmp;}
		$count_all = $count_all + $tmp;
	}
	
	$nodelist = $xml_all->getElementsByTagName("country")->item(0)->getElementsByTagName("cou");
	
	$tmpcount = 0;
	foreach($nodelist as $nodecou){
		$tmpcount++;
		
		$country = $nodecou->getAttribute("name");
		$icon = $nodecou->getElementsByTagName("icon")->item(0)->nodeValue;
		$count = $nodecou->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		$perc_relative = round(($count/$count_max)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."country/".$icon."\" alt=\"*\" width=\"16\" height=\"11\"></td>\n";
		echo "\t\t\t\t<td>".$country."</td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc_relative."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
	}
	
	if($count_all == 0){
		echo "\t\t\t<tr><td colspan=\"4\"><div class=\"text\">".FLANG_NODATA."</div></td></tr>\n";
	}
?>
			</table>
		</div>
	</div>
	<div class="middle">
		<div class="border">
			<h2><?php echo FLANG_H_DIST_VISIT; ?></h2>
<?php 		
	if($count_day_max_p > 0){
		echo "\t\t\t<table class=\"databoard_100\">\n";
		echo "\t\t\t\t<tr>\n";
		
		$count_p_last = 0;
		$count_p_all = 0;
		$count_p_count = 0;
		
		$nodelist = $xml_all->getElementsByTagName("counter")->item(0)->getElementsByTagName("day");
		foreach($nodelist as $nodeday){
			$count_p = $nodeday->getElementsByTagName("people")->item(0)->nodeValue;
			$count_p_all = $count_p_all + $count_p;
			$count_p_count++;
			$fulldate = $nodeday->getAttribute("id");
			$day = substr($fulldate, -2, 2);
			$month = substr($fulldate, -5, 2);
			
			$height = round(($count_p/$count_day_max_p)*100,0);//100px max
			$height2 = round((($count_p_all/$count_p_count)/$count_day_max_p)*100,0) - 1; //100px max //bars come down 2px ;)
			
			echo "\t\t\t\t\t<td title=\"".$count_p." ".FLANG_VISITTIME_D." ".$day.". ".$monthnames[$month-1]." (".sprintf("%+d", $count_p-$count_p_last)." ".FLANG_VISITOR_S.")\"><div class=\"graph\" style=\"height:".$height."px;\"></div><div class=\"dots\" style=\"top:-".$height2."px;\"></div></td>\n";
			$count_p_last = $count_p;
			}
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t</table>\n";
	}else{
		echo "\t\t\t<div class=\"text\">".FLANG_NODATA."</div>\n";
	}
?>
		</div>
		<div class="border">
			<h2><?php echo FLANG_H_SITE; ?></h2>
			<table class="Auflistung">
			<tr>
				<th><?php echo FLANG_SITE; ?>:</th>
				<th><?php echo FLANG_VISITOR_L; ?>:</th>
				<th><?php echo FLANG_GRAPH; ?>:</th>
				<th><?php echo FLANG_BOT_L; ?>:</th>
			</tr>
<?php
	$xml_sites = new DOMDocument();
	$xml_sites->loadXML(get_xml_backend("./backend/sitecounter.php"));
	
	
	//count all
	$count_all_p = 0;//reset
	$count_all_p = $xml_sites->getElementsByTagName("total")->item(0)->getElementsByTagName("people")->item(0)->nodeValue;
	$count_max_p = 0;
	
	$count_all_b = 0;
	$count_all_b = $xml_sites->getElementsByTagName("total")->item(0)->getElementsByTagName("bots")->item(0)->nodeValue;
	
	$nodelist = $xml_sites->getElementsByTagName("sub");
	
	$tmpcount = 0;
	$last_parent_site = "";
	
	foreach($nodelist as $nodesite){
		$tmp = $nodesite->getElementsByTagName("people")->item(0)->nodeValue;
		if($tmp > $count_max_p){$count_max_p = $tmp;}
	}
	
	foreach($nodelist as $nodesite){
		$tmpcount++;
		
		if($fstat_use_site_var == true){
			$parentssite = $nodesite->parentNode->getAttribute("name");
			if($parentssite != $last_parent_site){
				$last_parent_site = $parentssite;
				echo "\t\t\t<tr>\n";
				echo "\t\t\t\t<th class=\"center\" colspan=\"4\">".$parentssite."</th>\n";
				echo "\t\t\t</tr>\n";
			}
		}
		
		$name = $nodesite->getAttribute("name");
		
		$count_p = $nodesite->getElementsByTagName("people")->item(0)->nodeValue;
		$count_b = $nodesite->getElementsByTagName("bots")->item(0)->nodeValue;
		$perc_p = round(($count_p/$count_all_p)*100, 1);
		$perc_p_relative = round(($count_p/$count_max_p)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td>".$name."</td>\n";
		echo "\t\t\t\t<td>".$count_p."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc_p." %\"><div style=\"width:".$perc_p_relative."%;\"></div></td>\n";
		echo "\t\t\t\t<td>".$count_b."</td>\n";
		echo "\t\t\t</tr>\n";
	}
	
	if($count_all_p == 0 and $count_all_b == 0){
		echo "\t\t\t<tr><td colspan=\"4\"><div class=\"text\">".FLANG_NODATA."</div></td></tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2><?php echo FLANG_H_REFERER; ?></h2>
			<table class="Auflistung">
			<tr>
				<th><?php echo FLANG_DOMAIN; ?>:</th>
				<th><?php echo FLANG_COUNT; ?>:</th>
				<th><?php echo FLANG_GRAPH; ?>:</th>
			</tr>
<?php
	$count_all = 0;
	$count_max = 0;
	
	//count all referes
	$nodelist = $xml_all->getElementsByTagName("referer")->item(0)->getElementsByTagName("count");
	foreach($nodelist as $nodecount){
		$tmp = $nodecount->nodeValue;
		if($tmp > $count_max){$count_max = $tmp;}
		$count_all = $count_all + $tmp;
	}
	
	$nodelist = $xml_all->getElementsByTagName("referer")->item(0)->getElementsByTagName("ref");
	
	$tmpcount = 0;
	foreach($nodelist as $noderef){
		$tmpcount++;
		
		$domain = $noderef->getAttribute("domain");
		$count = $noderef->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		$perc_relative = round(($count/$count_max)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td>".$domain."</td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc_relative."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
	}
	
	if($count_all == 0){
		echo "\t\t\t<tr><td colspan=\"3\"><div class=\"text\">".FLANG_NODATA."</div></td></tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2><?php echo FLANG_H_SEARCHW; ?></h2>
			<table class="Auflistung">
			<tr>
				<th><?php echo FLANG_SEARCHW; ?>:</th>
				<th><?php echo FLANG_COUNT; ?>:</th>
				<th><?php echo FLANG_GRAPH; ?>:</th>
			</tr>
<?php
	$count_all = 0;
	$count_max = 0;
	
	//count all searchstr
	$nodelist = $xml_all->getElementsByTagName("search")->item(0)->getElementsByTagName("count");
	foreach($nodelist as $nodecount){
		$tmp = $nodecount->nodeValue;
		if($tmp > $count_max){$count_max = $tmp;}
		$count_all = $count_all + $tmp;
	}
	
	$nodelist = $xml_all->getElementsByTagName("search")->item(0)->getElementsByTagName("ref");
	
	$tmpcount = 0;
	foreach($nodelist as $nodesearch){
		$tmpcount++;
		
		$string = $nodesearch->getAttribute("keywords");
		$count = $nodesearch->getElementsByTagName("count")->item(0)->nodeValue;
		$perc = round(($count/$count_all)*100, 1);
		$perc_relative = round(($count/$count_max)*100, 1);
		
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td>".$string."</td>\n";
		echo "\t\t\t\t<td>".$count."</td>\n";
		echo "\t\t\t\t<td class=\"perc\" title=\"".$perc." %\"><div style=\"width:".$perc_relative."%;\"></div></td>\n";
		echo "\t\t\t</tr>\n";
	}
	
	if($count_all == 0){
		echo "\t\t\t<tr><td colspan=\"3\"><div class=\"text\">".FLANG_NODATA."</div></td></tr>\n";
	}
?>
			</table>
		</div>
		<div class="border">
			<h2><?php echo FLANG_H_DIST_TIME; ?></h2>
<?php 		
	$count_time_max_p = 0;//reset/set
	$count_time_max_b = 0;//reset/set
	
	$nodelist = $xml_all->getElementsByTagName("times")->item(0)->getElementsByTagName("time");
	
	foreach($nodelist as $nodetime){
		$count_b = $nodetime->getElementsByTagName("bots")->item(0)->nodeValue;
		$count_p = $nodetime->getElementsByTagName("people")->item(0)->nodeValue;
		if($count_b > $count_time_max_b){$count_time_max_b = $count_b;}
		if($count_p > $count_time_max_p){$count_time_max_p = $count_p;}
	}
	
	$count_all_time_b = $xml_all->getElementsByTagName("times")->item(0)->getElementsByTagName("total")->item(0)->getElementsByTagName("bots")->item(0)->nodeValue;
	$count_all_time_p = $xml_all->getElementsByTagName("times")->item(0)->getElementsByTagName("total")->item(0)->getElementsByTagName("people")->item(0)->nodeValue;
	
	if($count_time_max_p > 0){
		echo "\t\t\t<table class=\"databoard_100\">\n";
		echo "\t\t\t\t<tr>\n";
		
		foreach($nodelist as $nodetime){
			$count_p = $nodetime->getElementsByTagName("people")->item(0)->nodeValue;
			$period = $nodetime->getAttribute("period");
			
			$height = round(($count_p/$count_time_max_p)*100,0);//100px max
			
			echo "\t\t\t\t\t<td title=\"".$count_p." ".FLANG_VISITTIME_T." ".$period." ".FLANG_CLOCK."\"><div class=\"graph\" style=\"height:".$height."px; top:0px;\"></div></td>\n"; //top:0, becasue there are no dots ;)
		}
		echo "\t\t\t\t</tr>\n";
		echo "\t\t\t</table>\n";
	}else{
		echo "\t\t\t<div class=\"text\">".FLANG_NODATA."</div>\n";
	}
?>
		</div>
	</div>
