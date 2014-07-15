<?php

require_once __DIR__ . "/../analyse/country.php";

/**
 * Overview
 *
 * @author Fabian
 */
class Overview {
    
    private $xpath;
    
    //$all_xpath a xpath object, which is initilised on the dom
    public function __construct(&$all_xpath) {
        $this->xpath = &$all_xpath;
    }
    
    
    
    /***********************************
     *               List              *
     *       Browser / OS / Bot        *
     ***********************************
      $type (for example "browser", "os", "bot")
      $icontype (for example "agent" or "os")
      $header2 (FLANG_BROWSER, FLANG_OS, FLANG_BOT_S)
      ($ico_dir = $fstat_ico_dir)
     */
    public function ovlist($type, $icontype, $header2, $ico_dir){
?>
            <table class="Auflistung">
                <tr>
                    <th colspan="2"><?php echo $header2; ?>:</th>
                    <th><?php echo FLANG_COUNT; ?>:</th>
                    <th><?php echo FLANG_GRAPH; ?>:</th>
                </tr>
<?php
    $count_all = $this->xpath->evaluate('sum(//'.$type.'/typ/all/count/text())');
    $count_max = $this->xpath->evaluate('string(//'.$type.'/typ/all/count[not(//'.$type.'/typ/all/count > text())]/text())');

    $nodelist = $this->xpath->query('//'.$type.'/typ');

    $tmpcount = 0;
    foreach ($nodelist as $node) :
        $tmpcount++;
        $name = $node->getAttribute("name");
        $icon = $this->xpath->evaluate('string(./all[1]/icon[1]/text())', $node);
        $count = $this->xpath->evaluate('string(./all[1]/count[1]/text())', $node);
        $perc = $count_all != 0 ? round(($count / $count_all) * 100, 1) : 0;
        $perc_relative = $count_max != 0 ? round(($count / $count_max) * 100, 1) : 0;

        $namestrip = preg_replace('#[^a-z0-9]#i', '', $name);
?>
                <tr<?php echo ($tmpcount % 2 == 0) ? "" : " class=\"backhigh\""; ?>>
                    <td class="icell"><img src="<?php echo $ico_dir.$icontype."/".$icon; ?>" alt="*" width="16" height="16"></td>
                    <td><a href="javascript:showhide('<?php echo $type."_".$namestrip; ?>')"><?php echo $name; ?></a></td>
                    <td><?php echo $count; ?></td>
                    <td class="perc" title="<?php echo $perc; ?>%"><div style="width:<?php echo $perc_relative; ?>%"></div></td>
                </tr>
<?php
        $nodelist2 = $this->xpath->query('./sub', $node);

        foreach ($nodelist2 as $nodesub):
            $name = $nodesub->getAttribute("version");
            $icon = $this->xpath->evaluate('string(./icon[1]/text())', $nodesub);
            $count = $this->xpath->evaluate('string(./count[1]/text())', $nodesub);
?>
                <tr class="<?php echo ($tmpcount % 2 == 0) ? "": "backhigh "; echo $type."_".$namestrip; ?>" style="display:none;">
                    <td class="icell"><img src="<?php echo $ico_dir.$icontype."/".$icon; ?>" alt="*" width="16" height="16"></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $count; ?></td>
                    <td>&nbsp;</td>
                </tr>
<?php
        endforeach;
    endforeach;
?>
<?php if($count_all == 0): ?>
                <tr><td colspan="4"><div class="text"><?php echo FLANG_NODATA; ?></div></td></tr>
<?php endif; ?>
            </table>
<?php
    }
    
    
    
    /***********************************
     *              List 2             *
     *        Search / Referer         *
     ***********************************
     * $type (for example "search", "referer")
     * $header2 (for example FLANG_SEARCHW, FLANG_DOMAIN)
     * $attribute (for example "keywords", "domain")
     */
    public function ovlist2($type, $header2, $attribute){
?>
            <table class="Auflistung striped">
                <tr>
                    <th><?php echo $header2; ?>:</th>
                    <th><?php echo FLANG_COUNT; ?>:</th>
                    <th><?php echo FLANG_GRAPH; ?>:</th>
                </tr>
<?php
    $count_all = $this->xpath->evaluate('sum(//'.$type.'/ref/count/text())');
    $count_max = $this->xpath->evaluate('string(//'.$type.'/ref/count[not(//'.$type.'/ref/count > text())]/text())');

    $nodelist = $this->xpath->query('//'.$type.'/ref');

    foreach($nodelist as $node){

        $string = $node->getAttribute($attribute);
        $count = $this->xpath->evaluate('string(./count[1]/text())', $node);
        $perc = $count_all != 0 ? round(($count/$count_all)*100, 1) : 0;
        $perc_relative = $count_max != 0 ? round(($count/$count_max)*100, 1) : 0;

?>
                <tr>
                    <td><?php echo $string; ?></td>
                    <td><?php echo $count; ?></td>
                    <td class="perc" title="<?php echo $perc; ?>%"><div style="width:<?php echo $perc_relative; ?>%"></div></td>
                </tr>
<?php
    }
?>
<?php if($count_all == 0): ?>
                <tr><td colspan="3"><div class="text"><?php echo FLANG_NODATA; ?></div></td></tr>
<?php endif; ?>
            </table>
<?php
    }
    
    
    
    /***********************************
     *     Distribution Diagram        *
     ***********************************
     * $count_day_max
     * $databoard_height (50 or 100)
     * $type ("bots" or "people")
     * $text_name (FLANG_BOT_S/FLANG_VISITOR_S)
     * $text_visit (FLANG_VISITTIME_B/FLANG_VISITTIME_D)
     * $lang = $lang ("global" in index.php)
     */
    public function distribution($count_day_max, $databoard_height, $type, $text_name, $text_visit, $lang){
if($count_day_max > 0): ?>
            <table class="databoard_<?php echo $databoard_height; ?>">
                <tr>
<?php
    $count_last = 0;
    $count_all = 0;
    $count_count = 0;

    $nodelist = $this->xpath->query('//counter/day');
    foreach($nodelist as $nodeday):
        $count = $this->xpath->evaluate('string(./'.$type.'/text())', $nodeday);
        $count_all = $count_all + $count;
        $count_count++;
        $date = new DateTime($nodeday->getAttribute("id"));
        $day = $date->format('j');
        $month = $lang->monthnames[$date->format('n') - 1];

        $height = round(($count/$count_day_max)*$databoard_height,0);//$databoard_height (eg 50 or 100) max
        $height2 = round((($count_all/$count_count)/$count_day_max)*$databoard_height , 0) - 1; //$databoard_height (eg 50 or 100) max //bars come down 2px ;)
        $height2 *= -1;
?>
                    <td title="<?php echo $count." ".$text_visit." ".$day.". ".$month." (".sprintf("%+d", $count-$count_last)." ".$text_name.")"; ?>">
                        <div class="graph" style="height:<?php echo $height; ?>px;"></div>
                        <div class="dots" style="top:<?php echo $height2; ?>px;"></div>
                    </td>
<?php
        $count_last = $count;
    endforeach;
?>
                </tr>
            </table>
<?php else: ?>
            <div class="text"><?php echo FLANG_NODATA; ?></div>
<?php endif;
    }
    
    
    /***********************************
     *   Time Distribution Diagram     *
     ***********************************
     * $count_time_max
     * $databoard_height (50 or 100)
     * $type ("bots" or "people")
     * $text_name (FLANG_BOT_S/FLANG_VISITOR_S)
     * $text_visit (FLANG_VISITTIME_B/FLANG_VISITTIME_D)
     */
    public function time_distribution($count_time_max, $databoard_height, $type, $text_name, $text_visit){
if($count_time_max > 0): ?>
            <table class="databoard_<?php echo $databoard_height; ?>">
                <tr>
<?php
    $count_all = 0;

    $nodelist = $this->xpath->query('//times/time');
    foreach($nodelist as $nodetime):
        $count = $this->xpath->evaluate('string(./'.$type.'/text())', $nodetime);
        $count_all = $count_all + $count;
        $period = $nodetime->getAttribute("period");

        $height = round(($count/$count_time_max)*$databoard_height,0);//$databoard_height (eg 50 or 100) max
?>
                    <td title="<?php echo $count." ".$text_visit." ".$period." ".$text_name; ?>">
                        <div class="graph" style="height:<?php echo $height; ?>px; top:0px;"></div>
                    </td>
<?php
    endforeach;
?>
                </tr>
            </table>
<?php else: ?>
            <div class="text"><?php echo FLANG_NODATA; ?></div>
<?php endif;
    }
    
    
    
    
    /***********************************
     *            Country List         *
     ***********************************/
    public function country_list(){
?>
            <h2><?php echo FLANG_H_COUNTRY; ?></h2>
            <table class="Auflistung">
                <tr>
                    <th colspan="2"><?php echo FLANG_COUNTRY; ?>:</th>
                    <th><?php echo FLANG_COUNT; ?>:</th>
                    <th><?php echo FLANG_GRAPH; ?>:</th>
                </tr>
<?php
    $count_all = $this->xpath->evaluate('sum(//country/cou/count/text())');
    $count_max = $this->xpath->evaluate('string(//country/cou/count[not(//country/cou/count > text())]/text())');

    $nodelist = $this->xpath->query('//country/cou');

    $tmpcount = 0;
    foreach ($nodelist as $node) :
        $tmpcount++;
        $name = $node->getAttribute("name");
        $icon = $this->xpath->evaluate('string(./icon[1]/text())', $node);
        $count = $this->xpath->evaluate('string(./count[1]/text())', $node);
        $perc = $count_all != 0 ? round(($count / $count_all) * 100, 1) : 0;
        $perc_relative = $count_max != 0 ? round(($count / $count_max) * 100, 1) : 0;
?>
                <tr<?php echo ($tmpcount % 2 == 0) ? "" : " class=\"backhigh\""; ?>>
                    <td class="icell"><div class="country_icon" style="background-position: 0px <?php echo Country::getCountryOffset($icon); ?>px"></div></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $count; ?></td>
                    <td class="perc" title="<?php echo $perc; ?>%"><div style="width:<?php echo $perc_relative; ?>%"></div></td>
                </tr>
<?php
    endforeach;
?>
<?php if($count_all == 0): ?>
                <tr><td colspan="4"><div class="text"><?php echo FLANG_NODATA; ?></div></td></tr>
<?php endif; ?>
            </table>
<?php
    }
}
