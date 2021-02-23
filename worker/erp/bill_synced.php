<?php 

/***
 * 成功同步到ishop，回调通知erp
 * @param unknown $msg
 * @return boolean
 */
function on_bill_synced($msg) {
    
    $bill_id = $msg['bill_id'];
    $bill_no = $msg['bill_no'];

    global $db_config;

    $erp_db = new MysqlDB($db_config['zhanting']);
    $erp_db->selectDB('warehouse_shipping');
   
    return $erp_db->upsert([ 'bill_id' => $bill_id, 'bill_no' => $bill_no, 'latest_push_time' =>  $msg['date'] ], 'bill_sync', ['bill_id', 'bill_no']);
}

?>
