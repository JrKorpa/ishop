<?php
/**
 * 款式模型
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class base_style_infoModel extends Model {

    public function __construct(){
        parent::__construct('base_style_info');
    }

    /**
     * 会员详细信息（查库）
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getStyleInfo($condition, $field = '*', $master = false) {
        return $this->table('base_style_info')->field($field)->where($condition)->master($master)->find();
    }
    /**
     * 会员列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getStyleList($condition = array(), $field = '*', $page = null, $order = 'member_id desc', $limit = '') {
       return $this->table('base_style_info')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

    /**
     * 款式数量
     * @param array $condition
     * @return int
     */
    public function getStyleCount($condition) {
        return $this->table('base_style_info')->where($condition)->count();
    }

    /**
     * 编辑会员
     * @param array $condition
     * @param array $data
     */
    public function editStyle($data,$condition) {
        return $this->table('base_style_info')->where($condition)->update($data);
    }

    public function getProductTypeName($id){
        $row = $this->table('app_product_type')->field("product_type_name")->where(array('product_type_id'=>$id))->find();
        return !empty($row['product_type_name'])?$row['product_type_name']:'';
    }
    public function getStyleTypeName($id){
        $row = $this->table('app_cat_type')->field("cat_type_name")->where(array('cat_type_id'=>$id))->find();
        return !empty($row['cat_type_name'])?$row['cat_type_name']:'';
    }

    public function  getStyleXilieList($condition = array(), $field = '*', $page = null, $order = 'id desc', $limit = '') {
        return $this->table('app_style_xilie')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }
}
