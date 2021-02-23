<?php

interface istyle {
    public function get_style_goods_index($keys);
    public function get_style_goods_list($where,$page,$page_size);
    public function build_style_goods_image_list($result);
    public function build_style_goods_price_list($result,$store_id);
    public function get_style_gallery($where);
    public function get_style_goods_attr($where);    
    public function get_style_goods_price($where);
    public function get_style_goods_info($where);
    public function get_style_goods_diy_index($keys,$style_sn);
    public function get_style_info($where);
    public function get_param_by_sn($where);
    public function get_couple_marry_goods_list($where,$page,$page_size);
    public function get_cpdz_price($where);
    public function get_cpdz_price_list($where);
    public function build_cpdz_price_list($cpdz_price_list,$type);
    public function get_style_goods_sn($where);
    public function update_style_info($data,$where);
    public function get_couple_info($where, $field);
    public function reason_couple_style_other($style_sn);
    public function reason_style_by_xiangkou($style_sn);
    public function get_stone_by_xiangkou($xiangkou);
    public function stone_scope_by_xiangkou($data);
    public function get_tuo_adaptive_by_dia($cart, $carat, $style_sn);
    public function build_tsyd_goods_list($result,$carat,$tsyd_carat);
    public function get_stone_common_by_xiangkou($xiangkou);
    public function get_style_sn_list($where);
}

