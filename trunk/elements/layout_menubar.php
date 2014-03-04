    <h1><?php echo $fstat_title; ?></h1>
    <div class="menubar">
        <div class="menuentry">
            <a href="#"><?php echo FLANG_MONTH; ?></a>
            <div class="submenu">
                <a href="./<?php echo $urlBuilder->build('overview', gmdate("Y"), gmdate("n")); ?>"><?php echo FLANG_TODAY; ?></a>
                <hr>
<?php
    $prev = $displayTime->getPreviousStartMonth();
    $next = $displayTime->getNextStartMonth();
?>
                <a href="./<?php echo $urlBuilder->build('overview', $prev['year'], $prev['month']); ?>"><?php echo FLANG_PREV_MONTH; ?></a>
                <a href="./<?php echo $urlBuilder->build('overview', $next['year'], $next['month']); ?>"><?php echo FLANG_NEXT_MONTH; ?></a>
                <hr>
<?php for($i = 0; $i < 12; $i++): ?>
<?php   if($i != ($displayTime->getStartMonth()-1)): ?>
                <a href="./<?php echo $urlBuilder->build('overview', $displayTime->getStartYear(), $i+1); ?>"><?php echo $lang->monthnames[$i]; ?></a>
<?php   else: ?>
                <span><?php echo $lang->monthnames[$displayTime->getStartMonth()-1]; ?></span>
<?php   endif; ?>
<?php endfor; ?>
            </div>
        </div>
        <div class="menuentry">
            <a href="#"><?php echo FLANG_YEAR; ?></a>
            <div class="submenu">
<?php for($i = -3; $i <= 3; $i++): ?>
<?php   if($i == 0): ?>
                <span><?php echo $displayTime->getStartYear(); ?></span>
<?php   else: ?>
                <a href="./<?php echo $urlBuilder->build('overview', $displayTime->getStartYear()+$i, $displayTime->getStartMonth()); ?>"><?php echo ($displayTime->getStartYear()+$i); ?></a>
<?php   endif;?>
<?php endfor; ?>
            </div>
        </div>
        <div class="menuentry">
            <a href="<?php echo "./".$urlBuilder->build('last'); ?>"><?php echo FLANG_LAST." ".$fstat_last_length." ".FLANG_VISITOR_S; ?></a>
        </div>
        <div class="menuentry">
            <a href="<?php echo "./".$urlBuilder->build('lastbots'); ?>"><?php echo FLANG_LAST." ".$fstat_last_length." ".FLANG_BOT_S; ?></a>
        </div>
        <div class="menuentry">
            <a href="#"><?php echo FLANG_LANG; ?></a>
            <div class="submenu">
<?php $langs = Language::getAllLanguages();?>
<?php foreach ($langs as $value) : ?>
<?php   if($urlBuilder->getPage() == 'overview'): ?>
                <a href="./<?php echo $urlBuilder->build(null, $displayTime->getStartYear(), $displayTime->getStartMonth(), $value['short']); ?>"><?php echo $value['long']; ?></a>
<?php   else: ?>
                <a href="./<?php echo $urlBuilder->build(null, null, null, $value['short']); ?>"><?php echo $value['long']; ?></a>
<?php   endif; ?>
<?php endforeach;?>
            </div>
        </div>
        <div class="menuentryR">
            <a href="<?php echo "./".$urlBuilder->build('about'); ?>"><?php echo "".FLANG_H_ABOUT; ?></a>
        </div>
        <div class="clear"></div>
    </div>
	
