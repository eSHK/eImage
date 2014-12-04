<?
require './config.php';
ini_set('memory_limit', '256M');

if(!$_GET['img'] || @!getimagesize($_GET['img'])) {
	//echo $langpark['open_error'];
	//header('HTTP/1.0 404 Not Found');
	header('Location: error/404.html');
} else {
	if($_GET['size'] && $_GET['size'] > $resize_width || $_POST['newsize'] > $resize_width) {
		echo $langpark['resize_large'] . '&nbsp;' . $resize_width . '&nbsp;' . $langpark['pixel'];
	} else {
		if(!$_GET['size']) {
			$thumbsize = $resize_thumbsize;
		} elseif($_GET['size'] && $_GET['size'] <= $resize_width) {
			$thumbsize = $_GET['size'];
		}

		if($_POST['newsize']) {
			$thumbsize = $_POST['newsize'];
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $_GET['img']);
		}

		$imgfile = $_GET['img'];
		list($width, $height) = getimagesize($imgfile);
		$imgratio = $width/$height;
	
		if($imgratio > 1) {
			$newheight = $thumbsize/$imgratio;
			$newwidth = $thumbsize;
		} else {
			$newheight = $thumbsize;
			$newwidth = $thumbsize*$imgratio;
		}

		$thumb = imagecreatetruecolor($newwidth, $newheight);
		$background_color = ImageColorAllocate($thumb, 255,255,255);
		imagecolortransparent($thumb, $background_color);
		$file_ext = strrchr($_GET['img'], '.');

		if($file_ext) {
			$file_ext = strtolower($file_ext);
			if($file_ext == '.gif') {
				$source = imagecreatefromgif($imgfile);
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				header('Content-type: image/gif');
				imagegif($thumb);
			} elseif($file_ext == '.jpg' || $file_ext == '.jpeg') {
				$source = imagecreatefromjpeg($imgfile);
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				header('Content-type: image/jpeg');
				imagejpeg($thumb, NULL, '100');
			} elseif($file_ext == '.png') {
				$source = imagecreatefrompng($imgfile);
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				header('Content-type: image/png');
				imagepng($thumb);
			}
		}
	}
}
?>