<?php
/**
 * 珂兰技术 - kelan.club 运营版
 */
$config = array();
$config['base_site_url'] 		= 'http://ishop.kela.cn';
$config['agent_site_url']       = $config['base_site_url'] . '/agent';
$config['shop_site_url']        = $config['base_site_url'] . '/shop';
$config['cms_site_url']         = $config['base_site_url'] . '/cms';
$config['microshop_site_url']   = $config['base_site_url'] . '/microshop';
$config['circle_site_url']      = $config['base_site_url'] . '/circle';
$config['admin_site_url']       = $config['base_site_url'] . '/admin';
$config['mobile_site_url']      = $config['base_site_url'] . '/mobile';
$config['wap_site_url']         = $config['base_site_url'] . '/wap';
$config['chat_site_url']        = $config['base_site_url'] . '/chat';
$config['wechat_site_url']      = $config['base_site_url'] . '/wechat/ems/';
$config['node_site_url'] 		= $config['base_site_url']; //如果要启用IM，把 localhost/s7test 修改为您的服务器IP
$config['delivery_site_url']    = $config['base_site_url'] . '/delivery';
$config['chain_site_url']       = $config['base_site_url'] . '/chain';
$config['member_site_url']      = $config['base_site_url'] . '/member';
$config['upload_site_url']      = $config['base_site_url'] . '/data/upload';
$config['resource_site_url']    = $config['base_site_url'] . '/data/resource';
$config['cms_modules_url']      = $config['base_site_url'] . '/admin/modules/cms';
$config['microshop_modules_url']= $config['base_site_url'] . '/admin/modules/microshop';
$config['circle_modules_url']   = $config['base_site_url'] . '/admin/modules/circle';
$config['admin_modules_url']    = $config['base_site_url'] . '/admin/modules/shop';
$config['mobile_modules_url']   = $config['base_site_url'] . '/admin/modules/mobile';
$config['order_detail_def_page'] = $config['upload_site_url'] . '/shop/common/default_goods_image_240.gif';
$config['version']              = '20161215';
$config['setup_date']           = '2018-02-23 15:35:44';
$config['gip']                  = 0;
$config['dbdriver']             = 'mysql';
$config['tablepre']             = '';
$config['db']['1']['dbhost']       = 'localhost';
$config['db']['1']['dbport']       = '3306';
$config['db']['1']['dbuser']       = 'root';
$config['db']['1']['dbpwd']        = '123456';
$config['db']['1']['dbname']       = 'ishop';

//$config['db']['1']['dbhost']       = '192.168.0.134';
//$config['db']['1']['dbport']       = '3306';
//$config['db']['1']['dbuser']       = 'root';
//$config['db']['1']['dbpwd']        = 'zaq1!xsw2@';
//$config['db']['1']['dbname']       = 'ishop';

$config['db']['1']['dbcharset']    = 'UTF-8';
$config['db']['slave']             = $config['db']['master'];
$config['session_expire']   = 3600;
$config['lang_type']        = 'zh_cn';
$config['cookie_pre']       = '492D_';
$config['cache_open'] = false;
/*$config['redis']['prefix']        = 'kl_';
$config['redis']['master']['port']        = 6379;
$config['redis']['master']['host']        = '192.168.0.137';
$config['redis']['master']['pconnect']    = 0;
$config['redis']['slave']             = array();*/
$config['fullindexer']['open']      = false;
$config['fullindexer']['appname']   = 'goods';
$config['debug']            = false;
$config['url_model'] = false; //如果要启用伪静态，把false修改为true
$config['subdomain_suffix'] = '';//如果要启用店铺二级域名，请填写不带www的域名，比如kelan.club
/*$config['session_type'] = 'redis';
$config['session_save_path'] = 'tcp://192.168.0.137:6379';*/
$config['node_chat'] = false;//如果要启用IM，把false修改为true
//流量记录表数量，为1~10之间的数字，默认为3，数字设置完成后请不要轻易修改，否则可能造成流量统计功能数据错误
$config['flowstat_tablenum'] = 3;
$config['queue']['open'] = false;
$config['queue']['host'] = 'localhost';
$config['queue']['port'] = 6379;
//$config['oss']['open'] = false;
//$config['oss']['img_url'] = '';
//$config['oss']['api_url'] = '';
//$config['oss']['bucket'] = '';
//$config['oss']['access_id'] = '';
//$config['oss']['access_key'] = '';
$config['https'] = false;
$config['gearmand'] = array(
    ['host'=> 'localhost', 'port' => '4730'],
);
$config['crm_member_api'] = 'http://api.kela.cn/crm/member';
return $config;
