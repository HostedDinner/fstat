	<div class="border">
		<table class="Auflistung">
			<tr>
				<th>&nbsp;</th>
				<th><?php echo FLANG_DATE; ?>:</th>
				<th><?php echo FLANG_TIME; ?>:</th>
				<th colspan="2"><?php echo FLANG_COUNTRY; ?>:</th>
				<th colspan="2"><?php echo FLANG_BROWSER; ?>:</th>
				<th><?php echo FLANG_DOMAIN; ?>:</th>
				<th><?php echo FLANG_H_SITE; ?>:</th>
			</tr>
<?php
	$xml_last = new DOMDocument();
        $xml_last->loadXML(Backend::getXML("./backend/lastbots.php"));
	
	$nodelist = $xml_last->getElementsByTagName("visitor");
	
	$tmpcount = 0;
	foreach($nodelist as $row){
		$tmpcount++;
		
		$v_typ = @$row->getElementsByTagName("typ")->item(0)->nodeValue;
		$v_uas = @$row->getElementsByTagName("uas")->item(0)->nodeValue;
		$v_uip = @$row->getElementsByTagName("uip")->item(0)->nodeValue;
		$v_uhost = @$row->getElementsByTagName("uhost")->item(0)->nodeValue; if($v_uhost == ""){$v_uhost = $v_uip;};
		$v_uti = @$row->getElementsByTagName("uti")->item(0)->nodeValue;
		$v_ufam = @$row->getElementsByTagName("ufam")->item(0)->nodeValue;
		$v_unam = @$row->getElementsByTagName("unam")->item(0)->nodeValue;
		$v_uico = @$row->getElementsByTagName("uico")->item(0)->nodeValue;
		$v_ucoi = @$row->getElementsByTagName("ucoi")->item(0)->nodeValue;
		$v_ucon = @$row->getElementsByTagName("ucon")->item(0)->nodeValue;
		$v_rkey = @$row->getElementsByTagName("rkey")->item(0)->nodeValue;
		$v_rdom = @$row->getElementsByTagName("rdom")->item(0)->nodeValue;
		$pathnode = $row->getElementsByTagName("path")->item(0);
			if($pathnode){
				$sitenodelist = $pathnode->getElementsByTagName("site");
			}else{
				$sitenodelist = NULL;
			}
		$c_filename = $fstat_data_dir."stat/".date("Y/m/d", $v_uti).".xml";
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td title=\"".$v_uip." (".$v_uhost.")\">".$tmpcount.".</td>\n";
		echo "\t\t\t\t<td><a href=\"".$c_filename."\">".date("d.m.y", $v_uti)."</a></td>\n";
		echo "\t\t\t\t<td>".date("H:i", $v_uti)."</td>\n";
		echo "\t\t\t\t<td class=\"icell\"><div class=\"country_icon\" style=\"background-position: 0px ".Country::getCountryOffset($v_ucoi)."px;\" title=\"".$v_ucon."\"></div></td>\n";
		echo "\t\t\t\t<td title=\"".$v_uip." (".$v_uhost.")\">".$v_ucon."</td>\n";
		echo "\t\t\t\t<td class=\"icell\"><img src=\"".$fstat_ico_dir."agent/".$v_uico."\" alt=\"*\" title=\"".$v_ufam."\" width=\"16\" height=\"16\"></td>\n";
		echo "\t\t\t\t<td title=\"".$v_uas."\">".$v_unam."</td>\n";
		echo "\t\t\t\t<td title=\"".$v_rkey."\">".$v_rdom."</td>\n";
		
		$tmp_last_1 = "";
		$tmp_last_2 = "";
		
		if($sitenodelist){
			echo "\t\t\t\t<td title=\"";
			foreach($sitenodelist as $v_sites){
				$val = $v_sites->nodeValue;
				list($val1, $val2) = explode("/", trim($val), 2);
				
				if($tmp_last_1 != $val1){
					echo $val1.": ".$val2.", ";
				}else{
					if($val2 != $tmp_last_2){
						echo $val2.", ";
					}
				}
				$tmp_last_1 = $val1;
				$tmp_last_2 = $val2;
			}
			echo "\">".FLANG_H_SITE."</td>\n";
		}else{
			echo "\t\t\t\t<td></td>\n";
		}
	
		echo "\t\t\t</tr>\n";
	}
?>
		</table>
	</div>
