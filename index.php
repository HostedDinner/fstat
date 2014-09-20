<?php
    error_reporting(0); //keine Fehler anzeigen
    //error_reporting(E_ALL ^ E_NOTICE); //alle Fehler ausser Notice anzeigen
    //error_reporting(E_ALL); // alle Fehler anzeigen
    
    session_start();
    
    //measures the loading speed
    require_once __DIR__ . "/classes/speedMeasure.php";
    $speed = new SpeedMeasure();
    $speed->start();
    
    //The language class is special, it includes the language defines at init time of the first var
    //therefor "new Language" must be called once!
    require_once "./classes/display/language.php";
    $lang = new Language(isset($_GET['lang']) ? $_GET['lang'] : null);

    //including necassary classes
    require_once "./classes/analyse/country.php";
    require_once "./classes/display/displayTime.php";
    require_once "./classes/display/urlbuilder.php";
    require_once "./classes/backend.php";

    //"Config"
    include_once "./config/settings.php";
    include_once "./config/information.php";


    //instances of the classes
    $displayTime = new DisplayTime();
    $displayTime->setUnsafeStartDate(isset($_GET['year']) ? $_GET['year'] : null, isset($_GET['month']) ? $_GET['month'] : null);
    $displayTime->setUnsafeEndDate(isset($_GET['year']) ? $_GET['year'] : null, isset($_GET['month']) ? $_GET['month'] : null);

    $urlBuilder = new URLBuilder(isset($_GET['show']) ? $_GET['show'] : null, $lang->getLanguage());

    $backend = new Backend();
    if(isset($_GET['length'])){
        $backend->setUnsafeListLength($_GET['length']);
    }


    //set Page title
    switch ($urlBuilder->getPage()) {
        case "about":
            $fstat_title = FLANG_H_ABOUT_FSTAT;
            break;
        case "last":
            $fstat_title = FLANG_H_LAST . " " . $backend->getListLength() . " " . FLANG_VISITOR_L;
            break;
        case "lastbots":
            $fstat_title = FLANG_H_LAST . " " . $backend->getListLength() . " " . FLANG_BOT_L;
            break;
        case "overview":
        default:
            $fstat_title = FLANG_H_STATFOR . " " . $lang->monthnames[$displayTime->getStartMonth() - 1] . " " . $displayTime->getStartYear();
            break;
    }
?>
<!DOCTYPE html>
<html>
<head>
<?php
    include("./elements/layout_header.php");
?>
</head>
<body>
<div id="container">
<?php
    //set Header
    include("./elements/layout_menubar.php");

    //set Page content
    switch ($urlBuilder->getPage()) {
        case "about":
            include("./elements/about.php");
            break;
        case "last":
        case "lastbots":
            //checked in last.php what schould be diplayed
            include("./elements/last.php");
            break;
        case "overview":
        default:
            include("./elements/overview.php");
            break;
    }
    
    //set Footer
    include("./elements/layout_footer.php");
?>
</div>
</body>
</html>