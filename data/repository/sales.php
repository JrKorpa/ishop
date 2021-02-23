<?php

class sales extends base_repository implements isales {
    
    public function create_bcd($order_sn)
    {
        return $this->invoke('allow_buchan', ['order_sn' => $order_sn]);
    }
    
    public function get_cpdz_price($where){
        return $this->invoke('getCpdzPrice', $where);
    }
    /**
     * 赠品列表
     * @see isales::get_gift_list()
     */    
    public function get_gift_list($where){
        $where['status'] = 1;//status=1可用
        $where['sale_way'] = 2;//sale_way = 1线上  2线下
        //return $this->invoke('getGiftList', $where);
        $model = new style_apiModel();
        return $model->getGiftGoodsList($where);
    }
    /**
     * 订单生产日志
     * @see isales::get_Bclog_list()
     */ 
    public function get_Bclog_list($page_size, $page_index, $where=[])
    {
        $where['page'] = $page_index;
        $where['pageSize'] = $page_size;
        return $this->invoke('getBclog_by_zhmd', $where);
    }

    /**
     * 获取客户信息
     * @see isales::get_vip_list_by_mob()
     */ 
    public function get_vip_list_by_mob($mobile)
    {
        return $this->getpack($mobile);
    }

    /**
     * 获取客户信息
     * @see isales::get_vip_list_by_mob()
     */ 
    public function save_vip_info($data)
    {
        return $this->savepack($data);
    }
    
}