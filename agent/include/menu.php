<?php
/**
 * 菜单
 *
 * @运维舫提供技术支持 授权请购买shopnc授权
 * @license    http://www.shopnc.club
 * @link       唯一论坛：www.shopnc.club
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
$_menu = array (
    'child' => array (
        array(
            'name' => '代理商',
            'menu_code' => '',
            'child' => array(
                'agent' => '代理商管理',
                'level' => '等级管理',

            )
        ),
        array(
            'name' => '订单',
            'child' => array(
                'order' => '订单管理',
                'purchase_order' => '进货单管理',
            )
        ),
        array(
            'name' => '账户',
            'child' => array(
                'commission' => '佣金管理',
                'withdraw' => '提现管理',
                'jiesuan' => '结算管理',
            )
        )
    )
);

$_menu = [
    [
        'name' => '我的面板',
        'url' => urlAgent() . "/index.php?act=index&op=index&top_code=10100&menu_code=10101",
        'top_code' => '10100',
        'child' => [
            [
                'name' => '统计报表',
                'url' => urlAgent() . "/index.php?act=index&op=index&top_code=10100&menu_code=10101",
                'menu_code' => '10101',
            ],
        ]
    ],
    [
        'name' => '账户管理',
        'url' => urlAgent() . "/index.php?act=balance&op=balance&top_code=10200&menu_code=10201",
        'top_code' => '10200',
        'child' => [
            [
                'name' => '我的账户',
                'url' => urlAgent() . "/index.php?act=balance&op=balance&top_code=10200&menu_code=10201",
                'menu_code' => '10201',
            ],
            [
                'name' => '提现管理',
                'url' => urlAgent() . "/index.php?act=balance&op=index&top_code=10200&menu_code=10202",
                'menu_code' => '10202',
            ],
            [
                'name' => '账户管理',
                'url' => urlAgent() . "/index.php?act=balance&op=bankaccount&top_code=10200&menu_code=10203",
                'menu_code' => '10203',
            ],
        ]
    ],
    [
        'name' => '下级代理管理',
        'url' => urlAgent() . "/index.php?act=agent&op=team_manage&top_code=10300&menu_code=10301",
        'top_code' => '10300',
        'child' => [
            [
                'name' => '团队管理',
                'url' => urlAgent() . "/index.php?act=agent&op=team_manage&top_code=10300&menu_code=10301",
                'menu_code' => '10301',
            ],
            [
                'name' => '佣金管理',
                'url' => urlAgent() . "/index.php?act=agent&op=commission&top_code=10300&menu_code=10302",
                'menu_code' => '10302',
            ],
            [
                'name' => '订单管理',
                'url' => urlAgent() . "/index.php?act=agent&op=order&top_code=10300&menu_code=10303",
                'menu_code' => '10303',
            ],
        ]
    ],
    [
        'name' => '商品管理',
        'url' => urlAgent() . "/index.php?act=goods&op=index&top_code=10400&menu_code=10401",
        'top_code' => '10400',
        'child' => [
            [
                'name' => '商品列表',
                'url' => urlAgent() . "/index.php?act=goods&op=index&top_code=10400&menu_code=10401",
                'menu_code' => '10401',
            ],
            [
                'name' => '进货市场',
                'url' => urlAgent() . "/index.php?act=goods&op=market&top_code=10400&menu_code=10402",
                'menu_code' => '10402',
            ],
            [
                'name' => '进货单管理',
                'url' => urlAgent() . "/index.php?act=goods&op=purchase_order&top_code=10400&menu_code=10403",
                'menu_code' => '10403',
            ],
            [
                'name' => '统计',
                'url' => urlAgent() . "/index.php?act=goods&op=statistics&top_code=10400&menu_code=10404",
                'menu_code' => '10404',
            ],
        ]
    ],
];
