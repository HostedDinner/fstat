	<h1><?php echo $fstat_title; ?></h1>
	<div class="menubar">
		<div class="menuentry">
			<a href="#"><?php echo FLANG_MONTH; ?></a>
			<div class="submenu">
<?php
        echo "\t\t\t\t<a href=\"./".$urlBuilder->build('overview', gmdate("Y"), gmdate("n"))."\">".FLANG_TODAY."</a>\n";
	echo "\t\t\t\t<hr>\n";
	
        $prev = $displayTime->getPreviousStartMonth();
        $next = $displayTime->getNextStartMonth();
	
	echo "\t\t\t\t<a href=\"./".$urlBuilder->build('overview', $prev['year'], $prev['month'])."\">".FLANG_PREV_MONTH."</a>\n";
	echo "\t\t\t\t<a href=\"./".$urlBuilder->build('overview', $next['year'], $next['month'])."\">".FLANG_NEXT_MONTH."</a>\n";
?>
				<hr>
<?php
	for($i = 0; $i < 12; $i++){
	
		if($i != ($displayTime->getStartMonth()-1)){
			$string = "<a href=\"./".$urlBuilder->build('overview', $displayTime->getStartYear(), $i+1)."\">".  $lang->monthnames[$i]."</a>";
		}else{
			$string = "<span>".$lang->monthnames[$displayTime->getStartMonth()-1]."</span>";
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
			$string = "<span>".$displayTime->getStartYear()."</span>";
		}else{
			$string = "<a href=\"./".$urlBuilder->build('overview', $displayTime->getStartYear()+$i, $displayTime->getStartMonth())."\">".($displayTime->getStartYear()+$i)."</a>";
		}
		echo "\t\t\t\t".$string."\n";
	}
?>
			</div>
		</div>
		<div class="menuentry">
			<a href="<?php echo "./".$urlBuilder->build('last'); ?>"><?php echo FLANG_LAST." ".$fstat_last_length." ".FLANG_VISITOR_S; ?></a>
		</div>
		<div class="menuentry">
			<a href="<?php echo "./".$urlBuilder->build('lastbots'); ?>"><?php echo FLANG_LAST." ".$fstat_last_length." ".FLANG_BOT_S; ?></a>
		</div>
		<div class="menuentry">
			<a href="#"><?php echo FLANG_LANG; ?></a>
			<div class="submenu">
<?php
	$langdir = opendir("./lang/");
	while (($file = readdir($langdir)) !== FALSE){
		if((substr($file, -4) == ".php")){
			$langcode = substr($file, -6, 2);
			$langstr = Language::lookupLang($langcode);
			if($urlBuilder->getPage() == 'overview'){
                                $string = "<a href=\"./".$urlBuilder->build(null, $displayTime->getStartYear(), $displayTime->getStartMonth(), $langcode)."\">".$langstr."</a>";
			}else{
				$string = "<a href=\"./".$urlBuilder->build(null, null, null, $langcode)."\">".$langstr."</a>";
			}
			echo "\t\t\t\t".$string."\n";
		}
	}
	closedir($langdir);
?>
			</div>
		</div>
		<div class="menuentryR">
			<a href="<?php echo "./".$urlBuilder->build('about'); ?>"><?php echo "".FLANG_H_ABOUT; ?></a>
		</div>
		<div class="clear"></div>
	</div>
	
