<?php

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class bill_syncControl extends BaseCronControl {
    
    public function indexOp() {
        $this->_to_erp(100);
    }
    
    public function to_erpOp() {
        
        $size = empty($_SERVER['argv'][3]) ? 100 : $_SERVER['argv'][3];
        $this->_to_erp($size);
    }
    
    private function _to_erp($size) {
        $model = Model("erp_bill");
        
        $data = $model->getBillsToSync($size);
        foreach ($data as $d) {
            //获取单据信息，发送到后端系统
            EventEmitter::dispatch("erp", array('event' => 'sync_bill', 'bill_id' => $d['bill_id'], 'bill_no' => $d['bill_no']));
        }
    }  
   
    
}