<?php

function on_member_upsert($msg) {
    
    global $setting_config;
    
    $member_name = isset($msg['member_name']) ? $msg['member_name'] : '';
    $member_passwd = $msg['member_passwd'];

    if (empty($member_name) && empty($msg['member_id'])) {
        echo 'skip since member name and id both are empty.'.PHP_EOL;
        return true;
    }
    
    if (empty($member_name)) {
        $member_model = Model('member');  
        
        $member = $member_model->getMemberInfo(['member_id' => $msg['member_id'] ], "member_name");
        if (empty($member)) {
            echo 'skip since member can not be found in ishop.'.PHP_EOL;
            return true;
        }
        
        $member_name = $member['member_name'];
    }
    
    echo 'start to handle member_upsert for '.$member_name.PHP_EOL;
    
    //1. 查看sso是否有当前用户, 如果有，则更新密码；如果没有，则新增；
    //2. 查看当前用户是否有ishop的权限，如果没有，则新增进去；
    $sso_db = new MysqlDB($setting_config['sso']);
    $user = $sso_db->getRow("select * from oauth2_user where account='".$member_name."'");
    if (empty($user)) {
        $resp = $sso_db->insert('oauth2_user', ['account' => $member_name, 'password' => $member_passwd, 'enabled' => 1]);   
        if ($resp === false) {
            return false;   
        }
        
        $uuid = $sso_db->insertId();
    } else {
        $resp = $sso_db->update('oauth2_user', ['password' => $member_passwd, 'enabled' => 1], ['uuid' => $user['uuid']]);
        if ($resp === false) {
            return false;
        }
   
        $uuid = $user['uuid'];
    }

    $client_id_wap ='ishop_wap';
    $client_id_seller = 'ishop_seller';
    if(!empty(C('sys_scope')) && C('sys_scope')=='tsyd'){
         $client_id_wap ='tsyd_wap';
         $client_id_seller = 'tsyd_seller';
    }
    
    $resp = $sso_db->upsert(['client_id' => $client_id_wap, 'user_id' => $uuid, 'access_token_exp' => 86400], 'oauth2_user_client', ['client_id', 'user_id']);
    if ($resp === false) {
        return false;
    }
    
    $resp = $sso_db->upsert(['client_id' => $client_id_seller, 'user_id' => $uuid, 'access_token_exp' => 86400], 'oauth2_user_client', ['client_id', 'user_id']);
    if ($resp === false) {
        return false;
    }
    
    echo '----finish handle member_upsert for '.$member_name.PHP_EOL;
    return true;
}