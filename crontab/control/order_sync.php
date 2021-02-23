<?php

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class order_syncControl extends BaseCronControl {
    
    public function indexOp() {
        
        $this->_push_order(100);
        $this->_pull_order(100);        
    }
    
    public function push_orderOp() {
              
        $size = empty($_SERVER['argv'][3]) ? 100 : $_SERVER['argv'][3];
        $this->_push_order($size);               
    }
    
    public function pull_orderOp(){
        
        $size = empty($_SERVER['argv'][3]) ? 100 : $_SERVER['argv'][3];
        $this->_pull_order($size);
    }
    
    private function _push_order($size) {
        $model = Model("order");
        
        $data = $model->getOrdersToSync($size);
        foreach ($data as $d) {
            //订单主表，订单明细, 订单支付信息
            $order_id=$d['order_id'];
            $order_info = $model->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
            //获取订单信息，发送到后端系统
            EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $order_info));
            sleep(1);
        }       
    }
    
    private function _pull_order($size) {

        $data = Model("order")->getOrderSyncList($size);
        foreach ($data as $d) {
            EventEmitter::dispatch("ishop", array('event' => 'pull_order', 'order_sn' => $d['order_sn'], 'order_id' => $d['order_id']));
            sleep(1);
        }
    }
    
}