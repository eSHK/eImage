<?php
if(!ob_start("ob_gzhandler")) ob_start();
session_start();
date_default_timezone_set('Asia/Hong_Kong');

//Smarty
require './libs/Smarty.class.php';
$smarty = new Smarty;

//Lanuage Setting
$action = htmlentities($_GET['action']);
$lang = strtolower($_GET['lang']);
$_lang = (!$_COOKIE['lang'] || !in_array($_COOKIE['lang'], array('zh', 'en'))) ? 'zh' : $_COOKIE['lang'];
$urlparm = $action ? "?action=$action" : '';
if(isset($lang) && in_array($lang, array('zh', 'en'))) {
    setcookie('lang', $lang);
    header("Location: index.php$urlparm");
}
$userlang = $_lang == 'zh' ? '繁中' : 'Eng';
include './lang/'.$_lang.'.php';

/*
if($_SERVER["HTTP_CF_VISITOR"] != '{"scheme":"https"}') {
    Header("Location: https://{$_SERVER["HTTP_HOST"]}{$_SERVER["PHP_SELF"]}$urlparm");
}
*/
if($_SERVER["HTTP_CF_VISITOR"] != '{"scheme":"https"}') {
    $ssl = "https";
    $ssl_url = "https://{$_SERVER["HTTP_HOST"]}{$_SERVER["PHP_SELF"]}$urlparm";
} else {
    $ssl = "http";
    $ssl_url = "http://{$_SERVER["HTTP_HOST"]}{$_SERVER["PHP_SELF"]}$urlparm";
}

//System Setting
$site['title'] = 'eImage | Free Image Upload | Free Image Hosting | eService-HK';
$site['keywords'] = 'Upload, Free, Image, HK, Service, eService-HK, Hosting, eImage';
$site['description'] = 'eService-HK is a free image hosting provider in hongkong.';
$site['url'] = 'https://'.$_SERVER['HTTP_HOST'].'/';
$upload_dir = 'upload/';
$upload_dir_date = 'upload/'.date('Y').'/'.date('m').'/'.date('d').'/';
$upload_ip_dir = 'upload_ip/';
$upload_ip_dir_date = 'upload_ip/'.date('Y').'/'.date('m').'/'.date('d').'/';
$del_dir = 'upload';
$filelife_day = 0;
$maxuploadmb = 5;
$maxuploadsize = $maxuploadmb * 1024 * 1024;
$resize_width = 1920;
$resize_thumbsize = 360;
$upload_type = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
$upload_ext = array('.gif', '.GIF', '.jpg', '.JPG', '.jpeg', '.JPEG', '.png', '.PNG');
$sesspass = 'eImages' . date('dm');
$to = 'demo@demo.com';

//Setting the administor account of system
function encrypt_pass($password) {
    $key = '1c5344c9a85b00a7db02afcb88debc41';
    $result = md5(md5($password).$key);
    return $result;
}

$admin = array(
    'username'	=>	'demo',
    'password'	=>	'1c5344c9a85b00a7db02afcb88debc41'
);
?>
