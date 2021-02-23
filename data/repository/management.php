<?php

class management extends base_repository implements imanagement  {


    /**
    * 获取数据字典
    **/
    public function get_dictlist($condition)
    {
        $model_dict=Model("dict");
        $model_dict_item=Model("dict_item");
        $dict_info=$model_dict->getDictInfo($condition);
        $result = array();
        $dict_item_list = $model_dict_item->getDictItemList(array("dict_id"=>$dict_info["id"]));
        if(!empty($dict_item_list)){
          $result=array_column($dict_item_list,'label','name');
        }
       return $result;
    }
    
    /**
    * 根据渠道获取来源
    **/
    public function get_sources_list($ids)
    {
        $where = array('dept_ids'=>$ids);
        return $this->invoke("GetCustomerSourcesByIds", $where);
    }
    /**
     * 获取客户来源，支持 ids,dept_ids查询，兼容get_sources_list方法
     * @param unknown $where
     * @return mixed
     */
    public function get_customer_sources_list($where){
        return $this->invoke("GetCustomerSourcesByIds", $where);
    }
}

