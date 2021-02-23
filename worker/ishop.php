<?php

if (PHP_SAPI != 'cli') {
    exit('please run as cli.');
}

require __DIR__ . '/../base.php';

global $setting_config;
Base::parse_conf($setting_config);

ini_set('date.timezone','Asia/Shanghai');

$job_server_list = C('gearmand');
if (empty($job_server_list)) exit('job server is empty.');

require __DIR__.'/Worker.php';
require __DIR__.'/MysqlDB.class.php';

$setting_config['zhanting'] = [
    'dsn'=>"mysql:host=192.168.1.132;port=3306;dbname=app_order",
    'user'=>"cuteman",
    'password'=>"QW@W#RSS33#E#",
    'charset' => 'utf8'
];

$setting_config['sso'] = [
    'dsn'=>"mysql:host=192.168.1.59;port=3306;dbname=kelapi",
    'user'=>"cuteman",
    'password'=>"QW@W#RSS33#E#",
    'charset' => 'utf8'
];

$setting_config['ishop'] = [
    'dsn'=>"mysql:host=192.168.1.131;port=3306;dbname=ishop",
    'user'=>"ishop",
    'password'=>"zaq1!xsw2@",
    'charset' => 'utf8'
];

$worker = new Worker($job_server_list);
$worker->bind('ishop');
$worker->start();
?>