<?php
require '.config.php';

$id = htmlentities($_GET['id']);
if(isset($id) && is_numeric($id)) {
    switch($id) {
        case 1:
            $chart_url = '';
            break;
        case 2:
            $chart_url = '';
            break;
        case 3:
            $chart_url = '';
            break;
        case 4:
            $chart_url = '';
            break;
    }
    file_get_contents();
}

?>
