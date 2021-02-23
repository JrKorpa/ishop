<?php
/**
 * 店铺仓库管理
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_warehouseModel extends Model {
    public function __construct(){
        parent::__construct('erp_warehouse');
    }

    /**
     * 仓库列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getWareHouseList($condition = array(), $field = '*', $page = 0) {
        return $this->table('erp_warehouse')->field($field)->where($condition)->page($page)->select();
    }

    /**
     * 仓库详细信息
     * @param array $condition
     * @return array
     */
    public function getWareHouseInfo($condition, $field = '*') {
        return $this->table('erp_warehouse')->field($field)->where($condition)->find();
    }


    /**
     * 查询仓库
     * @param array $condition
     * @return array
     */
    public function getWareHouseAll($condition, $field = '*',$limit=1000) {
        return $this->table('erp_warehouse')->field($field)->where($condition)->limit($limit)->select();
    }

    public function getWareHouseInfoByID($house_id, $field = '*') {
        $info = $this->_rWareHouseCache($house_id, $field);
        if (empty($info)) {
            $info = $this->getWareHouseInfo(array('house_id' => $house_id),$field);
            $this->_wWareHouseCache($house_id, $info);
        }
        return $info;
    }

    /**
     * 添加仓库
     * @param unknown $insert
     * @return boolean
     */
    public function addWareHouse($insert) {
        return $this->table('erp_warehouse')->insert($insert);
    }

    /**
     * 更新仓库
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editWareHouse($update, $condition) {
        $list = $this->getWareHouseList($condition, 'house_id');
        if (empty($list)) {
            return true;
        }
        $result = $this->table('erp_warehouse')->where($condition)->update($update);
        if ($result) {
            foreach ($list as $val) {
                $this->_dWareHouseCache($val['house_id']);
            }
        }
        return $result;
    }

    /**
     * 删除仓库
     * @param array $condition
     * @return boolean
     */
    public function delWareHouse($condition) {
        $list = $this->getWareHouseList($condition, 'house_id');
        if (empty($list)) {
            return true;
        }
        
        $result = $this->table('erp_warehouse')->where($condition)->delete();
        if ($result) {
            foreach ($list as $val) {
                $this->_dWareHouseCache($val['house_id']);
            }
        }
        return $result;
    }

    /**
     * 读取仓库缓存
     * @param int $house_id
     * @param string $fields
     * @return array
     */
    private function _rWareHouseCache($house_id, $fields) {
        return rcache($house_id, 'store_warehouse', $fields);
    }

    /**
     * 写入仓库缓存
     * @param int $house_id
     * @param array $info
     * @return boolean
     */
    private function _wWareHouseCache($house_id, $info) {
        return wcache($house_id, $info, 'store_warehouse');
    }

    /**
     * 删除仓库缓存
     * @param int $house_id
     * @return boolean
     */
    private function _dWareHouseCache($house_id) {
        return dcache($house_id, 'store_warehouse');
    }

    /**
     * 获取储位
     * @param int $house_id
     * @return boolean
     */
    public function getBoxList($house_id){
        $param  = array();
        $param['table'] = 'erp_box,erp_warehouse';
        $param['join_type'] = 'left join';
        $param['field'] = 'erp_box.*';
        $param['join_on']   = array('erp_warehouse.house_id=erp_box.house_id');
        $param['where'] =  "and erp_warehouse.house_id='{$house_id}' and erp_warehouse.store_id = ".$_SESSION['store_id'];
        
        return Db::select($param);
    }
    /**
     * 删除仓库前查询是否有库存商品
     * @param unknown $house_id_array
     * @return boolean
     */
    public function preDropWarehouse($house_id_array){
        $num = $this->table('goods_items')->where(array('house_id'=>array('in',$house_id_array)))->count();
        if($num>0){
            return false;
        }
        return true;
    }

   /**
     * 删除仓库前查询是否系统内置仓库
     * @param unknown $house_id_array
     * @return boolean
     */
    public function preDropWarehouseIssystem($house_id_array){
        $num = $this->table('erp_warehouse')->where(array('house_id'=>array('in',$house_id_array),'is_system'=>1 ))->count();
        if($num>0){
            return false;
        }
        return true;
    }

    
    /**
     * 柜位是否可以删除
     * @param unknown $house_id_array
     * @return boolean
     */
    public function preDropBox($box_id_array){
        $num = $this->table('goods_items')->where(array('box_id'=>array('in',$box_id_array)))->count();
        if($num>0){
            return false;
        }
        $num = $this->table('erp_bill')->where(array('to_box_id'=>array('in',$box_id_array)))->count();
        if($num>0){     
            return false;
        }
        return true;
    }
}
