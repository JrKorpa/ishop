<?php

class warehouse extends base_repository implements iwarehouse{


    /**
    * 查询仓储列表
    * @page_size 每页显示条数
    * @page_index 第几页
    * @where array 检索条件
    **/
    public function get_warehouse_list($page_size, $page_index, $where=[])
    {
        $where['page'] = $page_index;
        $where['pageSize'] = $page_size;
        return $this->invoke("getwitwarehousegoodslist",$where);
    }
    /**
     * 查询仓储列表
     * @page_size 每页显示条数
     * @page_index 第几页
     * @where array 检索条件
     **/
    public function get_warehousegoods_list($where=[],$page, $page_size ){
        $where['page'] = $page;
        $where['pageSize'] = $page_size;
        $model = new warehouse_apiModel();
        //is_shop:1门店2总部
        if($where["is_shop"]==1){
            return $model->getWarehouseGoodsList($where);
        }else{ 
            $where["wholesale_company_id"] = $where["company_id"];
            $where["company_id"] = 58;
            $where["put_in_type"] = array(1,2,3,4);
            //$where['warehouse_id'] = array(705,706,708,1873);//705展厅9柜,706展厅10柜,708展厅边柜,1873浩鹏后库  2018-11-13补加
            $where['box_sn'] = array('9-A-3','9-A-4','9-A-5','9-B-1','9-B-2','9-C-1','9-C-2','9-C-3','9-C-4','9-D-Z','9-E-2','9-E-3','9-K-7','10-A-1','10-A-2','10-B-1','10-B-2','10-C-1','10-C-2','10-C-3','10-C-4','10-D-1','10-D-2','10-K-1','10-K-10','10-K-2','10-K-5','10-K-6','10-K-7','10-K-8','10-K-9','10-S-1','S-1','S-2','S-K-1','S-K-2','S-K-3','S-K-4','S-K-5','S-K-6');
            //$where["product_type"] = array('钻石','珍珠','翡翠','宝石','彩钻','K金','PT','银饰','素金类');
            //$where["pageSize"] = 48;
            
            if(!empty($where['extends']) && !in_array('pifajia',$where['extends'])){
                $where['extends'][] = 'pifajia';
            }else{
                $where['extends'] = array('pifajia');
            }            
            $result= $this->invoke("getwitwarehousegoodslist",$where);
            if(!empty($result['return_msg']['data'])){
                $goods_list = $result['return_msg']['data'];
                $model->fill_image_and_price($goods_list,$where,false);
                $result['return_msg']['data'] = $goods_list;
            }           
            return $result;
        }
    }
    /**
    * 查询货品详情
    * @goods_id 货号
    **/
    public function get_warehousegoods_info($where=[]){
        //is_shop:1门店2总部
        $model = new warehouse_apiModel();
        if($where["is_shop"]==2){
            $where["wholesale_company_id"] = $where["company_id"];
            $where["company_id"] = 58;
            $where["pageSize"] = 1;
            if(!empty($where['extends'])){
                $where['extends'] = array_merge($where['extends'],array('goods_price','pifajia'));
            }  else{
                $where['extends'] = array('goods_price','pifajia');
            }
            $result= $this->invoke("getwitwarehousegoodslist",$where);
            if(!empty($result['return_msg']['data'])){
                $goods_list = $result['return_msg']['data'];
                $model->fill_image_and_price($goods_list,$where,true);
                //print_r($goods_list);
                $result['return_msg'] = $goods_list[0];
            }            
            return $result;
        }else{            
            $result = $model->getWarehouseGoodsInfo($where);
            return $result;
        }
    }

    /**
     * 根据货号批量更新货品信息 
     * @data array('货号1'=>array(status'=>1),"货号2"=>array(status'=>1))
     **/
    public function update_warehousegoods_info($data){
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>'');
        if(empty($data)){
            $result['error'] = 1;
            $result['error_msg'] = "参数错误";
            return $result;
        }  
        $data1 = array();
        $data2 = array();
        foreach ($data as $goods_id=>$vo){
            if($vo['is_shop'] ==1){
                unset($vo['is_shop']);
                $data1[$goods_id] = $vo;
            }else if($vo['is_shop'] ==2){
                unset($vo['is_shop']);
                $data2[$goods_id] = $vo;
            }else{
                $result['error'] = 1;
                $result['error_msg'] = "参数错误:is_shop不能为空";
                return $result;
            }
        } 
        //门店现货修改     
        if(!empty($data1)){
            $goods_model = new goods_itemsModel();
            foreach ($data1 as $goods_id=>$update_data) {
                $res = $goods_model->editGoodsItems($update_data,array('goods_id'=>$goods_id));
                if($res === false){
                    $result['error'] = 1;
                    $result['error_msg'] = "{$goods_id}修改失败";
                    return $result;
                }
            }            
        }
        //总部现货修改
        if(!empty($data2)){
            $result = $this->invoke("updateWarehouseGoodsById", array('data'=>$data2));
        }
        return $result;
    } 

    /**
    * 生成销售单据
    * @where Data
    **/
    public function createBillInfoS($data)
    {
        $where = array();
        $where['data'] = $data;
        return $this->invoke("createBillInfoS",$where);
    }

    /**
    * 生成销售退货单据
    * @where Data
    **/
    public function createBillInfoD($where)
    {
        return $this->invoke("createBillInfoD",$where);
    }
}

