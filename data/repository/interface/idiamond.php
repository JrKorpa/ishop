<?php

interface idiamond {
    public function get_diamond_index($keys, $where);
    public function get_diamond_list($page_size, $page_index, $where);
    public function get_diamond_info($where);
    public function update_diamond_info($data);
    public function multiply_jiajialv(&$diamondinfo, $store_id, $company_id);
    public function GetDiamondByCert_id($cert_id, $where);
    public function get_diamond_by_kuan_sn($kuan_sn);
    public function get_tuijian_list($where);
    public function get_tsyddia_by_cert_id($cert_id, $store_id, $company_id);
}

