<?php
include __DIR__ . "/config/settings.php";

require_once __DIR__ . "/classes/analyse/user.php";
require_once __DIR__ . "/classes/dirHelper.php";
require_once __DIR__ . "/classes/analyse/country.php";
require_once __DIR__ . "/classes/analyse/reference.php";
require_once __DIR__ . "/classes/analyse/security.php";
require_once __DIR__ . "/classes/analyse/UASparser.php";

$user = new User(getenv("REMOTE_ADDR"), $_SERVER['HTTP_USER_AGENT']);
$dirhelper = new DirHelper(__DIR__);

if(
    ($dirhelper->checkExists($fstat_data_dir) == false) ||
    ($dirhelper->checkExists($fstat_cache_dir) == false) ||
    ($dirhelper->checkExists($fstat_cache_dir . "ip/") == false)
){
    return 0;
}

//Delete old entrys in the Ip directory
$dirhelper->deleteOldIPs($fstat_cache_dir."ip", $fstat_new_user);
//get the user from cache, on success it is a old user is_new is false then
$user->getFromCache(__DIR__ . "/" . $fstat_cache_dir . "ip");

if ($user->is_new) {
    //Daten auswerten

    $current_folder = __DIR__ . "/" . $fstat_data_dir . "stat/" . gmdate("Y", $user->time) . "/" . gmdate("m", $user->time);
    
    if (
        ($dirhelper->checkExists($fstat_data_dir . "stat") == false) ||
        ($dirhelper->checkExists($fstat_data_dir . "stat/" . gmdate("Y", $user->time)) == false) ||
        ($dirhelper->checkExists($current_folder, true) == false)
    ){
        //quit the execution here
        return 0;
    }
    
    
    //User Agent Parser
    $parser = new UAS\Parser(__DIR__ . "/".$fstat_cache_dir, $fstat_update_interval, false, $fstat_update_auto);
    $uaa = $parser->Parse($user->agent);
    //ReferParser
    $ref = new Reference(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");
    $ref->parse();
    //Country Parser
    $country = new Country(__DIR__ . "/dbip-country-1.csv", __DIR__ . "/dbip-country-2.csv", __DIR__ . "/dbip-country-3.csv", __DIR__ . "/dbip-country-4.csv", __DIR__ . "/dbip-country-5.csv");
    $country->parse($user->ip);
    
    
    //Browser Typ ist bis jetzt nicht deklariert
    $user->type = $uaa['typ'];
    
    
    //Daten in XML schreiben:
    $tmp_filename = $current_folder . "/" . gmdate("d", $user->time) . ".xml";
    
    if (file_exists($tmp_filename)) {
        $xmldoc = new DOMDocument();
        $xmldoc->preserveWhiteSpace = false;
        $xmldoc->formatOutput = true;
        $xmldoc->load($tmp_filename);
        $root = $xmldoc->documentElement;
    } else {
        $xmldoc = new DOMDocument('1.0', 'UTF-8');
        $xmldoc->xmlStandalone = true;
        $xmldoc->preserveWhiteSpace = false;
        $xmldoc->formatOutput = true;
        $root = $xmldoc->createElement("list");
            $xmldoc->appendChild($root);
    }

    $newvisitor = $xmldoc->createElement("visitor");
    $newvisitor->appendChild($xmldoc->createElement('typ', htmlspecialchars($uaa['typ'])));
    $newvisitor->appendChild($xmldoc->createElement('uas', htmlspecialchars($user->agent)));
    $newvisitor->appendChild($xmldoc->createElement('uip', htmlspecialchars($user->ip)));
    $newvisitor->appendChild($xmldoc->createElement('uhost', htmlspecialchars(gethostbyaddr($user->ip))));
    $newvisitor->appendChild($xmldoc->createElement('uti', $user->time));
    $newvisitor->appendChild($xmldoc->createElement('ufam', htmlspecialchars($uaa['ua_family'])));
    $newvisitor->appendChild($xmldoc->createElement('unam', htmlspecialchars($uaa['ua_name'])));
    $newvisitor->appendChild($xmldoc->createElement('uico', htmlspecialchars($uaa['ua_icon'])));
    if($user->type == "Robot" or $user->type == "Validator"){
        $newvisitor->appendChild($xmldoc->createElement('uurl', htmlspecialchars($uaa['ua_url'])));
    }
    $newvisitor->appendChild($xmldoc->createElement('usec', Security::isHTTPS() ? 'HTTPS' : 'HTTP'));
    $newvisitor->appendChild($xmldoc->createElement('ofam', htmlspecialchars($uaa['os_family'])));
    $newvisitor->appendChild($xmldoc->createElement('onam', htmlspecialchars($uaa['os_name'])));
    $newvisitor->appendChild($xmldoc->createElement('oico', htmlspecialchars($uaa['os_icon'])));
    $newvisitor->appendChild($xmldoc->createElement('ucoi', htmlspecialchars($country->getCountryShort())));
    $newvisitor->appendChild($xmldoc->createElement('ucon', htmlspecialchars($country->getCountry())));
    $newvisitor->appendChild($xmldoc->createElement('rkey', htmlspecialchars($ref->getKeywords())));
    $newvisitor->appendChild($xmldoc->createElement('rdom', htmlspecialchars($ref->getDomain())));
    $newvisitor->appendChild($xmldoc->createElement('host', htmlspecialchars($_SERVER['HTTP_HOST']))); // the requested host
    $root->appendChild($newvisitor);

    $xmldoc->formatOutput = true;
    $xmldoc->save($tmp_filename, LIBXML_NOEMPTYTAG);
    
    //IP Cache schreiben:
    $user->writeToCache(__DIR__ . "/" . $fstat_cache_dir . "ip");
}

//Daten ausgewertet...
//Pfade notieren
$current_folder = __DIR__ . "/" . $fstat_data_dir . "paths/" . gmdate("Y", $user->time) . "/" . gmdate("m", $user->time);
if (
    ($dirhelper->checkExists($fstat_data_dir . "paths") == false) ||
    ($dirhelper->checkExists($fstat_data_dir . "paths/" . gmdate("Y", $user->time)) == false) ||
    ($dirhelper->checkExists($current_folder, true) == false)
){
    return 0;
}

if ($user->type == "Robot" or $user->type == "Validator") {
    $f_cont = @fopen($current_folder . "/bot_" . $user->escapedIP . "_" . gmdate("d_H", $user->time) . ".path", 'a');
} else {
    $f_cont = @fopen($current_folder . "/" . $user->escapedIP . "_" . gmdate("d_H", $user->time) . ".path", 'a');
}

if ($fstat_use_site_var) {
    $site_name = $fstat_default_site_name; //default

    $tmp_sitevar_array = explode(",", $fstat_site_variable);
    foreach ($tmp_sitevar_array as $tmp_sitevar) {
        $tmp_sitevar = trim($tmp_sitevar);
        if (isset($_GET[$tmp_sitevar])) {
            $site_name = htmlspecialchars($_GET[$tmp_sitevar]);
            $site_name = str_replace("|", "&#124;", $site_name); //to prevent corruption of table ;)
            break; //use first one from config
        }
    }
    fputs($f_cont, time() . "|" . basename($_SERVER['SCRIPT_FILENAME']) . "|" . $site_name . "\n");
} else {
    fputs($f_cont, time() . "|" . basename($_SERVER['SCRIPT_FILENAME']) . "\n");
}
fclose($f_cont);
//Pfade notiert
?>