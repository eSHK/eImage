<?php
function encrypt_pass($password) {
	$key = '1c5344c9a85b00a7db02afcb88debc41';
	$result = md5(md5($password).$key);
	return $result;
}

echo "Copy the encrypted password to the config.php<br>";
echo "'password'=>'".encrypt_pass($_GET['pass'])."'";

?>