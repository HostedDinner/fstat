<?php

    require_once __DIR__ . "/../classes/display/overview.php";
    
    //
    //var_dump(Backend::getXML(__DIR__ . "/../backend/all.php"));
    $xml_all = new DOMDocument();
    $xml_all->loadXML(Backend::getXML(__DIR__ . "/../backend/all.php"));
    
    //$_SESSION['xml'] = $xml_all->saveXML();
    
    $all_xpath = new DOMXPath($xml_all);
    
    $overview = new Overview($all_xpath);
    
?>
    <div class="left">
        <div class="border">
            <h2><?php echo FLANG_H_OVERVIEW; ?></h2>
            <table class="Auflistung striped" style="text-align:right;">
                <thead>
                <tr>
                    <th><?php echo FLANG_DAY; ?>:</th>
                    <th><?php echo FLANG_VISITOR_S; ?>:</th>
                    <th><?php echo FLANG_BOT_S; ?>:</th>
                </tr>
                </thead>
                <tbody>
<?php
    
    //here we do not use the fancy xpath to find the highest, because anyway we have to travel the dom
    $count_day_max_b = 0;//reset/set
    $count_day_max_p = 0;//reset/set
    $count_days = 0;
    
    $nodelist = $all_xpath->query('//counter/day');
	
    foreach($nodelist as $nodeday):
        $count_days++;
        $count_b = $all_xpath->evaluate('string(./bots[1]/text())', $nodeday);
        $count_p = $all_xpath->evaluate('string(./people[1]/text())', $nodeday);
        
        if($count_b > $count_day_max_b){$count_day_max_b = $count_b;}
        if($count_p > $count_day_max_p){$count_day_max_p = $count_p;}

        $date = new DateTime($nodeday->getAttribute("id"));
        $day = $date->format('j');
        $month = $lang->monthnames[$date->format('n') - 1];

        if(gmdate("Y-m-d") == $date->format("Y-m-d")){
            $class = " class=\"high_today\"";
            $day = "<span class=\"today_arrow\">&#x25BA;</span>&nbsp;".$day; //adds a Pointer in Front of today
        }elseif($date->format('w') == 6){//6 for Saturday
            $class = " class=\"high_Sa\"";
        }elseif($date->format('w') == 0){//0 for Sunday
            $class = " class=\"high_Su\"";
        }else{
            $class = "";
        }
?>
                <tr title="<?php echo $date->format('j').". ".$month; ?>">
                    <td<?php echo $class; ?>><?php echo $day; ?></td>
                    <td<?php echo $class; ?>><?php echo $count_p; ?></td>
                    <td<?php echo $class; ?>><?php echo $count_b; ?></td>
                </tr>
<?php
    endforeach;
    
    $count_all_b = $all_xpath->evaluate('string(//counter[1]/total[1]/bots[1]/text())');
    $count_all_p = $all_xpath->evaluate('string(//counter[1]/total[1]/people[1]/text())');
    
    $counter_av_p = $count_days != 0 ? round($count_all_p/$count_days, 1) : 0;
    $counter_av_b = $count_days != 0 ? round($count_all_b/$count_days, 1) : 0;
?>
                </tbody>
                <tfoot>
                <tr class="table_sum">
                    <td>&sum;</td>
                    <td><?php echo $count_all_p; ?></td>
                    <td><?php echo $count_all_b; ?></td>
                </tr>
                <tr class="table_sum2">
                    <td>&Oslash;</td>
                    <td><?php echo $counter_av_p; ?></td>
                    <td><?php echo $counter_av_b; ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="right">
        <div class="border">
            <h2><?php echo FLANG_H_BROWSER; ?></h2>
<?php
    $overview->ovlist('browser', 'agent', FLANG_BROWSER, $fstat_ico_dir);
?>
        </div>
        <div class="border">
            <h2><?php echo FLANG_H_OS; ?></h2>
<?php
    $overview->ovlist('os', 'os', FLANG_OS, $fstat_ico_dir);
?>
        </div>
        <div class="border">
            <h2><?php echo FLANG_H_BOT; ?></h2>
<?php
    $overview->ovlist('bot', 'agent', FLANG_BOT_S, $fstat_ico_dir);
?>
            <br />
<?php
    $overview->distribution($count_day_max_b, 50, 'bots', FLANG_BOT_S, FLANG_VISITTIME_B, $lang);
?>
        </div>
        <div class="border">
<?php 
    $overview->country_list();
?>
        </div>
        <div class="border">
            <h2><?php echo FLANG_HOST; ?></h2>
<?php 
    $overview->ovlist2('hosts', FLANG_HOST, 'host', 'host');
?>
        </div>
        <div class="border">
            <h2><?php echo FLANG_H_IP_VERSION; ?></h2>
<?php 
    $overview->ovlist2('ipv', FLANG_H_IP_VERSION, 'version', 'ip');
?>
        </div>
    </div>
    <div class="middle">
        <div class="border">
            <h2><?php echo FLANG_H_DIST_VISIT; ?></h2>
<?php
    $overview->distribution($count_day_max_p, 100, 'people', FLANG_VISITOR_S, FLANG_VISITTIME_D, $lang);
?>
        </div>
        <div class="border">
<?php
	$xml_sites = new DOMDocument();
        $xml_sites->loadXML(Backend::getXML(__DIR__ . "/../backend/sitecounter.php"));
        
        $sites_xpath = new DOMXPath($xml_sites);
        
	$updatetime = $sites_xpath->evaluate('string(//sites/@update)'); //$xml_sites->getElementsByTagName("sites")->item(0)->getAttribute("update");
?>
            <h2><?php echo FLANG_H_SITE; ?></h2>
            <div class="infobox"><?php
                echo FLANG_CACHED . " " . (new DateTime($updatetime))->format($fstat_date_format). " ";
                echo "<a href=\"./".$urlBuilder->build(null, $displayTime->getStartYear(), $displayTime->getStartMonth())."&amp;refresh=1\">".FLANG_RELOAD."</a>";
            ?></div>
            <table class="Auflistung striped">
                <thead>
                <tr>
                    <th><?php echo FLANG_SITE; ?>:</th>
                    <th><?php echo FLANG_VISITOR_L; ?>:</th>
                    <th><?php echo FLANG_GRAPH; ?>:</th>
                    <th><?php echo FLANG_BOT_L; ?>:</th>
                </tr>
                </thead>
                <tbody>
<?php
    //count all
    $count_all_p = $sites_xpath->evaluate('string(//sites[1]/total[1]/people[1]/text())');
    $count_max_p = $sites_xpath->evaluate('string(//sites/site/sub/people[not(//sites/site/sub/people > text())]/text())');

    $count_all_b = $sites_xpath->evaluate('string(//sites[1]/total[1]/bots[1]/text())');
    
    
    $nodelist = $sites_xpath->query('//sites/site');

    foreach($nodelist as $node){
        if($fstat_use_site_var == true):
?>
                <tr>
                    <th class="center" colspan="4"><?php echo $node->getAttribute("name"); ?></th>
                </tr>
<?php
        endif;

        $subnodelist = $sites_xpath->query('./sub', $node);
        
        foreach ($subnodelist as $subnode) {
            $name = $subnode->getAttribute("name");

            $count_p = $sites_xpath->evaluate('string(./people/text())', $subnode);
            $count_b = $sites_xpath->evaluate('string(./bots/text())', $subnode);
            $perc_p = $count_all_p != 0 ? round(($count_p/$count_all_p)*100, 1) : 0;
            $perc_p_relative = $count_max_p != 0 ? round(($count_p/$count_max_p)*100, 1) : 0;
?>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $count_p; ?></td>
                    <td class="perc" title="<?php echo $perc_p; ?> %"><div style="width:<?php echo $perc_p_relative; ?>%;"></div></td>
                    <td><?php echo $count_b; ?></td>
                </tr>
<?php
        }
    }
?>
<?php if($count_all_p == 0 and $count_all_b == 0): ?>
                <tr><td colspan="4"><div class="text"><?php echo FLANG_NODATA; ?></div></td></tr>
<?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="border">
            <h2><?php echo FLANG_H_REFERER; ?></h2>
<?php
    $overview->ovlist2("referer", FLANG_DOMAIN, "domain");
?>
        </div>
        <div class="border">
            <h2><?php echo FLANG_H_SEARCHW; ?></h2>
<?php
    $overview->ovlist2("search", FLANG_SEARCHW, "keywords");
?>
        </div>
        <div class="border">
            <h2><?php echo FLANG_H_DIST_TIME; ?></h2>
<?php
    $count_time_max = $all_xpath->evaluate('string(//times/time/people[not(//times/time/people > text())]/text())');
    $overview->time_distribution($count_time_max, 100, 'people', FLANG_CLOCK, FLANG_VISITTIME_T);
?>
        </div>
    </div>
