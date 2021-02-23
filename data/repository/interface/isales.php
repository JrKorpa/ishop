<?php

interface isales {
    public function create_bcd($order_sn);
    public function get_cpdz_price($where);
    public function get_gift_list($where);
    public function get_Bclog_list($page_size, $page_index, $where);
    public function get_vip_list_by_mob($mobile);
    public function save_vip_info($data);
}