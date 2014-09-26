    <div class="footer">
        <a href="https://github.com/HostedDinner/fstat">FStat</a> Version <?php echo $fstat_fstat_version; ?> from F-Soft by <a href="http://www.fabian-neffgen.de/">Fabian Neffgen</a><br>
        <?php echo FLANG_H_LICENSE; ?> <a href="https://www.gnu.org/licenses/gpl.html">GNU GPL v3</a><br>
        <?php echo FLANG_H_USEDBY; ?> <a href="http://user-agent-string.info/">UAString Api</a> <?php echo FLANG_H_DATA_FROM; ?> <a href="http://db-ip.com/db/download/country">DB-IP.com</a><br>
        <?php
        if (isset($speed)) {
            echo "(in " .$speed->stop() . " Sekunden geladen.)";
        }
        ?>
    </div>
