	<div class="border">
		<table class="Auflistung">
			<tr>
				<th>&nbsp;</th>
				<th><?php echo FLANG_DATE; ?>:</th>
				<th><?php echo FLANG_TIME; ?>:</th>
				<th>&nbsp;</th>
				<th><?php echo FLANG_BROWSER; ?>:</th>
				<th>&nbsp;</th>
				<th><?php echo FLANG_OS; ?>:</th>
				<th>&nbsp;</th>
				<th><?php echo FLANG_COUNTRY; ?>:</th>
				<th><?php echo FLANG_DOMAIN; ?>:</th>
				<th><?php echo FLANG_H_SITE; ?>:</th>
			</tr>
<?php
	$xml_last = new DOMDocument();
	$xml_last->loadXML(get_xml_backend("./backend/last.php"));
	
	$nodelist = $xml_last->getElementsByTagName("visitor");
	
	$tmpcount = 0;
	foreach($nodelist as $row){
		$tmpcount++;
		
		$v_typ = @$row->getElementsByTagName("typ")->item(0)->nodeValue;
		$v_uas = @$row->getElementsByTagName("uas")->item(0)->nodeValue;
		$v_uip = @$row->getElementsByTagName("uip")->item(0)->nodeValue;
		$v_uti = @$row->getElementsByTagName("uti")->item(0)->nodeValue;
		$v_ufam = @$row->getElementsByTagName("ufam")->item(0)->nodeValue;
		$v_unam = @$row->getElementsByTagName("unam")->item(0)->nodeValue;
		$v_uico = @$row->getElementsByTagName("uico")->item(0)->nodeValue;
		$v_ofam = @$row->getElementsByTagName("ofam")->item(0)->nodeValue;
		$v_onam = @$row->getElementsByTagName("onam")->item(0)->nodeValue;
		$v_oico = @$row->getElementsByTagName("oico")->item(0)->nodeValue;
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
		
		if($tmpcount % 2 == 0){echo "\t\t\t<tr>\n";}else{echo "\t\t\t<tr class=\"backhigh\">\n";}
		echo "\t\t\t\t<td title=\"".$v_uip."\">".$tmpcount.".</td>\n";
		echo "\t\t\t\t<td>".date("d.m.y", $v_uti)."</td>\n";
		echo "\t\t\t\t<td>".date("H:i", $v_uti)."</td>\n";
		echo "\t\t\t\t<td class=\"icell\" title=\"".$v_ufam."\"><img src=\"".$fstat_ico_dir."agent/".$v_uico."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td title=\"".$v_uas."\">".$v_unam."</td>\n";
		echo "\t\t\t\t<td class=\"icell\" title=\"".$v_ofam."\"><img src=\"".$fstat_ico_dir."os/".$v_oico."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td title=\"".$v_uas."\">".$v_onam."</td>\n";
		echo "\t\t\t\t<td class=\"icell\" title=\"".$v_ucon."\"><img src=\"".$fstat_ico_dir."country/".$v_ucoi."\" alt=\"*\"></td>\n";
		echo "\t\t\t\t<td>".$v_ucon."</td>\n";
		echo "\t\t\t\t<td title=\"".$v_rkey."\">".$v_rdom."</td>\n";
		
		$tmp_last_1 = "";
		$tmp_last_2 = "";
		
		if($sitenodelist){
			echo "\t\t\t\t<td title=\"";
			foreach($sitenodelist as $v_sites){
				$val = $v_sites->nodeValue;
				list($val1, $val2) = explode("/", trim($val));
				
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
