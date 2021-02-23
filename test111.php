<?php

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
define('APP_ID','shop');
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));

require __DIR__ . '/base.php';

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);//是否显示PHP错误信息，1显，0不显;
$api = data_gateway('idiamond');
$res = $api->get_diamond_list(40,10,array('act'=>'getall','StartDate'=>636689251439869028,'EndDate'=>636690116886151147));
var_dump($res['return_msg']['data']);die;
