<?php
require './config.php';

$id = htmlentities($_GET['id']);
if(isset($id) && is_numeric($id)) {
    switch($id) {
        case 1: // eth0 traffic
            $chart_url = '';
            break;
        case 2: // CPU usage
            $chart_url = '';
            break;
        case 3: // Load average
            $chart_url = '';
            break;
        case 4: // Memory usage
            $chart_url = '';
            break;
        case 5: // Disk IOs per device
            $chart_url = '';
            break;
    }
    header("Content-Type:image/png");
    $result = file_get_contents($chart_url);
	echo $result;
} else {
    header('Location: index.php');
}

?>
