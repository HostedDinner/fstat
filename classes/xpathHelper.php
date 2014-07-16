<?php
/**
 * Description of XPathHelper
 *
 * @author Fabian
 */
class XPathHelper {
    static function listSiteNodeValues(&$nodelist){
        $tmp_last_1 = "";
        $tmp_last_2 = "";
        $result = "";
        
        foreach ($nodelist as $v_sites) {
            $val = $v_sites->nodeValue;
            list($val1, $val2) = explode("/", trim($val), 2);

            if ($tmp_last_1 != $val1) {
                $result .= $val1 . ": " . $val2 . ", ";
            } else if ($val2 != $tmp_last_2) {
                $result .= $val2 . ", ";
            }
            $tmp_last_1 = $val1;
            $tmp_last_2 = $val2;
        }
        
        $result = substr($result, 0, -2); //kill the last ", "
        
        return $result;
    }
}
?>