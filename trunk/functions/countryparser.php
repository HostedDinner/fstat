<?php
	function CountryParse(){
		$lookupip = getenv("REMOTE_ADDR");
		$lookupip_long = sprintf("%u", ip2long($lookupip));
		
		$c_found = false;
		
		//part 1
		$handle = fopen (FSTAT_PATH."ip-to-country-1.csv","r");
		
		if($handle){
			while ( ($data = fgetcsv ($handle, 200, ",")) !== FALSE ) {
				if(($data[0] <= $lookupip_long) and ($lookupip_long <= $data[1])){
					$returnar['icon'] = strtolower($data[2]).".png";
					$returnar['name'] = ucwords(strtolower($data[4]));
					$c_found = true;
					break;
				}
			}
		}
		fclose ($handle);
		
		//part 2
		if(!$c_found){
			$handle2 = fopen (FSTAT_PATH."ip-to-country-2.csv","r");
		
			if($handle2){
				while ( ($data = fgetcsv ($handle2, 200, ",")) !== FALSE ) {
					if(($data[0] <= $lookupip_long) and ($lookupip_long <= $data[1])){
						$returnar['icon'] = strtolower($data[2]).".png";
						$returnar['name'] = ucwords(strtolower($data[4]));
						break;
					}
				}
			}
			fclose ($handle2);
		}
		
		
		
		if(!isset($returnar['icon'])){
			$returnar['icon'] = "fam.png";
		}
		if(!isset($returnar['name'])){
			$returnar['name'] = "unknown";
		}
		
		
		return $returnar;
	}
?>