<?php

/**
 * DisplayTime
 *
 * @author Fabian
 */
class DisplayTime {
    
    private $startTime;
    private $endTime;
    
    
    public function __construct() {
        $this->startTime = new DateTime();
        $this->endTime = new DateTime();
    }
    
    
    
    
    
    public function setUnsafeStartDate($syear, $smonth, $sday = 1){
        $this->setUnsafeDate('start', $syear, $smonth, $sday);
    }
    
    public function setUnsafeEndDate($syear, $smonth, $sday = 31){
        $this->setUnsafeDate('end', $syear, $smonth, $sday);
    }
    
    
    
    
    public function getStartYear(){
        return $this->startTime->format("Y");
    }
    
    public function getStartMonth(){
        return $this->startTime->format("n");
    }
    
    public function getStartDay(){
        return $this->startTime->format("j");
    }
    
    
    public function getEndYear(){
        return $this->endTime->format("Y");
    }
    
    public function getEndMonth(){
        return $this->endTime->format("n");
    }
    
    public function getEndDay(){
        return $this->endTime->format("j");
    }
    
    
    public function getPreviousStartMonth(){
        $tuple = array();
        $tuple['month'] = $this->getStartMonth() - 1;
	$tuple['year'] = $this->getStartYear();
	if($tuple['month'] == 0){
		$tuple['month'] = 12;
		$tuple['year'] = $tuple['year'] - 1;
	}
        return $tuple;
    }
    
    public function getNextStartMonth(){
        $tuple = array();
        $tuple['month'] = $this->getStartMonth() + 1;
	$tuple['year'] = $this->getStartYear();
	if($tuple['month'] == 13){
		$tuple['month'] = 1;
		$tuple['year'] = $tuple['year'] + 1;
	}
        return $tuple;
    }
    
    public function getPreviousEndMonth(){
        $tuple = array();
        $tuple['month'] = $this->getEndMonth() - 1;
	$tuple['year'] = $this->getEndYear();
	if($tuple['month'] == 0){
		$tuple['month'] = 12;
		$tuple['year'] = $tuple['year'] - 1;
	}
        return $tuple;
    }
    
    public function getNextEndMonth(){
        $tuple = array();
        $tuple['month'] = $this->getEndMonth() + 1;
	$tuple['year'] = $this->getEndYear();
	if($tuple['month'] == 13){
		$tuple['month'] = 1;
		$tuple['year'] = $tuple['year'] + 1;
	}
        return $tuple;
    }
    
    
    
    
    //is actually called from setUnsafe(Start|End)Date
    //type is "start" or "end"
    private function setUnsafeDate($type, $syear, $smonth, $sday){
        if($syear != null){
		$year = preg_replace('#[^0-9]#i', '' , $syear);//alles ausser 0-9 mit nichts ersetzten
                if($year == ""){ //zB wenn alles weggekürzt wurde
                    $year = gmdate("Y");
                }
	}else{
		$year = gmdate("Y");
	}
	
	if($smonth != null){
		$month = preg_replace('#[^0-9]#i', '', $smonth);//alles ausser 0-9 mit nichts ersetzten
		if(($month == "") || ($month > 12) || ($month == 0)){
			$month = gmdate("n");
		}
	}else{
		$month = gmdate("n");
	}
        
        if($sday != null){
		$day = preg_replace('#[^0-9]#i', '', $sday);//alles ausser 0-9 mit nichts ersetzten
		if(($day == "") || ($day > 31) || ($day == 0)){
			$day = gmdate("j");
		}
	}else{
		$day = gmdate("j");
	}
        
        
        switch ($type){
            case 'end':
                $this->endTime->setDate($year, $month, $day);
                break;
            case 'start':
            default:
                $this->startTime->setDate($year, $month, $day);
                break;
        }
    }
    
    
    
}
