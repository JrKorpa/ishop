<?php
/**
 * 从erp中获取订单生产进度，更新到ishop
 * @param unknown $msg
 * @return boolean
 */
function on_pull_order($msg) {
    
    global $setting_config;
    
    $order_sn = $msg['order_sn'];
    $order_id = $msg['order_id'];

    $order_model = Model('order');
    if (empty($order_id)) {
        $order = $order_model->getOrderInfo(['order_sn' => $order_sn],[], 'order_id');
        if (empty($order)) return true;

        $order_id = $order['order_id'];
    }
    
    $erpdb = new MysqlDB($setting_config['zhanting']);
    
    //从后端拉取当前订单的布产信息；
    $bc_list = $erpdb->getAll("SELECT i.order_sn, d.*, p.bc_sn from base_order_info i inner join app_order_details d on d.order_id =i.id
left join kela_supplier.product_info p on p.id = d.bc_id 
where i.order_sn ='{$order_sn}' and d.is_stock_goods = 0 and d.bc_id > 0;");
    
    $order_info = null;
    if (empty($bc_list)) {

        // 当前订单是期货单，状态生产中，但是没有布产信息；
        $order_sync = $order_model->getOrderSync($order_id);
        if (!empty($order_sync) 
            && $order_sync['order_state'] == ORDER_STATE_MAKING 
            && $order_sync['refund_state'] < 2 
            && $order_sync['pay_status'] > ORDER_PAY_TODO
            && $order_sync['is_xianhuo'] == 0 
            && $order_sync['add_time'] > 1514736000
            && empty($order_sync['bc_date'])) 
        {
            $order_info = $order_model->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
            if (!empty($order_info['extend_order_goods'])) {
                foreach($order_info['extend_order_goods'] as $detail) {
                    if ($detail['is_return'] == 0 && $detail['is_xianhuo'] == 0 && empty($detail['goods_itemid']) && empty($detail['bc_id'])) {
                        
                        echo 'dispatch sync_order since nothing buchan found in erp.'.PHP_EOL;

                        $order_model->editOrderSync(['order_id' => $order_id], ['latest_pull_order' => date('Y-m-d H:i:s')]);
                        $erpdb->dispose();

                        // 存在需要布产的商品
                        EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $order_info));
                        return true;
                    }
                }
            }
        } else if (empty($order_sync) || $order_sync['pay_status'] <= ORDER_PAY_TODO || $order_sync['add_time'] <= 1514736000) {
            return true;
        }
    }
    
    echo 'try to update order bc info'.PHP_EOL;
    
    // 当前订单有效的期货商品
    $order_goods = $order_model->getOrderGoodsBcStatus($order_id);
    $order_bc_status = 0;
    
    if (!empty($order_goods)) {
        $is_actived_tranx = false;
        try {
            $all_goods_is_bc = true; //是否都已布产
            foreach ($order_goods as $g) {
                $matched = false;
                foreach ($bc_list as $bc) {
                    if ($bc['ds_xiangci'] == $g['rec_id']) {
                        $matched = true;
                        if (empty($g['goods_itemid']) || $g['is_exchange'] != 1) {
                            if ($is_actived_tranx == false) {
                                $order_model->beginTransaction();
                                $is_actived_tranx = true;
                            }

                            $bc_status = $order_model->updateGoodsBCinfo($g['rec_id'], $bc); // 回写订单商品布产信息
                        } else {
                            // 说明goods_itemid已被覆盖(如，换货功能), 如果已换货，原布产单进度不再关注
                            $bc_status = 9; // 已出厂
                        }
                        if ($order_bc_status === 0 || $order_bc_status > $bc_status) $order_bc_status = $bc_status; // 取最小值
                    }
                }
                
                if (!$matched) $all_goods_is_bc = false;
            }
            
            if (!$all_goods_is_bc) $order_bc_status = 0; // 既然还有明细没有生成布产单，调整订单布产为最小值
            
            /*if ($order_bc_status < 9) {
                $order_model->editOrder(array('order_state' => ORDER_STATE_MAKING), array('order_id' => $order_id)); // 确保订单是生产中
            }*/
            
            if ($is_actived_tranx) $order_model->commit();
                
        } catch (Exception $ex) {
            if ($is_actived_tranx) $order_model->rollback();
            echo $ex->getMessage();
            return false;
        }           
    } else {
        echo 'skip since no order goods with is_xianhuo=0 and is_return=0'.PHP_EOL;
        //没有有效期货订单商品, 订单状态根据付款状态进行下一步调整
        $order_bc_status = 9; // 设置为已出厂
    }
    
    $order_model->editOrderSync(['order_id' => $order_id], ['latest_pull_order' => date('Y-m-d H:i:s')]);
    
    if ($order_bc_status >= 9) {
        
        echo 'try to auto order workflow'.PHP_EOL;
        $resp = Logic('order')->auto_order_wf($order_sn, $order_id);
        if ($resp !== true) {
            echo $resp->getMessage();
            return false;
        }
    }
    
    return true;
}

?>