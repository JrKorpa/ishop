<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
function __autoload($class) {
    echo $class."-".date("Y-m-d H:i:s");
}
$A = new AA();
exit;
if($res['error']==0){
    echo '1111';exit;
}


define('APP_ID','shop');
define('BASE_PATH',str_replace('\\','/',dirname(__FILE__)));

require __DIR__ . '/base.php';

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);//是否显示PHP错误信息，1显，0不显;

$api = data_gateway('istyle');
//商品列表
$where = array('caizhi'=>'18K','yanse'=>'白','group_by'=>'style_sn','order_by'=>'goods_salenum asc');
$where = array('xilie'=>9,'caizhi'=>'PT950','yanse2'=>'白','carat_min'=>0.1,'carat_max'=>0.3,'group_by'=>'style_sn','order_by'=>'goods_salenum asc');
//$data = $api->get_style_goods_list($where,1, 10);
//商品图库列表
$where = array('style_sn'=>'KLPW000002');
//$data = $api->get_style_gallery($where);
//商品属性列表
$where = array('style_sn'=>'KLRW030549','xiangkou'=>0.5);
//$data = $api->get_style_goods_attr($where);
print_r($data);
//商品价格计算
$where = array('style_sn'=>'KLRW030549','xiangkou'=>0.5,'caizhi'=>1,'yanse'=>2,'shoucun'=>10,'channel_id'=>221);
//$data = $api->get_style_goods_price($where);
$keys= array('tuo_type',"xiangqian","zhushi_num","cert","color","clarity","fackwork");
$data = $api->get_style_goods_diy_index($keys,"KLRW030549");
print_r($data);
echo '1111111111111';
