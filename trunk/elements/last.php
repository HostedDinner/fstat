    <div class="border">
        <table class="Auflistung striped">
            <tr>
                <th>&nbsp;</th>
                <th><?php echo FLANG_DATE; ?>:</th>
                <th><?php echo FLANG_TIME; ?>:</th>
                <th colspan="2"><?php echo FLANG_COUNTRY; ?>:</th>
<?php if($urlBuilder->getPage() == 'last'): ?>
                <th colspan="2"><?php echo FLANG_OS; ?>:</th>
<?php endif; ?>
                <th colspan="2"><?php echo FLANG_BROWSER; ?>:</th>
                <th><?php echo FLANG_DOMAIN; ?>:</th>
                <th><?php echo FLANG_H_SITE; ?>:</th>
            </tr>
<?php
    require_once __DIR__ . "/../classes/xpathHelper.php";

    $xml_last = new DOMDocument();
    if($urlBuilder->getPage() == 'last'){
        $xml_last->loadXML(Backend::getXML("./backend/last.php"));
    }else if($urlBuilder->getPage() == 'lastbots'){
        $xml_last->loadXML(Backend::getXML("./backend/lastbots.php"));
    }
    
    $xpath = new DOMXPath($xml_last);

    $nodelist = $xpath->query('//visitor');

    $tmpcount = 0;
?>
<?php foreach ($nodelist as $row) :
        $tmpcount++;

        $v_typ = $xpath->evaluate('string(./typ/text())', $row);
        $v_uas = $xpath->evaluate('string(./uas/text())', $row);
        $v_uip = $xpath->evaluate('string(./uip/text())', $row);
        $v_uhost = $xpath->evaluate('string(./uhost/text())', $row);
        if ($v_uhost == "") { $v_uhost = $v_uip;}
        $v_uti = $xpath->evaluate('string(./uti/text())', $row);
        $v_ufam = $xpath->evaluate('string(./ufam/text())', $row);
        $v_unam = $xpath->evaluate('string(./unam/text())', $row);
        $v_uico = $xpath->evaluate('string(./uico/text())', $row);
        $v_ofam = $xpath->evaluate('string(./ofam/text())', $row);
        $v_onam = $xpath->evaluate('string(./onam/text())', $row);
        $v_oico = $xpath->evaluate('string(./oico/text())', $row);
        $v_ucoi = $xpath->evaluate('string(./ucoi/text())', $row);
        $v_ucon = $xpath->evaluate('string(./ucon/text())', $row);
        $v_rkey = $xpath->evaluate('string(./rkey/text())', $row);
        $v_rdom = $xpath->evaluate('string(./rdom/text())', $row);
        $sitenodelist = $xpath->query('./path/site', $row);
        $c_filename = $fstat_data_dir . "stat/" . date("Y/m/d", $v_uti) . ".xml";
?>
            <tr>
                <td title="<?php echo $v_uip." (".$v_uhost.")"; ?>"><?php echo $tmpcount; ?></td>
                <td><a href="<?php echo $c_filename; ?>"><?php echo date("d.m.y", $v_uti); ?></a></td>
                <td><?php echo date("H:i", $v_uti); ?></td>
                <td class="icell"><div class="country_icon" style="background-position: 0px <?php echo Country::getCountryOffset($v_ucoi); ?>px" title="<?php echo $v_ucon; ?>"></div></td>
                <td title="<?php echo $v_uip." (".$v_uhost.")"; ?>"><?php echo $v_ucon; ?></td>
<?php if($urlBuilder->getPage() == 'last'): ?>
                <td class="icell"><img src="<?php echo $fstat_ico_dir . "os/" . $v_oico; ?>" alt="*" title="<?php echo $v_ofam; ?>" width="16" height="16"></td>
                <td title="<?php echo $v_uas; ?>"><?php echo $v_onam; ?></td>
<?php endif; ?>
                <td class="icell"><img src="<?php echo $fstat_ico_dir . "agent/" . $v_uico; ?>" alt="*" title="<?php echo $v_ufam; ?>" width="16" height="16"></td>
                <td title="<?php echo $v_uas; ?>"><?php echo $v_unam; ?></td>
                <td title="<?php echo $v_rkey; ?>"><?php echo $v_rdom; ?></td>
<?php if ($sitenodelist->length != 0) : ?>
                <td title="<?php echo XPathHelper::listSiteNodeValues($sitenodelist); ?>"><?php echo FLANG_H_SITE; ?></td>
<?php else : ?>
                <td></td>
<?php endif; ?>
            </tr>
<?php endforeach; ?>
        </table>
    </div>
