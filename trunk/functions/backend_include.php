<?php	if(!isset($fstat_backend_month)){		if(isset($_GET['month'])){			$fstat_backend_month = preg_replace('#[^0-9]#i','',$_GET['month']);//alles ausser 0-9 mit nichts ersetzten			if(($fstat_backend_month > 12) || ($fstat_backend_month == 0)){				$fstat_backend_month = date("m");			}		}else{			$fstat_backend_month = date("m");		}	}//else ist gesetzt		if(!isset($fstat_backend_year)){		if(isset($_GET['year'])){			$fstat_backend_year = preg_replace('#[^0-9]#i','',$_GET['year']);//alles ausser 0-9 mit nichts ersetzten		}else{			$fstat_backend_year = date("Y");		}	}//else ist gesetzt		if(!isset($fstat_backend_timestamp)){		$fstat_backend_timestamp = mktime(1, 1, 1, $fstat_backend_month, 1, $fstat_backend_year);	}//else ist gesetzt?>