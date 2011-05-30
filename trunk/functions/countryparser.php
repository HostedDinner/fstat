<?php
function CountryParse(){
	$lookupip = getenv("REMOTE_ADDR");
	$lookupip_long = sprintf("%u", ip2long($lookupip));
		
	$returnar = CountryParseHelper(FSTAT_PATH."ip-to-country-1.csv", $lookupip_long);
		
	if($returnar === false){
		$returnar = CountryParseHelper(FSTAT_PATH."ip-to-country-2.csv", $lookupip_long);
	}
		
	if($returnar === false){
		if(!isset($returnar['icon'])){
			$returnar['icon'] = "fam.png";
		}
		if(!isset($returnar['name'])){
			$returnar['name'] = "Unknown";
		}
	}
		
	return $returnar;
}
	
	
function CountryParseHelper($filename, $lookupip_long){
	if(is_readable($filename)){
		$handle = fopen($filename,"r");
			
		if($handle){
			//Seek to the end
			fseek($handle, 0, SEEK_END);
			$high = ftell($handle);
			$low = 0;
			
			while ($low <= $high) {
				$mid = floor(($low + $high) / 2);
		
				fseek($handle, $mid);
	
				if($mid != 0){
					//Read a line to move to eol
					$line = fgets($handle);
				}
		
				//Read a line to get data
				$data = fgetcsv ($handle, 200, ",");
				
				if(($data[0] <= $lookupip_long) and ($lookupip_long <= $data[1])){
					$returnar['icon'] = strtolower($data[2]).".png";
					$returnar['name'] = ucwords(strtolower($data[4]));
					fclose($handle);
					return $returnar;
				}else{
					if ($lookupip_long < $data[0]){
						$high = $mid - 1;
					}else{
						$low = $mid + 1;
					}
				}
			}
		}
		fclose($handle);
	}
	return false;
}
	
?>