<?php

if (PHP_SAPI != 'cli') {
    exit('please run as cli.');
}

require __DIR__ . '/../base.php';

global $setting_config;
Base::parse_conf($setting_config);

$job_server_list = C('gearmand');
if (empty($job_server_list)) exit('job server is empty.');

require __DIR__.'/Worker.php';
require __DIR__.'/MysqlDB.class.php';

$db_config = [
	'ishop' => [
		'dsn'=>"mysql:host=192.168.1.131;port=3306;dbname=ishop",
		'user'=>"ishop",
		'password'=>"zaq1!xsw2@",
		'charset' => 'utf8'
		],
	'zhanting' => [
		'dsn'=>"mysql:host=192.168.1.132;port=3306;dbname=app_order",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	],
    'zhanting_front' => [
        'dsn'=>"mysql:host=192.168.1.132;port=3306;dbname=front",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ]

];

ini_set('date.timezone','Asia/Shanghai');

$worker = new Worker($job_server_list);
$worker->bind('erp');
$worker->start();

?>
