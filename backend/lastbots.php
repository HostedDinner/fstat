<?php
if (!isset($is_include)) {
    header('Content-type: text/xml');
    error_reporting(0); //keine Fehler anzeigen
    //error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
    //error_reporting(E_ALL); // alle Fehler anzeigen
}

include __DIR__ . "/../config/settings.php";
include __DIR__ . "/../functions/backend_include.php"; //(re)defines $backend

$back_counter = $backend->getListLength();
$back_year = gmdate("Y");
$back_month = gmdate("m");


//Ausgabe vorbereiten:
    $xmlausgabe = new DOMDocument('1.0', 'UTF-8');
    $xmlausgabe->xmlStandalone = true;
    $xmlausgabe->preserveWhiteSpace = false;
    $xmlausgabe->formatOutput = true;
$root = $xmlausgabe->createElement("list");
$root = $xmlausgabe->appendChild($root);

//Das alteste Jahr finden
$back_oldest_year = $back_year;
if ($handle = @opendir(__DIR__ . "/../" . $fstat_data_dir . "stat/")) {
    while (false !== ($file = readdir($handle))) {
        if (is_dir(__DIR__ . "/../" . $fstat_data_dir . "stat/" . $file) && $file != "." && $file != "..") {
            if ($file < $back_oldest_year) {
                $back_oldest_year = $file;
            }
        }
    }
    closedir($handle);
}

if($backend->getFilterDoubleBot()){
    $doublefilter = array();
}


while (true) {//braucht keine Bedingung, da das Jahr abbricht... (Hoffentlich)
    $back_timestamp = gmmktime(1, 1, 1, $back_month, 1, $back_year);
    for ($i = gmdate("t", $back_timestamp); $i >= 1; $i--) {
        $filename = __DIR__ . "/../" . $fstat_data_dir . "stat/" . $back_year . "/" . str_pad($back_month, 2, "0", STR_PAD_LEFT) . "/" . str_pad($i, 2, "0", STR_PAD_LEFT) . ".xml";

        if (is_file($filename)) {
            $xmldoc = new DOMDocument();
            $xmldoc->load($filename);

            $nodelist = $xmldoc->getElementsByTagName("visitor");
            $nl = $nodelist->length;
            for ($pos = ($nl - 1); $pos >= 0; $pos--) {
                //ADD here everything
                $newnode = $xmlausgabe->importNode($nodelist->item($pos), true);

                $typ = @$newnode->getElementsByTagName("typ")->item(0)->nodeValue;
                $timestamp = @$newnode->getElementsByTagName("uti")->item(0)->nodeValue;
                $ip = @$newnode->getElementsByTagName("uip")->item(0)->nodeValue;
                $uas = @$newnode->getElementsByTagName("uas")->item(0)->nodeValue;
                $ip_read = str_replace(":", "_", $ip); //IPv6 Adresses have Problems on Win (no : allowed)
                
                //show only bots
                if ($typ != "Robot" and $typ != "Validator") {
                    continue;
                }
                
                //Double Bots (same UAS) will be skipped
                if($backend->getFilterDoubleBot()){
                    if(in_array($uas, $doublefilter)){
                        //TODO: Path of this one should be concated to the "old" one
                        continue;
                    }else{
                        $doublefilter[] = $uas;
                    }
                }

                $filename2 = __DIR__ . "/../" . $fstat_data_dir . "paths/" . $back_year . "/" . str_pad($back_month, 2, "0", STR_PAD_LEFT) . "/bot_" . $ip_read . "_" . gmdate("d_H", $timestamp) . ".path";

                if ($file2_cont = @file($filename2)) {
                    $pathnode = $xmlausgabe->createElement("path");
                    $newnode->appendChild($pathnode);
                    
                    foreach ($file2_cont as $row) {
                        if ($fstat_use_site_var) {
                            list($time, $sitename, $sitevar) = explode("|", trim($row));
                            if (!isset($sitevar)) { $sitevar = $sitename;}//compatibility
                            $pathnode->appendChild($xmlausgabe->createElement("site", $sitename . "/" . $sitevar));
                        } else {
                            list($time, $sitename) = explode("|", trim($row));
                            $pathnode->appendChild($xmlausgabe->createElement("site", $sitename));
                        }
                    }
                }

                //add to root
                $root->appendChild($newnode);

                $back_counter--;
                if ($back_counter <= 0) {
                    //complete go out of loop...
                    break 3;
                }
            }
        }
    }
    $back_month--;
    if ($back_month <= 0) {
        $back_year--;
        $back_month = 12;
    }
    if (($back_year < $back_oldest_year) or ( $back_year < 1980)) {//zweite falls etwas schief gelaufen ist
        //zu alt ;)
        break 1;
    }
}


if ($back_counter == 0) {
    $tmp = $backend->getListLength();
} else {
    $tmp = $backend->getListLength() - $back_counter;
}

$root->setAttribute("last", $tmp);
echo $xmlausgabe->saveXML();

//Aufbau der XML Datei
//<list last="20">
//  <visitor>
//    <typ>Browser</typ>
//    <uas>Mozilla/5.0 (Windows NT 6.0; rv:2.0) Gecko/20100101 Firefox/4.0</uas>
//    <uip>127.0.0.1</uip>
//    <uti>1300109848</uti>
//    <ufam>Firefox</ufam>
//    <unam>Firefox 4.0</unam>
//    <uico>firefox.png</uico>
//    <ofam>Windows</ofam>
//    <onam>Windows Vista</onam>
//    <oico>windowsvista.png</oico>
//    <ucoi>de.png</ucoi>
//    <ucon>Germany</ucon>
//    <rkey>bla blub</rkey>
//    <rdom>www.google.com</rdom>
//    <host>www.example.com</host>
//    <path>
//      <site>start</site>
//      <site>help</site>
//    </path>
//  </visitor>
//  <visitor>
//    ...
//  </visitor>
//</list>

?>

