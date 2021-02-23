<?php

if (PHP_SAPI != 'cli') {
    exit('please run as cli.');
}

ini_set('date.timezone','Asia/Shanghai');

$file = __DIR__ .'/sqls/'.date('Ymd_h').'.sql';
$data_path_dir = dirname($file);
if (!is_dir($data_path_dir)) {
	@mkdir($data_path_dir, 0777, true);
}

$db_config = [
    'zhanting' => [
        'dsn'=>"mysql:host=192.168.1.132;port=3306;dbname=front",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ],
    'ishop'    => [
        'dsn'=>"mysql:host=192.168.1.131;port=3306;dbname=ishop",
        'user'=>"ishop",
        'password'=>"zaq1!xsw2@",
        'charset' => 'utf8'
    ]
];

require_once __DIR__.'/MysqlDB.class.php';
$zt_db = new MysqlDB($db_config['zhanting']);
$shop_db = new MysqlDB($db_config['ishop']);

$param = isset($argv[1]) ? $argv[1]:null;
$param = trim($param,'');
if(empty($param)){
    $err =  '没有传入store_id';
    loginfo($err);
    exit ;
}
$param = explode(',',$param);

foreach ($param as $store_id){

    $sql = "SELECT
	s.id AS store_id,
	channel_name AS store_name,
	1 AS grade_id,
	1 as member_id,
  'admin' as member_name,
	'admin' as seller_name,
  1 as sc_id,
  c.company_name as store_company_name,
  0 as province_id,
  '' as area_info,
  ifnull(h.shop_address, c.address) as store_address,
  '' as store_zip,
  1 as store_state,
  0 as store_sort,
  0 as store_time,
  '' as store_keywords, 
  '' as store_description,
  0 as store_recommend,
  'default' as store_theme,
  0 as store_credit,
  0 as store_desccredit,
  0 as store_servicecredit,
  0 as store_deliverycredit,
  0 as store_collect,
  0 as store_sales,
  0 as store_free_price,
  0 as store_decoration_switch,
  0 as store_decoration_only,
  0 as store_decoration_image_count,
  1 as is_own_shop,
  0 as bind_all_gc,
  1 as left_bar_type,
  0 as is_person,
  c.id as store_company_id
from cuteframe.company c 
LEFT JOIN cuteframe.sales_channels s on c.id = s.company_id and s.channel_name not like '%已闭店%' and s.channel_class = 2 and s.channel_type != 1 and s.is_deleted = 0 
left join cuteframe.shop_cfg h on s.channel_type = 2 and h.id = s.channel_own_id and h.shop_type =2 and h.shop_status = 1 
where s.id = ".$store_id;

    $erp_store = $zt_db->getRow($sql);
    //如果展厅那边没有查到记录，说明这个store_id参数无效，跳过。
    if(empty($erp_store)){
        $err = "$store_id 在展厅没有查到记录";
        loginfo($err);
        continue;
    }
    //查询门店是否已经有这家店，如果存在，记录异常并跳过
    $res = $shop_db->getRow("select * from store where store_id=".$store_id);
    if(!empty($res)){
        $err = "$store_id 智慧门店已经存在此店";
        loginfo($err);
        continue;
    }

    // 开启事务处理
    try {
        $shop_db->beginTransaction();
        $shop_db->autoExec($erp_store,'store');//插入数据到门店表

        //给新店新建5个角色并授权与store_id=0一样的权限
        $seller_group_arr = $shop_db->getAll("select * from seller_group where store_id = 0");
        foreach ($seller_group_arr as $key=>$val){
            $val['store_id'] = $store_id;
            unset($val['group_id']);
            $seller_group_arr[$key] = $val;
        }
        $shop_db->autoExecALL($seller_group_arr,'seller_group');

        //将该渠道加入到admin、郭茜2064、闫修富2083、苏思南2188,谢佳漫2098，匡惠敏2384 管理渠道列表中
        $sql = "insert into seller_store(seller_id, seller_group_id, store_id, is_admin)
SELECT s.seller_id, g.group_id, t.store_id, 1 from store t 
inner join seller_group g on g.store_id = t.store_id and g.group_name = '老板' and t.store_id = {$store_id}
inner join seller s on s.seller_id in(1,2064,2083,2188,2098,2384)
where not EXISTS(select 1 from seller_store where seller_id = s.seller_id and seller_group_id = g.group_id);";
        $shop_db->query($sql);

        //给新增门店添加4个默认仓库：柜面、后库、待取、维修
        $warehouse_arr = array(
            'name'=>$erp_store['store_name'],
            'create_time'=>date('Y-m-d H:i:s',time()),
            'create_user'=>'system',
            'is_enabled'=>1,
            'type'=>1,
            'diamond_warehouse'=>0,
            'is_default'=>1,
            'company_id'=>$erp_store['store_company_id'],
            'company_name'=>$erp_store['store_company_name'],
            'store_id'=>$store_id,
            'store_name'=>$erp_store['store_name'],
            'is_system'=>1
        );
        $warehouse_arr_1 = $warehouse_arr_2 = $warehouse_arr_3 = $warehouse_arr_4 = $warehouse_arr;
        $warehouse_arr_1['name'] = $warehouse_arr['name']."柜面";
        $warehouse_arr_1['type'] = 1;
        $warehouse_arr_2['name'] = $warehouse_arr['name']."后库";
        $warehouse_arr_2['type'] = 2;
        $warehouse_arr_3['name'] = $warehouse_arr['name']."待取";
        $warehouse_arr_3['type'] = 3;
        $warehouse_arr_4['name'] = $warehouse_arr['name']."维修";
        $warehouse_arr_4['type'] = 13;

        $warehouse_insert_arr = array(
            $warehouse_arr_1,
            $warehouse_arr_2,
            $warehouse_arr_3,
            $warehouse_arr_4
        );
        $shop_db->autoExecALL($warehouse_insert_arr,'erp_warehouse');

        $shop_db->commit();
    } catch(Exception $e) {
        echo $e->getMessage();
        $shop_db->rollback();
        loginfo($e->getMessage());
        continue;
    }

}



function loginfo($err) {
    file_put_contents(__DIR__.'/'.date('Y-m-d').'_erp.import_store.err', $err.PHP_EOL,FILE_APPEND);
}











?>