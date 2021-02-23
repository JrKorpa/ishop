<?php 

/***
 * 成功同步订单到erp，回调ishop后续订单状态
 * @param unknown $msg
 * @return boolean
 */
function on_order_synced($msg) {
    
    $order_sn = $msg['order_sn'];
    $order_id = $msg['order_id'];
    
    $sync_info = ['order_id' => $order_id, 'latest_sync_order' => $msg['date']];
    
    $order_model = Model('order');    
    $order_sync = $order_model->getOrderSync($order_id);

    echo '---start to proccess order synced for '.$order_id.PHP_EOL;
    
    try {
        $order_model->beginTransaction();
        if (empty($order_sync['sync_id'])) {
            $sync_info['init_sync_order'] = $sync_info['latest_sync_order'];
            $order_model->createOrderSync($sync_info);

            // 添加订单日志
            $order_model->addOrderLog(['order_id' => $order_id, 'log_msg' => '订单已推送到ERP系统', 'log_role' => 'system', 'log_user' => 'system', 'log_orderstate' => $msg['order_state'] ]);
            
        } else {
            $order_model->editOrderSync(['sync_id' => $order_sync['sync_id']], $sync_info);
        }

        // 期货已付款的有效订单，系统自动进行布产
        if ($order_sync['is_xianhuo'] == 0 
            && $order_sync['order_state'] > ORDER_STATE_TO_BC 
            && $order_sync['pay_status'] > ORDER_PAY_TODO 
            && $order_sync['refund_state'] < 2
            && $order_sync['add_time'] > 1514736000
            && empty($order_sync['bc_date'])) 
        {

            $to_bc_good = $order_model->getOrderGoodsInfo(['order_id' => $order_id, 'is_return' => 0, 'is_xianhuo' => 0],'rec_id');
            if (!empty($to_bc_good)) {
                    
                echo '---start to create bcd for '.$order_id.PHP_EOL;
                
                // 需要布产        
                $sales_api = data_gateway('isales');
                $resp = $sales_api->create_bcd($order_sn);
                
                if (isset($resp['error']) && $resp['error'] == 0) {
                    $order_model->editOrderSync(['order_id' => $order_id], ['bc_date' => date('Y-m-d H:i:s'), 'sync_stop' => 1]);
                    // 添加订单日志
                    $order_model->addOrderLog(['order_id' => $order_id, 'log_msg' => '生产系统已接单', 'log_role' => 'system', 'log_user' => 'system', 'log_orderstate' => $msg['order_state'] ]);
                    // 确保订单生产中
                    $order_model->editOrder(['order_state' => ORDER_STATE_MAKING], ['order_id' => $order_id]);

                    echo '---end to create bcd for '.$order_id.PHP_EOL;
                    $order_model->commit();
                    return true;
                } else {
                    file_put_contents(__DIR__ . '/'.date('Ymd').'.buchan.log',  json_encode(['order_sn' => $order_sn, 'resp' => $resp]).PHP_EOL, FILE_APPEND);
                    throw new Exception(isset($resp['error']) ? $resp['error'] : '调用布产失败');
                }
            }
        }
        
        if ($order_sync['order_state'] == $msg['order_state'] && ($order_sync['order_state'] >= ORDER_STATE_SEND || $order_sync['order_state'] == ORDER_STATE_CANCEL)) {
            // 订单已经完成发货 或者 已取消, 不再进行后端同步
            $order_model->editOrderSync(['sync_id' => $order_sync['sync_id']], ['sync_stop' => 1]);
        }
        
        $order_model->commit();
        echo '----finish process order_sync for order '.$order_id.PHP_EOL; 
    }  catch (Exception $ex) {
        $order_model->rollback();
        echo $ex->getMessage().PHP_EOL;
        return false;
    }  

    // 生产中的单子, 触发生产进度更新
    if ($order_sync['is_xianhuo'] == 0 && $order_sync['order_state'] == ORDER_STATE_MAKING && $order_sync['pay_status'] > ORDER_PAY_TODO) {
        EventEmitter::dispatch("ishop", array('event' => 'pull_order', 'order_id' => $order_id, 'order_sn' => $order_sn, 'source' => 'ishop.order_synced'));
    }
    
    return true;
}

?>