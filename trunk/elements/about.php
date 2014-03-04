<?php
    //lastupdate of uasdata
    if (file_exists($fstat_cache_dir."cache.ini")) {
            $cacheIni = parse_ini_file($fstat_cache_dir."cache.ini");
            $uasdata_date = date($fstat_date_format, $cacheIni['lastupdate']);
    }else{
            $uasdata_date = "";
    }
?>
        <div class="border" style="text-align:left;">
            <div class="text">
                <h2><a href="http://code.google.com/p/fstat/">FStat</a> Version <?php echo $fstat_fstat_version; ?></h2>
                &copy; F-Soft by <a href="http://www.fabian-neffgen.de/">Fabian Neffgen</a><br>
                <br>
                <?php echo FLANG_H_USEDBY; ?> <a href="http://user-agent-string.info/">UAString Api</a> (<?php echo FLANG_LAST_UPDATE . " " . $uasdata_date; ?>),<br>
                <?php echo FLANG_H_IPBY; ?> <a href="http://db-ip.com/db/download/country">DB-IP.com</a><br>
                <?php echo FLANG_H_FLAGBY; ?> <a href="http://www.famfamfam.com/lab/icons/flags/">FamFamFam</a><br>
                <br>
                <?php echo FLANG_FEEDBACK; ?> <a href="mailto:fabian@fabian-neffgen.de?subject=FStat%20Version%20<?php echo rawurlencode($fstat_fstat_version); ?>">fabian@fabian-neffgen.de</a>
            </div>
        </div>