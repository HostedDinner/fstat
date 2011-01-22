<?php
function get_xml_backend($filename, $year = 0, $month = 0){
    if (is_file($filename)){
	
        if($year != 0) {$fstat_backend_year  = $year;}
		if($month != 0){$fstat_backend_month = $month;}
		
		$is_include = true;
		ob_start();
			include $filename;
			$contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }else{
		return false;
	}
}
	
	
	
//Echo Functions:.
function echo_tr_3($array, $ico_subdir, $max){
	global $fstat_ico_dir;
	$tmpcount = 0;
	foreach($array as $typ => $tarr){
		$tmpcount++;
		$perc = round(($tarr['alle']['count']/$max)*100, 1);
		$array[$typ]['alle']['perc'] = $perc;
	
		$typstrip = preg_replace('#[^a-z0-9]#i','',$typ);
	
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		//echo "\t\t<tr>\n";
		echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir.$ico_subdir."/".$array[$typ]['alle']['ico']."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td><a href=\"javascript:showhide('".$typstrip."');\">".$typ."</a></td>\n";
		echo "\t\t\t\t<td>".$array[$typ]['alle']['count']."</td>\n";
		echo "\t\t\t\t<td>".$array[$typ]['alle']['perc']."%</td>\n";
		echo "\t\t\t</tr>\n";
		
		//einzel Versionen auch noch:
		foreach($tarr as $version => $daten){
			if($version != 'alle'){
				if($tmpcount % 2 == 0){echo "\t\t\t<tr class=\"".$typstrip."\" style=\"display:none;\">\n";}else{echo "\t\t\t<tr class=\"backhigh ".$typstrip."\" style=\"display:none;\">\n";}
				$perc = round(($version['count']/$tarr['alle']['count'])*100, 1);
				$array[$typ][$version]['perc'] = $perc;
				echo "\t\t\t\t<td><img src=\"".$fstat_ico_dir.$ico_subdir."/".$array[$typ][$version]['ico']."\" alt=\"*\"></td>\n";
				echo "\t\t\t\t<td>".$version."</td>\n";
				echo "\t\t\t\t<td>".$array[$typ][$version]['count']."</td>\n";
				echo "\t\t\t\t<td>&nbsp;</td>\n";
				//echo "\t\t\t\t<td>".$array[$typ][$version]['perc']."%</td>\n";
				echo "\t\t\t</tr>\n";
			}
	
			echo "\t\t\t</tr>\n";
		}
	}
}
	
	
?>