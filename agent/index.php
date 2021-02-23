<?php
/**
 * 代理板块初始化文件
 */

define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));
if (!@include(dirname(dirname(__FILE__)).'/base.php')) exit('base.php isn\'t exists!');

define('APP_SITE_URL', AGENT_SITE_URL);
define('TPL_NAME',TPL_AGENT_NAME);
define('AGENT_TEMPLATES_URL',AGENT_SITE_URL.'/templates/'.TPL_NAME);
define('AGENT_RESOURCE_URL',AGENT_SITE_URL.'/resource');
define('SHOP_TEMPLATES_URL',SHOP_SITE_URL.'/templates/'.TPL_NAME);
define('BASE_TPL_PATH',BASE_PATH.'/templates/'.TPL_NAME);
if (!@include(BASE_PATH.'/control/control.php')) exit('control.php isn\'t exists!');
Base::run();
