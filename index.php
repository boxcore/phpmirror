<?php

if(!defined('DS'))   define('DS', DIRECTORY_SEPARATOR);       // 设定系统分割符号
if(!defined('ROOT')) define('ROOT', dirname(__FILE__).DS);    // 设定系统目录



require ROOT.'funcs/app.fn.php';
$mimes = include(ROOT.'conf/mimes.conf.php');
$conf = include(ROOT . 'conf/site_config.conf.php');



$setting = null;

if(isset($_GET['s'])){
	if(preg_match('#^//(([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6})/#is', $_GET['s'],$match)){
		$domain = $match[1];

		if(isset($conf[$domain])){
			$conf_setting = $conf[$domain];
		}else{
			echo 'not such conf for '.$domain;
			exit;
		}
	}

	if(!empty($conf_setting)){

		$save_path = 'mirrors'.DS.$conf_setting['site'].DS;

		$param = str_replace($conf_setting['site'], '', $_GET['s']);
		$param = str_replace('//', '/', $param);

		$static_file = $save_path. $param;
		$static_info = get_url_ext($static_file);

		$remote_file = $conf_setting['scheme'].'://'.$conf_setting['site'] . $param;
		$url_info    = get_url_ext($remote_file);
		$remote_ext  = $url_info['ext'];

		if(isset($mimes[$remote_ext])){
			if(is_array($mimes[$remote_ext])){
				header("Content-type: ".$mimes[$remote_ext][0]);
			}else{
				header("Content-type: ".$mimes[$remote_ext]);
			}
		}else{
			header("Content-type: text/plain");
		}


		if( file_exists($static_file) ){
			echo file_get_contents($static_file);
		}else{
			$str = get_content($remote_file, null, $setting);
			if($str >= 400){
				echo "page error code: ".$str;
			}else{

				
				mkdirs($static_info['dir']);

				if(!empty($conf_setting['preg'])){
					$str = preg_replace($conf_setting['preg']['patterns'], $conf_setting['preg']['replacements'],$str);
				}
				
				file_put_contents($static_file,$str);
				echo $str;
			}
			
		}

	}

	

}