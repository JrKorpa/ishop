<?php

interface iwarehouse {
    public function get_warehouse_list($page_size, $page_index, $where);
    public function get_warehousegoods_info($where=[]);
    public function get_warehousegoods_list($where,$page,$page_size);
    public function update_warehousegoods_info($data);
    public function createBillInfoS($data);
    public function createBillInfoD($data);
}

