<?php
//Require the Smarty Template System Function
require './config.php';

//Handle upload process
if($_SESSION['pass'] == $sesspass && $_SERVER['REQUEST_METHOD'] == 'POST' && in_array($_FILES['file']['type'], $upload_type) && in_array(strrchr($_FILES['file']['name'], '.'), $upload_ext) && $_FILES['file']['size'] < $maxuploadsize && getimagesize($_FILES['file']['tmp_name']) && empty($_COOKIE['dupload'])) {
    if($_FILES['file']['error'] > '0') {
        $error = 'Return Code: ' . $_FILES['file']['error'];
    } else {
        setcookie('dupload', 1);
        //Create the upload folder
        $oldumask = umask(0);
        @mkdir($upload_dir.'/'.date('Y'));
        @mkdir($upload_dir.'/'.date('Y').'/'.date('m'));
        @mkdir($upload_dir.'/'.date('Y').'/'.date('m').'/'.date('d'));
        @mkdir($upload_ip_dir.'/'.date('Y'));
        @mkdir($upload_ip_dir.'/'.date('Y').'/'.date('m'));
        @mkdir($upload_ip_dir.'/'.date('Y').'/'.date('m').'/'.date('d'));
        umask($oldumask);
        //Setting the filename
        $h = 8;
        $hm = $h*60;
        $ms = $hm*60;
        $upload_time = gmdate('His', time()+($ms));
        $file_name = md5($_FILES['file']['name']);
        $file_ext = strtolower(strrchr($_FILES['file']['name'], '.'));
        $upload_path = $upload_dir_date . $upload_time . '_' . $file_name . $file_ext;
        $upload_ip_path = $upload_ip_dir_date . $upload_time . '_' . $file_name . $file_ext;
        $upload_ip = $upload_ip_path . '_' . $_SERVER['REMOTE_ADDR'];
        $upload_img_size = getimagesize($_FILES['file']['tmp_name']);
        $uploadfile['name'] = $_FILES['file']['name']; //File name
        $uploadfile['type'] = $_FILES['file']['type']; //File type				
        //Check the file size
        if($_FILES['file']['size'] >= 1048576) {
            $uploadfile['size'] = sprintf("%.2f", $_FILES['file']['size']/1048576) . " MB";
        } elseif($_FILES['file']['size'] >= 1024) {
            $uploadfile['size'] = sprintf("%.1f", $_FILES['file']['size']/1024) . " KB";
        } else {
            $uploadfile['size'] = intval($_FILES['file']['size']) . " B";
        }
        $uploadfile['resolution'] = $upload_img_size[0] . '<span title="Width">(W)</span> x ' . $upload_img_size[1] . '<span title="Height">(H)</span>'; //File Resolution

        if($upload_img_size[0] > $resize_thumbsize) {
            $thumbsize = $resize_thumbsize;
        } else {
            $thumbsize = $upload_img_size[0];
        }
        $imgratio = $upload_img_size[0]/$upload_img_size[1];
        $showsize = ($imgratio > 1) ? $thumbsize : $thumbsize*$imgratio;
        $uploadfile['width'] = round($showsize) + 20;

        if(file_exists($upload_path)) {
            $error = $upload_path . '&nbsp;' . $langpark['file_exist'];
        } else {
            move_uploaded_file($_FILES['file']['tmp_name'], $upload_path);
            $ip_record = fopen($upload_ip, 'x');
            fwrite($ip_record, "{$_SERVER['REMOTE_ADDR']} - {$_SERVER['HTTP_USER_AGENT']}");
            fclose($ip_record);
            chmod($upload_path, 0644);
            chmod($upload_ip, 0644);
            $uploadfile['savepath'] = $upload_path;
        }

        if($upload_img_size[0] > $resize_thumbsize) {
            $imgshow = '<a href="' . $upload_path . '" target="_blank" title="' . $langpark['open_new_window'] . '"><img src="showimg.php?img=' . $upload_path . '" border="0" /></a>';
        } else {
            $imgshow = '<a href="' . $upload_path . '" target="_blank" title="' . $langpark['open_new_window'] . '"><img src="showimg.php?img=' . $upload_path . '&amp;size=' . $upload_img_size[0] . '" border="0" /></a>';
        }

        $code['html'] = '&lt;a href=&quot;' . $site['url'] . 'showimg.php?img=' . $upload_path . '&amp;size=' . $upload_img_size[0] . '&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;' . $site_url . 'showimg.php?img=' . $upload_path . '&amp;size=' . $upload_img_size[0] . '&quot; border=&quot;0&quot; alt=&quot;eImage&quot; /&gt;&lt;/a&gt;';
        $code['thumb'] = '[img]' . $site['url'] . 'showimg.php?img=' . $upload_path . '[/img]';
        $code['img'] = '[img]' . $site['url'] . $upload_path . '[/img]';
        $code['link'] = $site['url'] . $upload_path;
    }

} else {
    if($_SESSION['pass'] != $sesspass || $_COOKIE['dupload'] == 1) {
        header('Location: index.php');
    } elseif(!in_array($_FILES['file']['type'], $upload_type) || !in_array(strrchr($_FILES['file']['name'], '.'), $upload_ext)) {
        $error = $langpark['not_support_format'];
    } elseif(!getimagesize($_FILES['file']['tmp_name'])) {
        $error = $langpark['cannot_detect_size'];
    } elseif($_FILES['file']['size'] > $maxuploadsize) {
        $error = $langpark['file_size_large'];
    } elseif(empty($file['name']) || empty($file['type']) || empty($file['size']) || empty($file['resolution']) || empty($file['savepath'])){
        header('Location: index.php');
    }
}

$assign = array(
    //Variable
    'action'        =>  $action,
    'userlang'      =>  $userlang,
    'csspark'       =>  CSSFILE,
    'jspark'        =>  JSFILE,
    'langpark'      =>  $langpark,
    'site'          =>  $site,
    'file'          =>  $uploadfile,
    'code'          =>  $code,
    'imgshow'       =>  $imgshow,
    'error'         =>  $error,
    'ip'            =>  $_SERVER['REMOTE_ADDR'],
    //Template
    'header'        =>  'header.html',
    'leftside'      =>  'leftside.html',
    'content'       =>  'upload_process.html',
    'footer'        =>  'footer.html'
);

//Assign variables
$smarty->assign($assign);

$smarty->display('index.html');
?>
