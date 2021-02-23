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
require_once(BASE_DATA_PATH.'/config/params.php');
/**
 * 安装判断
 */
if (!is_file(BASE_ROOT_PATH."/install/lock") && is_file(BASE_ROOT_PATH."/install/index.php")){
    if (ProjectName != 'shop'){
        @header("location: ../install/index.php");
    }else{
        @header("location: install/index.php");
    }
    exit;
}

/**
 * 初始化
 */

define('DS', DIRECTORY_SEPARATOR);
define('INTELLIGENT_SYS',true);
define('StartTime',microtime(true));
define('TIMESTAMP',time());
define('DIR_SHOP','shop');
define('DIR_MBMBER','member');
define('DIR_CMS','cms');
define('DIR_CIRCLE','circle');
define('DIR_MICROSHOP','microshop');
define('DIR_ADMIN','admin');
define('DIR_API','api');
define('DIR_MOBILE','mobile');
define('DIR_WAP','wap');
define('DIR_RESOURCE','data/resource');
define('DIR_UPLOAD','data/upload');

define('ATTACH_PATH','shop');
define('ATTACH_COMMON','shop/common');
define('ATTACH_AVATAR','shop/avatar');
define('ATTACH_EDITOR','shop/editor');
define('ATTACH_MEMBERTAG','shop/membertag');
define('ATTACH_STORE','shop/store');
define('ATTACH_GOODS','shop/store/goods');
define('ATTACH_STORE_DECORATION','shop/store/decoration');
define('ATTACH_LOGIN','shop/login');
define('ATTACH_ARTICLE','shop/article');
define('ATTACH_BRAND','shop/brand');
define('ATTACH_GOODS_CLASS','shop/goods_class');
define('ATTACH_ADV','shop/adv');
define('ATTACH_ACTIVITY','shop/activity');
define('ATTACH_WATERMARK','shop/watermark');
define('ATTACH_POINTPROD','shop/pointprod');
define('ATTACH_GROUPBUY','shop/groupbuy');
define('ATTACH_SLIDE','shop/store/slide');
define('ATTACH_VOUCHER','shop/voucher');
define('ATTACH_REDPACKET','shop/redpacket');
define('ATTACH_STORE_JOININ','shop/store_joinin');
define('ATTACH_REC_POSITION','shop/rec_position');
define('ATTACH_CONTRACTICON','shop/contracticon');
define('ATTACH_CONTRACTPAY','shop/contractpay');
define('ATTACH_WAYBILL','shop/waybill');
define('ATTACH_MOBILE','mobile');
define('ATTACH_CIRCLE','circle');
define('ATTACH_CMS','cms');
define('ATTACH_LIVE','live');
define('ATTACH_MALBUM','shop/member');
define('ATTACH_MICROSHOP','microshop');
define('ATTACH_DELIVERY','delivery');
define('ATTACH_CHAIN', 'chain');
define('ATTACH_ADMIN_AVATAR','admin/avatar');
define('ATTACH_AGENT_AVATAR','agent/avatar');
define('ATTACH_FLEA','flea');
define('TPL_SHOP_NAME','default');
define('TPL_CIRCLE_NAME', 'default');
define('TPL_MICROSHOP_NAME', 'default');
define('TPL_CMS_NAME', 'default');
define('TPL_ADMIN_NAME', 'default');
define('TPL_DELIVERY_NAME', 'default');
define('TPL_CHAIN_NAME', 'default');
define('TPL_MEMBER_NAME', 'default');
define('TPL_AGENT_NAME', 'default');
define('TPL_FLEA_NAME', 'default');
define('TPL_CRM_NAME', 'default');
define('ADMIN_MODULES_SYSTEM', 'modules/system');
define('ADMIN_MODULES_CRM', 'modules/crm');
define('ADMIN_MODULES_SHOP', 'modules/shop');
define('ADMIN_MODULES_CMS', 'modules/cms');
define('ADMIN_MODULES_CIECLE', 'modules/circle');
define('ADMIN_MODULES_MICEOSHOP', 'modules/microshop');
define('ADMIN_MODULES_MOBILE', 'modules/mobile');
define('ADMIN_MODULES_AGENT', 'modules/agent');
define('ADMIN_MODULES_FLEA', 'modules/flea');
/*
 * 商家入驻状态定义
 */
//新申请
define('STORE_JOIN_STATE_NEW', 10);
//完成付款
define('STORE_JOIN_STATE_PAY', 11);
//初审成功
define('STORE_JOIN_STATE_VERIFY_SUCCESS', 20);
//初审失败
define('STORE_JOIN_STATE_VERIFY_FAIL', 30);
//付款审核失败
define('STORE_JOIN_STATE_PAY_FAIL', 31);
//开店成功
define('STORE_JOIN_STATE_FINAL', 40);

//默认颜色规格id(前台显示图片的规格)
define('DEFAULT_SPEC_COLOR_ID', 1);


/**
 * 商品图片
 */
define('GOODS_IMAGES_WIDTH', '60,240,360,1280');
define('GOODS_IMAGES_HEIGHT', '60,240,360,12800');
define('GOODS_IMAGES_EXT', '_60,_240,_360,_1280');

/**
 * 审核状态
 */
//默认未审核
define('ORDER_AUDIT_STATE_DEFAULT', 0);
//审核通过
define('ORDER_AUDIT_STATE_PASS', 1);
//审核通过
define('ORDER_AUDIT_STATE_UN_PASS', 2);

/**
 *  订单状态
 */
//已取消
define('ORDER_STATE_CANCEL', 0);
/**
 * 待确认
 */
define('ORDER_STATE_TO_CONFIRM', 5);
/**
 * 待支付(付定金 或 付尾款)
 */
define('ORDER_STATE_NEW', 10);
/**
 * 待布产
 */
define('ORDER_STATE_TO_BC', 14);

/**
 * 生产中
 */
define('ORDER_STATE_MAKING', 15);
/**
 * 待签收
 */
define('ORDER_STATE_TO_SIGN', 16);
/**
 * 已支付，暂时没有使用
 */
define('ORDER_STATE_PAY', 20);
/**
 * 待发货
 */
define('ORDER_STATE_TOSEND', 25);
/** 
 * 已发货，暂时没有使用
 */
define('ORDER_STATE_SEND', 30);
/**
 * 已完成
 */
define('ORDER_STATE_SUCCESS', 40);

/**
 * 已退款退货
 */
define('ORDER_STATE_RETURN', 41);


/**
 * 订单付款：待付款
 */
define('ORDER_PAY_TODO', 1);

/**
 * 订单付款：部分付款
 */
define('ORDER_PAY_PART', 2);

/**
 * 订单付款: 已全款
 */
define('ORDER_PAY_FULL', 3);

/**
 * 订单退款: 未退款
 */
define('REFUND_STATW_NO', 0);

/**
 * 订单退款: 部分退款
 */
define('REFUND_STATW_BU', 1);

/**
 * 订单退款: 全部退款
 */
define('REFUND_STATW_ALL', 2);

/*
 * 批发销售单销售类型： 零售
 */
define('BILL_ITEM_TYPE_LS', 'LS');

/*
 * 批发销售单销售类型： 批发
 */
define('BILL_ITEM_TYPE_PF', 'PF');

/*
 * 批发销售单销售类型： 维修
 */
define('BILL_ITEM_TYPE_WX', 'WX');

/*
 * 批发销售单销售类型： 转仓
 */
define('BILL_ITEM_TYPE_ZC', 'ZC');

/*
 * 订单布产状态： 未操作
 */
define('ORDER_BC_NO_OPR', 1);
/*
 * 订单布产状态： 已布产
 */
define('ORDER_BC_ASSIGNED', 2);
/*
 * 订单布产状态： 生产中
 */
define('ORDER_BC_MAKING', 3);
/*
 * 订单布产状态： 已出厂
 */
define('ORDER_BC_OUTPUT', 4);
/*
 * 订单布产状态： 不需布产
 */
define('ORDER_BC_IGNORE', 5);

/**
 * 拉取后端订单频率
 */
define('PULL_BC_FREQ_IN_HOUR', 3);

//订单超过N小时未支付自动取消
define('ORDER_AUTO_CANCEL_TIME', 3);
//订单超过N天未收货自动收货
define('ORDER_AUTO_RECEIVE_DAY', 10*1000);

//预订尾款支付期限(小时)
define('BOOK_AUTO_END_TIME', 72*1000);

//门店支付订单支付提货期限(天)
define('CHAIN_ORDER_PAYPUT_DAY', 7*1000);
/**
 * 订单删除状态
 */
//默认未删除
define('ORDER_DEL_STATE_DEFAULT', 0);
//已删除
define('ORDER_DEL_STATE_DELETE', 1);
//彻底删除
define('ORDER_DEL_STATE_DROP', 2);

/**
 * 文章显示位置状态,1默认网站前台,2买家,3卖家,4全站
 * @var string
 */
define('ARTICLE_POSIT_SHOP', 1);
define('ARTICLE_POSIT_BUYER', 2);
define('ARTICLE_POSIT_SELLER', 3);
define('ARTICLE_POSIT_ALL', 4);

//兑换码过期后可退款时间，15天
define('CODE_INVALID_REFUND', 15);

/**
 * 初始化
 */
if (!@include(BASE_DATA_PATH.'/config/config.ini.php')) exit('config.ini.php isn\'t exists!');
if (file_exists(BASE_PATH.'/config/config.ini.php')){
	include(BASE_PATH.'/config/config.ini.php');
}
global $config;

//默认平台店铺id
define('DEFAULT_PLATFORM_STORE_ID', $config['default_store_id']);

define('URL_MODEL',$config['url_model']);
define('SUBDOMAIN_SUFFIX', $config['subdomain_suffix']);
define('BASE_SITE_URL', $config['base_site_url']);
define('SHOP_SITE_URL', $config['shop_site_url']);
define('CMS_SITE_URL', $config['cms_site_url']);
define('CMS_modules_URL', $config['cms_modules_url']);
define('CIRCLE_SITE_URL', $config['circle_site_url']);
define('CIRCLE_modules_URL', $config['circle_modules_url']);
define('MICROSHOP_SITE_URL', $config['microshop_site_url']);
define('MICROSHOP_modules_URL', $config['microshop_modules_url']);
define('ADMIN_SITE_URL', $config['admin_site_url']);
define('AGENT_SITE_URL', $config['agent_site_url']);//new
define('ADMIN_modules_URL', $config['admin_modules_url']);
define('MOBILE_SITE_URL', $config['mobile_site_url']);
define('MOBILE_modules_URL', $config['mobile_modules_url']);
define('WAP_SITE_URL', $config['wap_site_url']);
define('UPLOAD_SITE_URL',$config['upload_site_url']);
define('RESOURCE_SITE_URL',$config['resource_site_url']);
define('DELIVERY_SITE_URL',$config['delivery_site_url']);
define('LOGIN_SITE_URL',$config['member_site_url']);
define('RESOURCE_SITE_URL_HTTPS',$config['resource_site_url']);
define('CHAIN_SITE_URL', $config['chain_site_url']);
define('MEMBER_SITE_URL', $config['member_site_url']);
define('LOGIN_RESOURCE_SITE_URL',MEMBER_SITE_URL.'/resource');
define('UPLOAD_SITE_URL_HTTPS', $config['upload_site_url']);
define('CHAT_SITE_URL', $config['chat_site_url']);
define('NODE_SITE_URL', $config['node_site_url']);


define('CHARSET',$config['db'][1]['dbcharset']);
define('DBDRIVER',$config['dbdriver']);
define('SESSION_EXPIRE',$config['session_expire']);
define('LANG_TYPE',$config['lang_type']);
define('COOKIE_PRE',$config['cookie_pre']);

define('DBPRE',$config['tablepre']);
define('DBNAME',$config['db'][1]['dbname']);

define('KELA_SPECIAL_BUSINESS', true);//false关闭特殊业务

define('SSO', json_encode(array(
    'login'   => 'http://my.kela.cn/member/sso/login?client=ishop_seller',
    'logout'   => 'http://my.kela.cn/member/sso/logout',
    'checkout'  => 'http://my.kela.cn/member/sso/checkout?client=ishop_seller&__klc_001=',
    'freq_check_token_inm' => 1,
    'token_ck'   => 'ssotid',
)));

define('SSO_WAP', json_encode(array(
    'login'   => 'http://my.kela.cn/member/sso/login?client=ishop_wap',
    'logout'   => 'http://my.kela.cn/member/sso/logout',
    'checkout'  => 'http://my.kela.cn/member/sso/checkout?client=ishop_wap&__klc_001=',
    'freq_check_token_inm' => 1,
    'token_ck'   => 'wapsso'
)));

if (PHP_SAPI != 'cli') {
    $_GET['act'] = is_string($_GET['act']) ? strtolower($_GET['act']) : (is_string($_POST['act']) ? strtolower($_POST['act']) : null);
    $_GET['op'] = is_string($_GET['op']) ? strtolower($_GET['op']) : (is_string($_POST['op']) ? strtolower($_POST['op']) : null);
    
    if (empty($_GET['act'])){
        require_once(BASE_CORE_PATH.'/framework/core/route.php');
        new Route($config);
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

    //启用ZIP压缩
    if ($config['gzip'] == 1 && function_exists('ob_gzhandler') && $_GET['inajax'] != 1){
    	ob_start('ob_gzhandler');
    }else {
    	ob_start();
    }
}

require_once(BASE_CORE_PATH.'/framework/libraries/queue.php');
require_once(BASE_CORE_PATH.'/framework/function/core.php');
require_once(BASE_CORE_PATH.'/framework/core/base.php');

require_once(BASE_CORE_PATH.'/framework/function/goods.php');
require_once(BASE_DATA_PATH. '/config/repository.php');

if(function_exists('spl_autoload_register')) {
	spl_autoload_register(array('Base', 'autoload'));
} else {
	function __autoload($class) {
		return Base::autoload($class);
	}
}

if ($config['gearmand']) {
    include_once(BASE_CORE_PATH.'/framework/libraries/EventEmitter.php');
}
