<?php
/**
 * 批发客户管理
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class jxc_wholesaleModel extends Model {
    public function __construct(){
        parent::__construct('jxc_wholesale');
    }

    /**
     * 批发客户列表
     * @param array $condition
     * @param string $field
     * @param int $pagejxc_wholesale
     * @return array
     */
    public function getJxcWholesaleList($condition = array(), $field = '*', $page = 0) {
        return $this->table('jxc_wholesale')->field($field)->where($condition)->page($page)->select();
    }

    /**
     * 批发客户详细信息
     * @param array $condition
     * @return array
     */
    public function getJxcWholesaleInfo($condition, $field = '*') {
        return $this->table('jxc_wholesale')->field($field)->where($condition)->find();
    }

    //public function getJxcWholesaleInfoByID($house_id, $field = '*') {
    //    $info = $this->_rJxcWholesaleCache($house_id, $fields);
    //    if (empty($info)) {
    //        $info = $this->getJxcWholesaleInfo(array('house_id' => $house_id),$field);
    //        $this->_wJxcWholesaleCache($house_id, $info);
    //    }
    //    return $info;
    //}
//
    ///**
    // * 添加仓库
    // * @param unknown $insert
    // * @return boolean
    // */
    //public function addJxcWholesale($insert) {
    //    return $this->table('jxc_wholesale')->insert($insert);
    //}
//
    ///**
    // * 更新仓库
    // * @param array $update
    // * @param array $condition
    // * @return boolean
    // */
    //public function editJxcWholesale($update, $condition) {
    //    $list = $this->getJxcWholesaleList($condition, 'house_id');
    //    if (empty($list)) {
    //        return true;
    //    }
    //    $result = $this->table('jxc_wholesale')->where($condition)->update($update);
    //    if ($result) {
    //        foreach ($list as $val) {
    //            $this->_dJxcWholesaleCache($val['house_id']);
    //        }
    //    }
    //    return $result;
    //}
//
    ///**
    // * 删除仓库
    // * @param array $condition
    // * @return boolean
    // */
    //public function delJxcWholesale($condition) {
    //    $list = $this->getJxcWholesaleList($condition, 'house_id');
    //    if (empty($list)) {
    //        return true;
    //    }
    //    
    //    $result = $this->table('jxc_wholesale')->where($condition)->delete();
    //    if ($result) {
    //        foreach ($list as $val) {
    //            $this->_dJxcWholesaleCache($val['house_id']);
    //        }
    //    }
    //    return $result;
    //}
//
    ///**
    // * 读取仓库缓存
    // * @param int $house_id
    // * @param string $fields
    // * @return array
    // */
    //private function _rJxcWholesaleCache($house_id, $fields) {
    //    return rcache($house_id, 'store_JxcWholesale', $fields);
    //}
//
    ///**
    // * 写入仓库缓存
    // * @param int $house_id
    // * @param array $info
    // * @return boolean
    // */
    //private function _wJxcWholesaleCache($house_id, $info) {
    //    return wcache($house_id, $info, 'store_JxcWholesale');
    //}
//
    ///**
    // * 删除仓库缓存
    // * @param int $house_id
    // * @return boolean
    // */
    //private function _dJxcWholesaleCache($house_id) {
    //    return dcache($house_id, 'store_JxcWholesale');
    //}
//
    ///**
    // * 获取储位
    // * @param int $house_id
    // * @return boolean
    // */
    //public function getBoxList($house_id){
    //    $param  = array();
    //    $param['table'] = 'erp_box,jxc_wholesale';
    //    $param['join_type'] = 'left join';
    //    $param['field'] = 'erp_box.*';
    //    $param['join_on']   = array('jxc_wholesale.house_id=erp_box.house_id');
    //    $param['where'] =  "and jxc_wholesale.house_id='{$house_id}' and jxc_wholesale.store_id = ".$_SESSION['store_id'];
    //    return Db::select($param);
    //}
    ///**
    // * 删除仓库前查询是否有库存商品
    // * @param unknown $house_id_array
    // * @return boolean
    // */
    //public function preDropJxcWholesale($house_id_array){
    //    $num = $this->table('goods_items')->where(array('house_id'=>array('in',$house_id_array)))->count();
    //    if($num>0){
    //        return false;
    //    }
    //    return true;
    //}
    //
    ///**
    // * 柜位是否可以删除
    // * @param unknown $house_id_array
    // * @return boolean
    // */
    //public function preDropBox($box_id_array){
    //    $num = $this->table('goods_items')->where(array('box_id'=>array('in',$box_id_array)))->count();
    //    if($num>0){
    //        return false;
    //    }
    //    $num = $this->table('erp_bill')->where(array('to_box_id'=>array('in',$box_id_array)))->count();
    //    if($num>0){     
    //        return false;
    //    }
    //    return true;
    //}
}
