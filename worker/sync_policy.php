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

/* TODO： 
select update_time from base_salepolicy_info order by update_time desc limit 1；
用此语句从zhanting和ishop查出销售政策的变更时间，比较时间
*/
require_once __DIR__.'/MysqlDB.class.php';
$sql = "select max(time) as time from (
	select max(create_time) as time from app_salepolicy_channel
	union 
	select max(update_time) as time from base_salepolicy_info
	union
	select max(add_time) as time from app_yikoujia_goods
) h";

$zt_db = new MysqlDB($db_config['zhanting']);
$zt_policy_latest_update_time = $zt_db->getOne($sql);

$shop_db = new MysqlDB($db_config['ishop']);
$ishop_policy_latest_update_time = $shop_db->getOne($sql);

if ($zt_policy_latest_update_time == $ishop_policy_latest_update_time) {
    echo 'skip since no updates'.PHP_EOL;
    exit();
}

$sql_tmpl = "mysqldump -u %s -h %s --password=%s --skip-add-locks --add-drop-table -B %s --tables %s > %s";
$sql_tar = "mysql -u %s -h %s --password=%s -D %s  < %s";

## front
$cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.132', 'QW@W#RSS33#E#', 'front', 'app_yikoujia_goods base_salepolicy_info app_salepolicy_channel', $file);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - dump front...'.PHP_EOL, FILE_APPEND);
exec($cmd);

file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - importing front...'.PHP_EOL, FILE_APPEND);
$command = sprintf($sql_tar, 'ishop', '192.168.1.131', 'zaq1!xsw2@', 'ishop', $file);
exec($command);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - front done!!!'.PHP_EOL, FILE_APPEND);

?>
