<?php
require './config.php';

$version = htmlentities($_GET['version']);
if(isset($version) && $version == '2') {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(in_array($_FILES['file']['type'], $upload_type) && in_array(strrchr($_FILES['file']['name'], '.'), $upload_ext) && $_FILES['file']['size'] < $maxuploadsize && getimagesize($_FILES['file']['tmp_name'])) {
            if($_FILES['file']['error'] > '0') {
                $error = array('status_code' => '400', 'status' => 'false', 'error' => $_FILES['file']['error']);
                echo json_encode($error);
            } else {
				$oldumask = umask(0);
				@mkdir($upload_dir.'/'.date('Y'));
				@mkdir($upload_dir.'/'.date('Y').'/'.date('m'));
				@mkdir($upload_dir.'/'.date('Y').'/'.date('m').'/'.date('d'));
				@mkdir($upload_ip_dir.'/'.date('Y'));
				@mkdir($upload_ip_dir.'/'.date('Y').'/'.date('m'));
				@mkdir($upload_ip_dir.'/'.date('Y').'/'.date('m').'/'.date('d'));
				umask($oldumask);
				$h = 8;
				$hm = $h*60;
				$ms = $hm*60;
				$upload_time = gmdate('His', time()+($ms));
				$file_name = md5($_FILES['file']['name']);
				$file_ext = strrchr($_FILES['file']['name'], '.');
				$upload_path = $upload_dir_date . $upload_time . '_' . $file_name . $file_ext;
				$upload_ip_path = $upload_ip_dir_date . $upload_time . '_' . $file_name . $file_ext;
				$upload_ip = $upload_ip_path . '_' . $_SERVER['REMOTE_ADDR'];
				$upload_img_size = getimagesize($_FILES['file']['tmp_name']);
				$uploadfile['name'] = $_FILES['file']['name'];
				$uploadfile['type'] = $_FILES['file']['type'];
				$uploadfile['size'] = $_FILES['file']['size'];
				$uploadfile['url'] = $site['url'] . $upload_path;
	
				if(file_exists($upload_path)) {
					$error = array('status_code' => '403', 'status' => 'false', 'error' => 'file already exist');
					echo json_encode($error);
				} else {
					move_uploaded_file($_FILES['file']['tmp_name'], $upload_path);
					$ip_record = fopen($upload_ip, 'x');
					fwrite($ip_record, "{$_SERVER["REMOTE_ADDR"]} - {$_SERVER["HTTP_USER_AGENT"]} @ API");
					fclose($ip_record);
					chmod($upload_path, 0644);
					chmod($upload_ip, 0644);
					$uploadfile['savepath'] = $upload_path;
				}
	
				$result = array(
					'status_code' => '200',
					'status' => 'ok',
					'name' => $uploadfile['name'],
					'type' => $uploadfile['type'],
					'size' => $uploadfile['size'],
					'url' => $uploadfile['url']
				);
				echo json_encode($result);
			}
		} else {
			$error = array('status_code' => '400', 'status' => 'false', 'error' => 'Image Only');
			echo json_encode($error);
		}
	} else {
			$error = array('status_code' => '405', 'status' => 'false', 'error' => 'The POST method is now required for all setters.');
			echo json_encode($error);
	}
} else {
	include './chrome/header.php';
	if(!strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
		echo 'Sorry! This extension requires Chrome 5.0 or above.';
		echo "<br />";
		echo 'Chrome Dev : http://www.google.com/chrome/eula.html?extra=devchannel';
		echo "<br />";
		echo 'eImage for Chrome : http://img.eservice-hk.net/chrome/eImage.crx';
	} else {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$method = htmlentities($_GET['method']);
			if(isset($method) && $method == 'upload') {
				$file_array = array();
				$file_num = count($_FILES['file']['name']);
				$max_file_uploads = 20;
	
				if($file_num > $max_file_uploads) {
					echo "Only {$max_file_uploads} files can be uploaded at once.";
				} else {
					if(is_array($_FILES['file']['name'])) {
						for($i = 0; $i < $file_num; ++$i) {
							$file_array[] = array(
								'name' => $_FILES['file']['name'][$i],
								'type' => $_FILES['file']['type'][$i],
								'size' => $_FILES['file']['size'][$i],
								'tmp_name' => $_FILES['file']['tmp_name'][$i],
								'error' => $_FILES['file']['error'][$i]
							);
						}
					} else {
						$file_array[] = $_FILES['file'];
					}
	
					foreach($file_array as $file) {
						if(!$file['error'] == UPLOAD_ERR_OK) {
							echo "Error Code: {$file['error']}\n";
						} elseif(!is_uploaded_file($file['tmp_name'])) {
							echo 'Error: Possible file upload attack!\n';
						} elseif(!getimagesize($file['tmp_name'])) {
							echo "Error: Failed to get the size of file {$file['name']}!\n";
						} elseif($file['size'] > $maxuploadsize) {
							echo "Error: {$file['name']} exceeded the max upload size ({$maxuploadmb}MB)!\n";
						} else {
							$oldumask = umask(0);
							@mkdir($upload_dir.'/'.date('Y'));
							@mkdir($upload_dir.'/'.date('Y').'/'.date('m'));
							@mkdir($upload_dir.'/'.date('Y').'/'.date('m').'/'.date('d'));
							@mkdir($upload_ip_dir.'/'.date('Y'));
							@mkdir($upload_ip_dir.'/'.date('Y').'/'.date('m'));
							@mkdir($upload_ip_dir.'/'.date('Y').'/'.date('m').'/'.date('d'));
							umask($oldumask);
							$h = 8;
							$hm = $h*60;
							$ms = $hm*60;
							$upload_time = gmdate('His', time()+($ms));
							$file_name = md5($file['name']);
							$file_ext = strrchr($file['name'], '.');
							$upload_path = $upload_dir_date . $upload_time . '_' . $file_name . $file_ext;
							$upload_ip_path = $upload_ip_dir_date . $upload_time . '_' . $file_name . $file_ext;
							$upload_ip = $upload_ip_path . '_' . $_SERVER['REMOTE_ADDR'];
							$upload_img_size = getimagesize($file['tmp_name']);
							$uploadfile['name'] = $file['name'];
							$uploadfile['type'] = $file['type'];
	
							if(file_exists($upload_path)) {
								echo $upload_path . '&nbsp;already exist';
							} else {
								move_uploaded_file($file['tmp_name'], $upload_path);
								$ip_record = fopen($upload_ip, 'x');
								fwrite($ip_record, "{$_SERVER["REMOTE_ADDR"]} - {$_SERVER["HTTP_USER_AGENT"]} @ Chrome Extensions");
								fclose($ip_record);
								chmod($upload_path, 0644);
								chmod($upload_ip, 0644);
								$uploadfile['savepath'] = $upload_path;
	
								$code['html'] = '&lt;a href=&quot;' . $site['url'] . 'showimg.php?img=' . $upload_path . '&amp;size=' . $upload_img_size[0] . '&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;' . $site_url . 'showimg.php?img=' . $upload_path . '&amp;size=' . $upload_img_size[0] . '&quot; border=&quot;0&quot; alt=&quot;eImage&quot; /&gt;&lt;/a&gt;';
								$code['thumb'] = '[img]' . $site['url'] . 'showimg.php?img=' . $upload_path . '[/img]';
								$code['img'] = '[img]' . $site['url'] . $upload_path . '[/img]';
								$code['link'] = $site['url'] . $upload_path;
	
								$img_url = $code['link'];
								$apikey = 'AIzaSyAAck_reviYMZJSz-H3TGCqvpKkDqS-hjw'; // Get API key from : https://code.google.com/apis/console/
								$json_postdata = json_encode(array('longUrl' => $img_url, 'key' => $apikey));
								$curl = curl_init();
								curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apikey);
								curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
								curl_setopt($curl, CURLOPT_HEADER, 0);
								curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
								curl_setopt($curl, CURLOPT_POST, 1);
								curl_setopt($curl, CURLOPT_POSTFIELDS, $json_postdata);
								
								$result = curl_exec($curl);
								curl_close($curl);
								
								if($result) {
									$json = json_decode($result);
									$googl_url = $json -> id;
									$googl_ext = "#{$file_ext}";
									$googl_url_img = '[img]' . $googl_url . $googl_ext . '[/img]';
								}
	
								if($file_num > 1) {
									$textarea = 'enable';
									$all_link .= "{$code['link']}\n";
									$all_img .= "{$code['img']}\n";
									$all_googl .= "{$googl_url}\n";
									$all_googl_img .= "{$googl_url_img}\n";
									echo "Name: {$file['name']}\n";
//									echo "<br />";
//									echo "<input type='text' value='{$code['link']}' size='34' onclick='this.select()' />\n";
//									echo "<div id='{$file['name']}' style='position:relative;float:right;' data-clipboard-text=\"{$code['link']}\">\n";
//									echo "<input type='button' value='Copy' />\n";
//									echo "</div>\n";
									echo "<br />";
								} else {
									echo "Name: {$file['name']}\n";
									echo "<input type='text' value='{$code['link']}' size='34' onclick='this.select()' />\n";
									echo "<div id='{$file['name']}' style='position:relative;float:right;' data-clipboard-text=\"{$code['link']}\">\n";
									echo "<input type='button' value='Copy' />\n";
									echo "</div>\n";
								}
							}
						}
					}
					if($textarea == 'enable') {
						echo "<br />";
						echo "Direct link\n";
						echo "<br />";
						echo "<textarea id='all_link' cols='27' onclick='this.select()'>{$all_link}</textarea>\n";
						echo "<div id='textarea_link' style='position:relative;float:right;' data-clipboard-text=\"{$all_link}\">\n";
						echo "<input type='button' value='Copy' />\n";
						echo "</div>\n";
						echo "<br />";
						echo "[img] link\n";
						echo "<br />";
						echo "<textarea id='all_img' cols='27' onclick='this.select()'>{$all_img}</textarea>\n";
						echo "<div id='textarea_img' style='position:relative;float:right;' data-clipboard-text=\"{$all_img}\">\n";
						echo "<input type='button' value='Copy' />\n";
						echo "</div>\n";
						echo "<br />";
						echo "goo.gl link\n";
						echo "<br />";
						echo "<textarea id='all_googl' cols='27' onclick='this.select()'>{$all_googl}</textarea>\n";
						echo "<div id='textarea_googl' style='position:relative;float:right;' data-clipboard-text=\"{$all_googl}\">\n";
						echo "<input type='button' value='Copy' />\n";
						echo "</div>\n";
						echo "<br />";
						echo "goo.gl [img] link\n";
						echo "<br />";
						echo "<textarea id='all_googl_img' cols='27' onclick='this.select()'>{$all_googl_img}</textarea>\n";
						echo "<div id='textarea_googl_img' style='position:relative;float:right;' data-clipboard-text=\"{$all_googl_img}\">\n";
						echo "<input type='button' value='Copy' />\n";
						echo "</div>\n";
					}
					echo "<script type=\"text/javascript\" src=\"./js/ZeroClipboard.js\"></script>";
					echo "<script type=\"text/javascript\">ZeroClipboard.config( { swfPath: \"./images/ZeroClipboard.swf\" } );var client = new ZeroClipboard( document.getElementById('textarea_link') );var client = new ZeroClipboard( document.getElementById('textarea_img') );var client = new ZeroClipboard( document.getElementById('textarea_googl') );var client = new ZeroClipboard( document.getElementById('textarea_googl_img') );var client = new ZeroClipboard( document.getElementById('{$file['name']}') );</script>";
				}
			} else {
				echo 'Error: Unknow method.';
			}
		} else {
			echo 'Error: The POST method is now required for all setters.';
		}
	}
	include './chrome/footer.php';
}
?>
