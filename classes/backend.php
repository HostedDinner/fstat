<?php
/**
 * Description of Backend
 *
 * @author Fabian
 */

//Config
require_once __DIR__ . "/../config/settings.php";

//Class Dependency
require_once __DIR__ . "/display/displayTime.php";

class Backend {
    
    private $listLength;
    private $modus;
    private $offset;
    private $forceRefresh;
    private $time;
    private $showList;
    private $filterDoubleBot;
    
    const MODUS_DAY = 0;
    const MODUS_MONTH = 1;
    const MODUS_YEAR = 2;
    
    
    public function __construct() {
        global $fstat_last_length;
        
        $this->listLength = $fstat_last_length;
        $this->modus = self::MODUS_MONTH; //default is month overview
        $this->offset = 0;
        $this->forceRefresh = false;
        $this->time = new DisplayTime();
        $this->showList = 'all';
        $this->filterDoubleBot = false;
    }
    
    public function setUnsafeListLength($newlength){
        $tmp = $this->listLength;
        $this->listLength = preg_replace('#[^0-9]#i', '', $newlength); //alles ausser 0-9 mit nichts ersetzten
        if($this->listLength == ""){
            $this->listLength = $tmp;
        //protection against too long lists
        }else if ($this->listLength > 200) {
            $this->listLength = 200;
        }
    }
    
    public function getListLength(){
        return $this->listLength;
    }
    
    
    public function setUnsafeModus($newmodus){
        $newmodus = preg_replace('#[^0-9]#i', '', $newmodus); //alles ausser 0-9 mit nichts ersetzten
        if(newmodus != ""){
            $this->setModus($newmodus);
        }//else do nothing
    }
    
    public function setModus($newmodus){
        if($newmodus <= 2){
            $this->modus = $newmodus;
        }
    }
    
    public function getModus(){
        return $this->modus;
    }
    
    
    public function setUnsafeOffset($newoffset) {
        $newoffset = preg_replace('#[^0-9]#i', '', $newoffset); //alles ausser 0-9 mit nichts ersetzten
        if($newoffset != ""){
            $this->offset = $newoffset;
        }
    }
    
    public function setOffset($newoffset){
        $this->offset = $newoffset;
    }
    
    
    public function getOffset(){
        return $this->offset;
    }
    
    public function setUnsafeForceRefresh($newRefresh){
        $newRefresh = preg_replace('#[^0-9]#i', '', $newRefresh); //alles ausser 0-9 mit nichts ersetzten
        switch ($newRefresh){
            case 1:
                $this->forceRefresh = true;
                break;
            default:
                $this->forceRefresh = false;
        }
    }
    
    public function setForceRefresh($newRefresh){
        $this->forceRefresh = (bool) $newRefresh;
    }
    
    public function getForceRefresh(){
        return $this->forceRefresh;
    }
    
    public function setTime($newtime){
        $this->time = $newtime;
    }
    
    public function getTime(){
        return $this->time;
    }
    
    
    public function setUnsafeShowList($newlist){
        $newlist = preg_replace('#[^0-9a-z|]#i', '', $newlist); //alles ausser 0-9 und A-Z mit nichts ersetzten
        if ($newlist == "") {
            $this->showList = "all";
        }else{
            $this->showList = $newlist;
        }
    }
    
    public function setShowList($newlist){
        $this->showList = $newlist;
    }
    
    public function getShowList(){
        return $this->showList;
    }
    
    function setFilterDoubleBot($filterDoubleBot) {
        $this->filterDoubleBot = $filterDoubleBot;
    }
    
    function getFilterDoubleBot() {
        return $this->filterDoubleBot;
    }  
    
    public static function getXML($filename, $year = null, $month = null){
        if (is_file($filename)) {

            if ($year != null) {
                $_GET['year'] = $year; //the object will fetch it again
            }
            if ($month != null) {
                $_GET['month'] = $month; //the object will fetch it again
            }

            $is_include = true;
            ob_start();
                include $filename;
                $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        } else {
            return false;
        }
    }
}
