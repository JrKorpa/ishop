<?php

/**
 * 菜单
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
$_menu['system'] = array(
    'name' => '平台',
    'child' => array(
        array(
            'name' => $lang['nc_config'],
            'child' => array(
                'setting' => $lang['nc_web_set'],
                'upload' => $lang['nc_upload_set'],
                'message' => '邮件设置',
                'taobao_api' => '淘宝接口',
                'admin' => '权限设置',
                'admin_log' => $lang['nc_admin_log'],
                'area' => '地区设置',
                'cache' => $lang['nc_admin_clear_cache'],

            )
        ),
        array(
            'name' => $lang['nc_website'],
            'child' => array(
                'article_class' => $lang['nc_article_class'],
                'article' => $lang['nc_article_manage'],
                'document' => $lang['nc_document'],
                'navigation' => $lang['nc_navigation'],
                'adv' => $lang['nc_adv_manage'],
                'rec_position' => $lang['nc_admin_res_position'],
            )
        ),
        array(
            'name' => '应用',
            'child' => array(
                'link' => '友情连接',
                'hao' => '基本设置',
                'goods' => '商品管理',
                'db' => '数据库管理',
//								'store' => '店铺管理',
//								'member'=>'会员管理',
            )
        )
    )
);
