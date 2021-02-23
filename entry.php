<?php
/**
 * 入口文件
 *
 * 统一入口，进行初始化信息
 *
 */

error_reporting(E_ALL & ~E_NOTICE);
define('BASE_ROOT_PATH',str_replace('\\','/',dirname(__FILE__)));
define('BASE_CORE_PATH',BASE_ROOT_PATH.'/core');
define('BASE_DATA_PATH',BASE_ROOT_PATH.'/data');
define("BASE_UPLOAD_PATH", BASE_ROOT_PATH . "/data/upload");
define("BASE_RESOURCE_PATH", BASE_ROOT_PATH . "/data/resource");
define('DS','/');
/**
 * 初始化
 */
if(!file_exists(BASE_DATA_PATH.'/config/config.ini.php')) {
    exit('config.ini.php isn\'t exists!');
}

$baseConfig = include(BASE_DATA_PATH.'/config/config.ini.php');
if (file_exists(BASE_DATA_PATH.'/config/config.local.ini.php')){
    $baseConfigLocal = include(BASE_DATA_PATH.'/config/config.local.ini.php');
    count($baseConfig) > 0 && is_array($baseConfigLocal) && array_walk($baseConfigLocal, function($val, $key) use(&$baseConfig) {
        $baseConfig[$key] = $val;
    });
}

if (file_exists(BASE_PATH.'/config/config.ini.php')){
	$subConfig = include(BASE_PATH.'/config/config.ini.php');
    count($baseConfig) > 0 && is_array($subConfig) && array_walk($subConfig, function($val, $key) use(&$baseConfig) {
        $baseConfig[$key] = $val;
    });
}

if (file_exists(BASE_PATH.'/config/config.local.ini.php')){
    $subConfigLocal = include(BASE_PATH.'/config/config.local.ini.php');
    count($baseConfig) > 0 && is_array($subConfigLocal) && array_walk($subConfigLocal, function($val, $key) use(&$baseConfig) {
        $baseConfig[$key] = $val;
    });
}

$config = $baseConfig;
global $config;
$_GET['act'] = is_string($_GET['act']) ? strtolower($_GET['act']) : (is_string($_POST['act']) ? strtolower($_POST['act']) : null);
$_GET['op'] = is_string($_GET['op']) ? strtolower($_GET['op']) : (is_string($_POST['op']) ? strtolower($_POST['op']) : null);

if (empty($_GET['act'])){
    require_once(BASE_CORE_PATH.'/framework/core/route.php');
    new Route($config);

    if ($_SERVER['REQUEST_URI'] == '/' || preg_match('#^/index#i', $_SERVER['REQUEST_URI'])) {
    	$_GET['act'] = 'show_store';
    	$_GET['store_id'] = preg_match('/^[\w]+$/i',$_GET['store_id']) ? $_GET['store_id'] : '1';
    }
}
//统一ACTION
$_GET['act'] = preg_match('/^[\w]+$/i',$_GET['act']) ? $_GET['act'] : 'index';
$_GET['op'] = preg_match('/^[\w]+$/i',$_GET['op']) ? $_GET['op'] : 'index';

//对GET POST接收内容进行过滤,$ignore内的下标不被过滤
$ignore = array('article_content','pgoods_body','doc_content','content','sn_content','g_body','store_description','p_content','groupbuy_intro','remind_content','note_content','adv_pic_url','adv_word_url','adv_slide_url','appcode','mail_content', 'message_content','member_gradedesc');
if (!class_exists('Security')) require(BASE_CORE_PATH.'/framework/libraries/security.php');
$_GET = !empty($_GET) ? Security::getAddslashesForInput($_GET,$ignore) : array();
$_POST = !empty($_POST) ? Security::getAddslashesForInput($_POST,$ignore) : array();
$_REQUEST = !empty($_REQUEST) ? Security::getAddslashesForInput($_REQUEST,$ignore) : array();
$_SERVER = !empty($_SERVER) ? Security::getAddSlashes($_SERVER) : array();
//启用ZIP压缩
if ($config['gzip'] == 1 && function_exists('ob_gzhandler') && $_GET['inajax'] != 1){
	ob_start('ob_gzhandler');
}else {
	ob_start();
}

require_once(BASE_CORE_PATH.'/framework/libraries/queue.php');
require_once(BASE_CORE_PATH.'/framework/function/core.php');
require_once(BASE_CORE_PATH.'/framework/core/base.php');
require_once(BASE_CORE_PATH.'/framework/function/goods.php');
if(function_exists('spl_autoload_register')) {
	spl_autoload_register(array('Base', 'autoload'));
} else {
	function __autoload($class) {
		return Base::autoload($class);
	}
}
