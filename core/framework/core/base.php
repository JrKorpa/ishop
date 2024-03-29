<?php
/**
 * 核心文件
 *
 * 核心初始化类，不允许继承
 * 
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
final class Base{

	const CPURL = '';

	/**
	 * init
	 */
	public static function init() {
	    // config info
	    global $setting_config;
	    self::parse_conf($setting_config);
	    define('MD5_KEY',md5($setting_config['md5_key']));
         
	    if(function_exists('date_default_timezone_set')){
	        if (is_numeric($setting_config['time_zone'])){
	            @date_default_timezone_set('Asia/Shanghai');
	        }else{
	            @date_default_timezone_set($setting_config['time_zone']);
	        }
	    }

	    //session start
	    self::start_session();
	    
	    //output to the template
	    Tpl::output('setting_config',$setting_config);
	  
	    //read language
	    Language::read('core_lang_index');
	}

	/**
	 * run
	 */
	public static function run(){
	    self::cp();
	    self::init();
		self::control();
	}
	public static function runadmin($admin){
	    self::cp();
	    self::init();
		self::controladmin($admin);
	}
	/**
	 * get setting
	 */
	public static function parse_conf(&$setting_config){
		$nc_config = $GLOBALS['config'];
		if(is_array($nc_config['db']['slave']) && !empty($nc_config['db']['slave'])){
			$dbslave = $nc_config['db']['slave'];
			$sid     = array_rand($dbslave);
			$nc_config['db']['slave'] = $dbslave[$sid];
		}else{
			$nc_config['db']['slave'] = $nc_config['db'][1];
		}
		$nc_config['db']['master'] = $nc_config['db'][1];
		$setting_config = $nc_config;
		$setting = ($setting = rkcache('setting')) ? $setting : rkcache('setting',true);
		$setting['sysversion'] = '<span class="vol"><font class="b">33</font><font class="o">hao</font></span>';
		$setting_config = array_merge_recursive($setting,$nc_config);
	}

	/**
	 * 控制器调度
	 *
	 */
	private static function control(){
			
		//二级域名
		if ($GLOBALS['setting_config']['enabled_subdomain'] == '1' && $_GET['act'] == 'index' && $_GET['op'] == 'index'){
			$store_id = subdomain();
			if ($store_id > 0) $_GET['act'] = 'show_store';
		}
		$act_file = realpath(BASE_PATH.'/control/'.$_GET['act'].'.php');
		$class_name = $_GET['act'].'Control';
		if (!@include($act_file)){
		    if (C('debug')) {
		        throw_exception("Base Error: access file isn't exists!");
		    } else {
		        showMessage('抱歉！您访问的页面不存在','','html','error');
		    }
		}
		if (class_exists($class_name)){
			$main = new $class_name();
			$function = $_GET['op'].'Op';
			if (method_exists($main,$function)){
				$main->$function();
			}elseif (method_exists($main,'indexOp')){
				$main->indexOp();
			}else {
				$error = "Base Error: function $function not in $class_name!";
				throw_exception($error);
			}
		}else {
			$error = "Base Error: class $class_name isn't exists!";
			throw_exception($error);
		}
	}
private static function controladmin($admin){
		
		//二级域名
		if ($GLOBALS['setting_config']['enabled_subdomain'] == '1' && $_GET['act'] == 'index' && $_GET['op'] == 'index'){
			$store_id = subdomain();
			if ($store_id > 0) $_GET['act'] = 'show_store';
		}
		$act_file = realpath(BASE_PATH.'/modules/'.$admin.'/control/'.$_GET['act'].'.php');
		
		$class_name = $_GET['act'].'Control';
		if (!@include($act_file)){
		    if (C('debug')) {
		        throw_exception("Base Error: access file isn't exists!");
		    } else {
		        showMessage('抱歉！您访问的页面不存在','','html','error');
		    }
		}
		if (class_exists($class_name)){
			$main = new $class_name();
			$function = $_GET['op'].'Op';
			if (method_exists($main,$function)){
				$main->$function();
			}elseif (method_exists($main,'indexOp')){
				$main->indexOp();
			}else {
				$error = "Base Error: function $function not in $class_name!";
				throw_exception($error);
			}
		}else {
			$error = "Base Error: class $class_name isn't exists!";
			throw_exception($error);
		}
	}

	/**
	 * 开启session
	 *
	 */
	private static function start_session(){
		if (SUBDOMAIN_SUFFIX){
			$subdomain_suffix = SUBDOMAIN_SUFFIX;
		}else{
			if (preg_match("/^[0-9.]+$/",$_SERVER['HTTP_HOST'])){
				$subdomain_suffix = $_SERVER['HTTP_HOST'];
			}else{
				$split_url = explode('.',$_SERVER['HTTP_HOST']);
				if($split_url[2] != '') unset($split_url[0]);
				$subdomain_suffix = implode('.',$split_url);
			}
		}
		//session.name强制定制成PHPSESSID,不请允许更改
		@ini_set('session.name','PHPSESSID');
		$subdomain_suffix = str_replace('http://','',$subdomain_suffix);
		if ($subdomain_suffix !== 'localhost') {
		    @ini_set('session.cookie_domain', $subdomain_suffix);
		}

		$session_type = C('session_type');
		if ($session_type) {
		    ini_set("session.save_handler", $session_type);
		    ini_set("session.save_path", C('session_save_path'));
		} else {
		    //默认以文件形式存储session信息
			session_save_path(BASE_DATA_PATH.'/session');
		}
		
		session_start();
	}

	public static function autoload($class){
		$class = strtolower($class);
		if (ucwords(substr($class,-5)) == 'Class' ){
			if (!@include_once(BASE_PATH.'/framework/libraries/'.substr($class,0,-5).'.class.php')){
				exit("Class Error: {$class}.isn't exists!");
			}
		}elseif (ucwords(substr($class,0,5)) == 'Cache' && $class != 'cache'){
			if (!@include_once(BASE_CORE_PATH.'/framework/cache/'.substr($class,0,5).'.'.substr($class,5).'.php')){
				exit("Class Error: {$class}.isn't exists!");
			}
		} else if (ucwords(substr($class,-5)) == 'Model' ){
		    $path = strlen($class) > 5 ? BASE_DATA_PATH.'/model/'.substr($class,0,-5).'.model.php' : BASE_CORE_PATH.'/framework/libraries/model.php';
		    if (!@include_once($path)){
		        exit("Class Error: {$class}.isn't exists!");
		    }
		}elseif ($class == 'db'){
            if (!@include_once(BASE_CORE_PATH.'/framework/db/pdo.php')){
                exit("Class Error: {$class}.isn't exists!");
            }
			/*if (!@include_once(BASE_CORE_PATH.'/framework/db/'.strtolower(DBDRIVER).'.php')){
				exit("Class Error: {$class}.isn't exists!");
			}*/
		}else{
			if (!@include_once(BASE_CORE_PATH.'/framework/libraries/'.$class.'.php')){
				exit("Class Error: {$class}.isn't exists!");
			}
		}
	}

	/**
	 * 合法性验证
	 *
	 */
	private static function cp(){
		if (self::CPURL == '') return;
		if ($_SERVER['HTTP_HOST'] == 'localhost') return;
		if ($_SERVER['HTTP_HOST'] == '127.0.0.1') return;
		if (strpos(self::CPURL,'||') !== false){
			$a = explode('||',self::CPURL);
			foreach ($a as $v) {
				$d = strtolower(stristr($_SERVER['HTTP_HOST'],$v));
				if ($d == strtolower($v)){
					return;
				}else{
					continue;
				}
			}
			header('location: http://官网');exit();
		}else{
			$d = strtolower(stristr($_SERVER['HTTP_HOST'],self::CPURL));
			if ($d != strtolower(self::CPURL)){
				header('location: http://官网');exit();
			}
		}
	}
}