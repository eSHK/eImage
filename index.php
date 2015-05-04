<?php
//Require the config file
require './config.php';

$_SESSION['pass'] = $sesspass;
setcookie('dupload', 0);

if(isset($action) && in_array($action, array('announcement', 'api', 'login', 'about', 'contact', 'faq', 'terms', 'notfound'))) {
    $action = strtolower($action);
    $content = "$action.html";
} else {
    $content = 'upload.html';
}

if($_GET['action'] == 'contact' && isset($_GET['send']) && !empty($_POST['name']) && !empty($_POST['email'])) {
    $type = $_POST['type'];
    switch($type) {
        case 1:
            $type = $langpark['type1'];
            break;
        case 2:
            $type = $langpark['type2'];
            break;
        case 3:
            $type = $langpark['type3'];
            break;
        case 4:
            $type = $langpark['type4'];
            break;
        default:
            $type = '其他';
            break;
    }
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $link = $_POST['link'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $ua = $_SERVER['HTTP_USER_AGENT'];

    $email_message = "{$langpark['type']}{$type}\n{$langpark['name']}{$name}\n{$langpark['email']}{$email}\n{$langpark['message']}{$message}\n{$langpark['link']}{$link}\n\n{$langpark['ip']}{$ip}\n{$langpark['ua']}{$ua}";
    $headers = "From: $email";
    if(mail($to, "[eImage] {$langpark['contact']}", $email_message, $headers)) {
        $result = 1;
    } else {
        $result = 0;
    }
}

if($_GET['action'] == 'login' && isset($_GET['send'])) {
    if($_POST['username'] == $admin['username'] && encrypt_pass($_POST['password']) == $admin['password']) {
        $content = 'chart.html';
    } else {
        $content = 'upload.html';
    }
}

$assign = array(
    //Variable
    'action'        =>  $action,
    'userlang'      =>  $userlang,
    'csspark'       =>  CSSFILE,
    'jspark'        =>  JSFILE,
    'langpark'      =>  $langpark,
    'ssl'           =>  $ssl,
    'ssl_url'       =>  $ssl_url,
    'site'          =>  $site,
    'maxuploadmb'   =>  $maxuploadmb,
    'maxuploadsize' =>  number_format($maxuploadsize),
    //Template
    'header'        =>  'header.html',
    'leftside'      =>  'leftside.html',
    'content'       =>  $content,
    'footer'        =>  'footer.html'
);

//Assign variables
$smarty->assign($assign);

//Display the template
$smarty->display('index.html');
?>
