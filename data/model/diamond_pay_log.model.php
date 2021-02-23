<?php
/**
 *裸钻购买信息模型
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class diamond_pay_logModel extends Model {



    public function __construct() {
       parent::__construct('diamond_pay_log');
    }

  

  

    /**
     * 取得 单条信息
     * @param unknown $condition
     * @param string $field
     */
    public function getDiamondPayLog($condition = array(), $field = '*') {
       return $this->field($field)->where($condition)->find();
    }

    /**
     * 查询符合条件的信息
     * @param unknown $condition
     * @param string $field
     */
    public function getDiamondPayLogList($condition = array(), $field = '*') {
       return $this->field($field)->where($condition)->select();
    }

    /**
     * 将添加信息
     *
     * @param array $data   商品数据信息
     * @param string $save_type 保存类型，可选值 db,cookie
     * @param int $quantity 购物数量
     */
    public function addDiamondPayLog($data = array()) {
       return $this->insert($data);
    }

    /**
     * 更新
     *
     * @param   array   $param
     */
    public function editDiamondPayLog($data,$condition) {
        $result = $this->where($condition)->update($data);
        return $result;
    }

   
   

   /**
     * 删除
     *
     * @param string $type 存储类型 db,cookie
     * @param unknown_type $condition
     */
    public function delDiamondPayLog( $condition = array()) {
        $result =  $this->where($condition)->delete();
        return $result;
    }
}
