	<div class="footer">
		<a href="http://code.google.com/p/fstat/">FStat</a> Version <?php echo $fstat_fstat_version; ?> from F-Soft by <a href="http://fa-ne.tk/">Fabian Neffgen</a><br>
		<?php echo FLANG_H_USEDBY; ?> <a href="http://user-agent-string.info/">UAString Api</a><br>
		<?php 
		if (isset($startzeit)){
				$endzeit=explode(" ", microtime());
				$endzeit=$endzeit[0]+$endzeit[1];
				echo "(in ".round($endzeit - $startzeit,4)." Sekunden geladen.)";
		}?>
	</div>