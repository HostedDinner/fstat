	<h1><?php echo $fstat_title; ?></h1>
	<div class="menu">
		<table>
		<tr>
		<td class="mainentry">
			<a href="<?php echo "./?show=overview&amp;year=".gmdate("Y")."&amp;month=".gmdate("m")."&amp;lang=".$show_lang; ?>"><?php echo FLANG_MONTH; ?></a>
			<ul>
<?php
	for($i = 0; $i < 12; $i++){
	
		if($i != ($show_month-1)){
			$string = "<a href=\"./?show=overview&amp;year=".$show_year."&amp;month=".($i+1)."&amp;lang=".$show_lang."\">".$monthnames[$i]."</a>";
		}else{
			$string = $monthnames[$show_month-1];
		}
		echo "\t\t\t\t<li>".$string."</li>\n";
	}
?>
			</ul>
		</td>
		<td class="mainentry">
			<a href="<?php echo "./?show=overview&amp;year=".gmdate("Y")."&amp;month=".gmdate("m")."&amp;lang=".$show_lang; ?>"><?php echo FLANG_YEAR; ?></a>
			<ul>
<?php
	for($i = -3; $i <= 3; $i++){
		if($i == 0){
			$string = $show_year;
		}else{
			$string = "<a href=\"./?show=overview&amp;year=".($show_year+$i)."&amp;month=".$show_month."&amp;lang=".$show_lang."\">".($show_year+$i)."</a>";
		}
		echo "\t\t\t\t<li>".$string."</li>\n";
	}
?>
			</ul>
		</td>
		<td class="mainentry">
			<a href="<?php echo "./?show=last&amp;lang=".$show_lang; ?>"><?php echo FLANG_LAST." ".$fstat_last_length; ?></a>
		</td>
		<td class="mainentry">
			<a href="#"><?php echo FLANG_LANG; ?></a>
			<ul>
<?php
	$langdir = opendir("./lang/");
	while (($file = readdir($langdir)) !== FALSE){
		if((substr($file, -4) == ".php")){
			$langcode = substr($file, -6, 2);
			$langstr = LookupLang($langcode);
			$string = "<a href=\"./?show=".$show_cat."&year=".$show_year."&amp;month=".$show_month."&amp;lang=".$langcode."\">".$langstr."</a>";
			echo "\t\t\t\t<li>".$string."</li>\n";
		}
	}
	closedir($langdir);
?>
			</ul>
		</td>
		</tr>
		</table>
	</div>
