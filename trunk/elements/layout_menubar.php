	<h1><?php echo $fstat_title; ?></h1>
	<div class="menubar">
		<div class="menuentry">
			<a href="#"><?php echo FLANG_MONTH; ?></a>
			<div class="submenu">
<?php
	echo "\t\t\t\t<a href=\"./?".URL_AddShow("overview").URL_AddYear(gmdate("Y")).URL_AddMonth(gmdate("m")).URL_AddLang()."\">".FLANG_TODAY."</a>\n";
	echo "\t\t\t\t<hr>\n";
	
	$prev_month = $show_month - 1;
	$prev_year = $show_year;
	if($prev_month == 0){
		$prev_month = 12;
		$prev_year = $prev_year - 1;
	}
	
	$next_month = $show_month + 1;
	$next_year = $show_year;
	if($next_month == 13){
		$next_month = 1;
		$next_year = $next_year + 1;
	}
	
	echo "\t\t\t\t<a href=\"./?".URL_AddShow("overview").URL_AddYear($prev_year).URL_AddMonth($prev_month).URL_AddLang()."\">".FLANG_PREV_MONTH."</a>\n";
	echo "\t\t\t\t<a href=\"./?".URL_AddShow("overview").URL_AddYear($next_year).URL_AddMonth($next_month).URL_AddLang()."\">".FLANG_NEXT_MONTH."</a>\n";
?>
				<hr>
<?php
	for($i = 0; $i < 12; $i++){
	
		if($i != ($show_month-1)){
			$string = "<a href=\"./?".URL_AddShow("overview").URL_AddYear().URL_AddMonth($i+1).URL_AddLang()."\">".$monthnames[$i]."</a>";
		}else{
			$string = "<span>".$monthnames[$show_month-1]."</span>";
		}
		echo "\t\t\t\t".$string."\n";
	}
?>
			</div>
		</div>
		<div class="menuentry">
			<a href="#"><?php echo FLANG_YEAR; ?></a>
			<div class="submenu">
<?php
	for($i = -3; $i <= 3; $i++){
		if($i == 0){
			$string = "<span>".$show_year."</span>";
		}else{
			$string = "<a href=\"./?".URL_AddShow("overview").URL_AddYear(($show_year+$i)).URL_AddMonth().URL_AddLang()."\">".($show_year+$i)."</a>";
		}
		echo "\t\t\t\t".$string."\n";
	}
?>
			</div>
		</div>
		<div class="menuentry">
			<a href="<?php echo "./?".URL_AddShow("last").URL_AddLang(); ?>"><?php echo FLANG_LAST." ".$fstat_last_length; ?></a>
		</div>
		<div class="menuentry">
			<a href="#"><?php echo FLANG_LANG; ?></a>
			<div class="submenu">
<?php
	$langdir = opendir("./lang/");
	while (($file = readdir($langdir)) !== FALSE){
		if((substr($file, -4) == ".php")){
			$langcode = substr($file, -6, 2);
			$langstr = LookupLang($langcode);
			if($show_cat == "last"){
				$string = "<a href=\"./?".URL_AddShow().URL_AddLang($langcode)."\">".$langstr."</a>";
			}else{
				$string = "<a href=\"./?".URL_AddShow().URL_AddYear().URL_AddMonth().URL_AddLang($langcode)."\">".$langstr."</a>";
			}
			echo "\t\t\t\t".$string."\n";
		}
	}
	closedir($langdir);
?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
