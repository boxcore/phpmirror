<?php

if(!defined('DS'))   define('DS', DIRECTORY_SEPARATOR);       // 设定系统分割符号
if(!defined('ROOT')) define('ROOT', dirname(__FILE__).DS);    // 设定系统目录



require ROOT.'funcs/app.fn.php';
$mimes = include(ROOT.'conf/mimes.conf.php');
$conf = include(ROOT . 'conf/site_config.conf.php');
$offset = 7*60*60*24; // cache 7 day
// $offset = 10; // cache 30s
$allow_exts = array('ttf',
					'ttc',
					'otf',
					'eot',
					'woff',
					'woff2',
					'font.css',
					'css',
					'js',
				);

$setting = $str = $is_gen_new = null;
if(isset($_GET['s'])){
	if(preg_match('#^//(([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6})/#is', $_GET['s'],$match)){
		$domain = $match[1];

		if(isset($conf[$domain])){
			$conf_setting = $conf[$domain];
		}else{
			// 无自定义配置文件
			$conf_setting = $conf['default'];
			$conf_setting['site'] = $domain;
		}
	}

	if(!empty($conf_setting)){

		$save_path = 'mirrors'.DS.$conf_setting['site'].DS;

		$param = str_replace($conf_setting['site'].'/', '', $_GET['s']);
		$param = str_replace('//', '/', $param);

		$static_file = $save_path. $param;
		$static_info = get_url_ext($static_file);

		$remote_file = $conf_setting['scheme'].'://'.$conf_setting['site'] . $param;
		$url_info    = get_url_ext($remote_file);
		$remote_ext  = $url_info['ext'];


		
		if(in_array($remote_ext, $allow_exts)){
			header('Access-Control-Allow-Origin: *');
		}
		if(isset($mimes[$remote_ext])){
			if(is_array($mimes[$remote_ext])){
				header("Content-type: ".$mimes[$remote_ext][0]);
			}else{
				header("Content-type: ".$mimes[$remote_ext]);
			}
		}else{
			header("Content-type: application/octet-stream");
		}


		if( file_exists($static_file) ){
			$static_time = filemtime($static_file);
			$static_date = date('Y-m-d H:i:s',$static_time);

			$time = time();
			if($time - $static_time <= $offset){
				$str = file_get_contents($static_file);
				$is_gen_new = 1;
			}

		}

		if(!$str){
			$setting['CURLOPT_USERAGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36';
			$str = get_content($remote_file, null, $setting);
			if($str >= 400){
				unset($str);
				// echo "page error code: ".$str;exit;
			}else{

				mkdirs($static_info['dir']);

				if(!empty($conf_setting['preg'])){
					$str = preg_replace($conf_setting['preg']['patterns'], $conf_setting['preg']['replacements'],$str);
				}
				
				if($is_gen_new) file_put_contents($static_file,$str);
			}
			
		}

	}

	

}

if($str){
	// 一些缓存配置
	header("Cache-Control: public");
  	header("Pragma: cache");
  	
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s", time() + $offset)." GMT";
	header($ExpStr);

	$md5 = md5($str);

	/**
	 * 启用etag后如果要启用session要这么处理：
	 * session_cache_limiter('public');//设置session缓存
	 * session_start();//读取session
	 */
	$ETag=$md5;
	if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && ($_SERVER['HTTP_IF_NONE_MATCH']==$ETag) ){
		header('HTTP/1.1 304 Not Modified'); //返回304，告诉浏览器调用缓存
		exit();
	}else{
		header('Etag:'.$ETag);
		file_put_contents($static_file,$str);
	};


	// echo "/* test:".time()." & etag: {$ETag}  filemtime : {$static_date} */";
	echo $str;

}else{
	header("HTTP/1.1 404 Not Found");  
	header("Status: 404 Not Found");  
	exit;
}
