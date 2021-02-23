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

$sql_tmpl = "mysqldump -u %s -h %s --password=%s --skip-add-locks --add-drop-table -B %s --tables %s > %s";
$sql_tar = "mysql -u %s -h %s --password=%s -D %s  < %s";

## cuteframe
$cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.59', 'QW@W#RSS33#E#', 'cuteframe', 'company dict dict_item customer_sources', $file);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - dump cf...'.PHP_EOL, FILE_APPEND);
exec($cmd);

file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - importing cf...'.PHP_EOL, FILE_APPEND);
$command = sprintf($sql_tar, 'ishop', '192.168.1.131', 'zaq1!xsw2@', 'ishop', $file);
exec($command);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - cf done!!!'.PHP_EOL, FILE_APPEND);

## front
$cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.132', 'QW@W#RSS33#E#', 'front',
'app_attribute app_attribute_value app_cat_type app_product_type app_style_baoxianfee app_style_gallery app_style_xilie base_style_info list_style_goods rel_cat_attribute rel_style_attribute rel_style_stone rel_style_lovers app_yikoujia_goods base_salepolicy_info app_salepolicy_channel',
$file
);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - dump front...'.PHP_EOL, FILE_APPEND);
exec($cmd);

file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - importing front...'.PHP_EOL, FILE_APPEND);
$command = sprintf($sql_tar, 'ishop', '192.168.1.131', 'zaq1!xsw2@', 'ishop', $file);
exec($command);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - front done!!!'.PHP_EOL, FILE_APPEND);

## app_order
$cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.132', 'QW@W#RSS33#E#', 'app_order', 'gift_goods', $file);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - dump app_order...'.PHP_EOL, FILE_APPEND);
exec($cmd);

file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - importing app_order...'.PHP_EOL, FILE_APPEND);
$command = sprintf($sql_tar, 'ishop', '192.168.1.131', 'zaq1!xsw2@', 'ishop', $file);
exec($command);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - app_order done!!!'.PHP_EOL, FILE_APPEND);

## warehouse_shipping
$cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.132', 'QW@W#RSS33#E#', 'warehouse_shipping', 'jxc_wholesale', $file);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - dump warehouse_shipping...'.PHP_EOL, FILE_APPEND);
exec($cmd);

file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - importing warehouse_shipping...'.PHP_EOL, FILE_APPEND);
$command = sprintf($sql_tar, 'ishop', '192.168.1.131', 'zaq1!xsw2@', 'ishop', $file);
exec($command);
file_put_contents(dirname($file).'/sync.log', date("Y-m-d H:i:s").' - warehouse_shipping done!!!'.PHP_EOL, FILE_APPEND);

?>
