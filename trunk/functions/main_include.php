<?php

function get_xml_backend($filename, $year = 0, $month = 0) {
    if (is_file($filename)) {

        if ($year != 0) {
            $fstat_backend_year = $year;
        }
        if ($month != 0) {
            $fstat_backend_month = $month;
        }

        $is_include = true;
        ob_start();
            include $filename;
            $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    } else {
        return false;
    }
}

?>