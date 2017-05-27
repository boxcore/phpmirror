# phpMirror

phpMirror 主要给用户提供资源镜像下载，比如你想把资源文件 `http://xxx.com/adminfront/css/home.min.css`,只需要访问项目地址：`http://phpmirror.dev/xxx.com/adminfront/css/home.min.css`。


## Install 

1. 修改 conf/site_config.conf.example.php 中配置对应的站点内容，保存为conf/site_config.conf.php
2. 配置nginx 
```lua
location / {
	// code ...
	if (!-e $request_filename) {
	   rewrite  ^(.*)$  /index.php?s=/$1  last;
	   break;
	}
	// code ...
}
```

enjoy it!