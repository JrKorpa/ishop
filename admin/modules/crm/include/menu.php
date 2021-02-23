<?php

/**
 * 菜单
 * 
 * @license    
 * @link 
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
$_menu['crm'] = array(
    'name' => 'CRM',
    'child' => array(
        array(
            'name' => $lang['nc_config'],
            'child' => array(
                'account' => $lang['nc_web_account_syn'],
                'sns_sharesetting' => $lang['nc_binding_manage'],
            ),
        ),
        array(
            'name' => $lang['nc_member'],
            'child' => array(
                'member' => $lang['nc_member_manage'],
                'member_exp' => '等级经验值',
                'points' => $lang['nc_member_pointsmanage'],
                'sns_malbum' => $lang['nc_member_album_manage'],
                'snstrace' => $lang['nc_snstrace'],
                'sns_member' => $lang['nc_member_tag'],
                'predeposit' => $lang['nc_member_predepositmanage'],
                'chat_log' => '聊天记录'
            )
        ),
        /*array(
            'name' => $lang['nc_merchant'],
            'child' => array(
                'merchant' => '商家列表',
            )
        ),*/
    )
);
