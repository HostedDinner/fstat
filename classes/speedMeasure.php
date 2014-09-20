<?php
/**
 * Description of speedMeasure
 *
 * @author Fabian
 */
class SpeedMeasure {
    
    private $startzeit;
    private $started;
    
    public function __construct() {
        $this->started = false;
    }
    
    public function start(){
        $arr = explode(" ", microtime());
        $this->startzeit = $arr[0] + $arr[1];
        $this->started = true;
    }
    
    public function stop(){
        if($this->started){
            $arr = explode(" ", microtime());
            $end = $arr[0] + $arr[1];
            $this->started = false;
            return round($end - $this->startzeit,4);
        }else{
            $this->started = false;
            return "--";
        }
    }
}
