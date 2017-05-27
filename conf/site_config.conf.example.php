<?php

$site_conf = array(
	'apps.bdimg.com' => array(
		'site' => 'apps.bdimg.com',
		'scheme' => 'http',
		'preg' => array(
			'patterns' => array(
				'#(http|https)://apps.bdimg.com/#',
				'#(http|https)://www.baidu.com/#',
			),

			'replacements' => array(
				'/apps.bdimg.com/',
				'/apps.bdimg.com/',
			),
		),
		'type' => 0,
	),

);

$site_conf['default'] = $site_conf['apps.bdimg.com'];


return $site_conf;