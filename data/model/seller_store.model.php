<?php
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class seller_storeModel extends Model{
    
    public function __construct(){
        parent::__construct('seller_store');
    }
    
    public function get_seller_stores($seller_id) {
        return $this->where(array('seller_id'=> $seller_id))->select();
    }
    
    public function get_store($seller_id, $store_id) {
        return $this->where(array('seller_id'=> $seller_id, 'store_id' => $store_id))->find();
    }

    /*
     * 增加
     * @param array $param
     * @return bool
     */
    public function addSellerStore($param){
        return $this->insert($param);
    }

    /*
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     */
    public function editSellerStore($update, $condition){
        return $this->where($condition)->update($update);
    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     */
    public function delSellerStore($condition){
        return $this->where($condition)->delete();
    }
}