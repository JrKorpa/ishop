<?php
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
/*扩展权限*/
$config['extend_limits'] = [
   'order' => [
       array('name'=>'查看全部订单','act'=>'limit_store_order_all'),
       array('name'=>'修改他人订单信息','act'=>'limit_change_order_all')
    ],
   'warehouse' => [
       array('name'=>'查看货品成本','act'=>'limit_show_goods_chengben'),
       array('name'=>'打印价格标签','act'=>'limit_print_goods_price'),
       array('name'=>'单据导出','act'=>'limit_export_bill'),
       array('name'=>'查看盘点商品','act'=>'limit_show_pandian_goods'),       
       array('name'=>'盘点审核','act'=>'limit_pandian_checked'),
    ],
    'consult' => [
        array('name'=>'审核退款退货','act'=>'limit_return_check'),
        array('name'=>'允许对他人订单申请退款退货','act'=>'limit_return_apply')
    ],
    /*
    'statistics' => [
        array('name'=>'库存分析','act'=>'statistics_stock'),
        array('name'=>'损益分析','act'=>'statistics_loss'),
    ]
    */
    
];
$config['voucher_super_users'] = ['郭茜', '陶卫威', '马飞', '李博', '邱珠玮', '毕娜娜', '叶云英','admin','段君','卢慧','杨俊'];
return $config;
