<?php 

/***
 * 成功同步单据到erp，回调通知ishop
 * @param unknown $msg
 * @return boolean
 */
function on_bill_synced($msg) {
    
    $bill_id = $msg['bill_id'];
    $bill_no = $msg['bill_no'];
      
    global $setting_config;
    $ishop_db = new MysqlDB($setting_config['ishop']);
    
    $resp = $ishop_db->upsert([ 'bill_id' => $bill_id, 'bill_no' => $bill_no, 'latest_push_time' =>  $msg['date'] ], 'erp_bill_sync', ['bill_id', 'bill_no']);
    $ishop_db->dispose();

    return $resp;
}

?>