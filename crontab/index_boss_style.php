<?php
/**
 * 队列
 *
 *
 * 计划任务触发 by 33h ao.com
 * 
 *  * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */

//if (empty($_SERVER['argv'][1])) exit('Access Invalid!!!');

define('APP_ID','crontab');
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
define('TRANS_MASTER',true);
define('INTELLIGENT_SYS', true);

require __DIR__ . '/../base.php';


$_GET['act'] = 'boss_style';
$_GET['op'] = 'web';


if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');
Base::run();

?>