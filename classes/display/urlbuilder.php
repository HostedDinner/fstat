<?php

/**
 * URLBuilder
 *
 * @author Fabian
 */
class URLBuilder {
    
    private $page;
    private $language;
    
    public function __construct($show, $language) {
        $this->language = $language;
        
        if($show != null){
                $this->page = preg_replace('#[^0-9a-z]#i', '' , $show);//alles ausser 0-9 und A-Z mit nichts ersetzten
        }else{
                $this->page = 'overview';
        }
    }
    
    
    public function build($newpage = null, $newyear = null, $newmonth = null, $newlang = null, $extra = null){
        
        if($newpage == null){
            $newpage = $this->page;
        }
        
        $temp = "?show=".$newpage;
        
        
        if($newyear != null){
            $temp .= "&amp;year=".$newyear;
        }
        
        if($newmonth != null){
            $temp .= "&amp;month=".$newmonth;
        }
        
        
        if($newlang == null){
            $newlang = $this->language;
        }
        $temp .= "&amp;lang=".$newlang;
        
        if($extra != null){
            $temp .= "&amp;" . $extra;
        }
        
	return $temp;
        
    }


    
    
    public function getPage(){
        return $this->page;
    }
    
}
