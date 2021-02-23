<?php

class purchase extends base_repository implements ipurchase  {
    
    /**
     * 查询起版信息详情，参数 addtime +》 起版号
     * @see idiamond::get_diamond_info()
     */
    public function get_qiban_info($qiban_sn, $where=[]){
        if(empty($qiban_sn)) return false;
        $where['addtime'] = $qiban_sn;
        return $this->invoke("getQibanInfo",$where);
    }

    /**
     * 回写起版 订单信息
     * @param order_sn 订单号
     * @param opt 录单人
     * @param customer 客户姓名
     * @param addtime 起版号
     * 1/验证起版号是否存在，并且是启用状态的
     * 2/是否被其他订单使用
     */
    public function set_qiban_info($where=[])
    {
        return $this->invoke("SetQiban",$where);
    }

    /**
     * 根据起版Id查询起版记录
     * @param qiban_sn 起版号
     */
    public function get_qiban_byaddtime($qiban_sn, $where=[])
    {
        $where['qb_id'] = $qiban_sn;
        return $this->invoke("GetQiBianGoodsByQBId",$where);
    }
}

