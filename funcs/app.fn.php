<?php

//登录成功后获取数据 
function get_content($url, $cookie=null, $setting=null) { 
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url); 
    if(isset($setting['CURLOPT_USERAGENT']) && $setting['CURLOPT_USERAGENT']) curl_setopt($curl, CURLOPT_USERAGENT, $setting['CURLOPT_USERAGENT']);

    $CURLOPT_HEADER = isset($setting['CURLOPT_HEADER']) ? $setting['CURLOPT_HEADER'] : 0;
    curl_setopt($curl, CURLOPT_HEADER, $CURLOPT_HEADER); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    if($cookie) curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie); //读取cookie 
    $rs = curl_exec($curl); //执行cURL抓取页面内容 
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl); 
    if($httpCode>=400) return $httpCode;

    if(isset($setting['charset']) && $setting['charset']) $rs = iconv($setting['charset'], 'UTF-8', $rs);
    return $rs; 
} 

//模拟登录 
function login_post($url, $cookie, $post, $setting=null) { 
    $curl = curl_init();//初始化curl模块 
    curl_setopt($curl, CURLOPT_URL, $url);//登录提交的地址 
    if(isset($setting['CURLOPT_USERAGENT']) && $setting['CURLOPT_USERAGENT']) curl_setopt($curl, CURLOPT_USERAGENT, $setting['CURLOPT_USERAGENT']);

    //伪造来源referer
    if(isset($setting['CURLOPT_REFERER']) && $setting['CURLOPT_REFERER']) curl_setopt ($curl,CURLOPT_REFERER, $setting['CURLOPT_REFERER']);
    

    curl_setopt($curl, CURLOPT_HEADER, 0);//是否显示头信息 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息 
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中 
    curl_setopt($curl, CURLOPT_POST, 1);//post方式提交 

    $post_str = http_build_query($post);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_str);//要提交的信息 

    $rs = curl_exec($curl);//执行cURL 
    if(isset($setting['charset']) && $setting['charset']){
        $rs = iconv($setting['charset'], 'UTF-8', $rs);
    }
    curl_close($curl);//关闭cURL资源，并且释放系统资源 
    return $rs ? $rs : null;
}

//模拟登录 
function post_content($url, $cookie, $post, $setting=array()) { 
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);

    if(isset($setting['CURLOPT_USERAGENT']) && $setting['CURLOPT_USERAGENT']) curl_setopt($curl, CURLOPT_USERAGENT, $setting['CURLOPT_USERAGENT']);
    

    //伪造来源referer
	if(isset($setting['CURLOPT_REFERER']) && $setting['CURLOPT_REFERER']) curl_setopt ($curl,CURLOPT_REFERER, $setting['CURLOPT_REFERER']);

    $CURLOPT_HEADER = isset($setting['CURLOPT_HEADER']) ? $setting['CURLOPT_HEADER'] : 0;
    curl_setopt($curl, CURLOPT_HEADER, $CURLOPT_HEADER);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie); //读取cookie
    // curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中 
    curl_setopt($curl, CURLOPT_POST, 1);//post方式提交 
    
    $post_str = http_build_query($post);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_str);//要提交的信息 

    $rs = curl_exec($curl);//执行cURL 
    if(isset($setting['charset']) && $setting['charset']){
        $rs = iconv($setting['charset'], 'UTF-8', $rs);
    }

    curl_close($curl);//关闭cURL资源，并且释放系统资源 
    return $rs ? $rs : null;
} 

/**
 * 递归转换数组编码
 *
 * @author chunze.huang
 * @date   2017-05-07
 * @param  [type]     $arr     [description]
 * @param  [type]     $charset [description]
 * @return [type]              [description]
 */
function iconv_arr($arr, $charset=null){
    if(isset($charset) && $charset){
        foreach($arr as $k=>$v){
            if(is_string($v)){
                $arr[$k] = iconv('UTF-8', $charset, $v);
            }elseif(is_array($v)){
                $arr[$k] = iconv_arr($v, $charset);
            }
        }
    }

    return $arr;
}


if(!function_exists('mkdirs')){
    function mkdirs($dir,$mode=0777) {
        if( ! is_dir( $dir ) )  {  
            if( ! mkdirs( dirname($dir) ) ) {  
                return false;  
            }  
            if( ! mkdir($dir,$mode) ) {  
                return false;  
            }  
        }  
        return true;
    }
}

function get_url_ext($url=null){
    $url = trim($url);
    $re = array();
    if($url){
        $a = parse_url($url);
        $b = $a['path'];
        $b = explode('.',$b);
        $re['ext'] = array_pop($b);
        $re['dir'] = substr( $a['path'], 0,  strrpos($a['path'],'/') );
        return $re;
        
    }
    
    return null;
}