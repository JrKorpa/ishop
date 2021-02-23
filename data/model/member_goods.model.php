<?php
/**
 * 用户商品模型
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class member_goodsModel extends Model {

    

    public function __construct() {
       parent::__construct('member_goods');
    }

    /**
     * 取属性值魔术方法
     *
     * @param string $name
     */
    public function __get($name) {
        return $this->$name;
    }
    /**
     * 取得 用户商品信息
     * @param unknown $condition
     * @param string $field
     */
    public function getGoodsInfo($condition = array(), $field = '*',$format=true) {
       $row = $this->table("member_goods")->field($field)->where($condition)->find();       
       if($format){
           $goods_info = @unserialize($row['goods_info']);
           if(!empty($goods_info)){
               $row = array_merge($row,$goods_info);
               $row['goods_info'] = $goods_info;
           }
       }
       return $row;
    }
    /**
     * 添加数据库购物车
     *
     * @param unknown_type $goods_info
     * @param unknown_type $quantity
     * @return unknown
     */
    public function addGoods($data = array()) {
        return $this->table("member_goods")->insert($data);
    }

    /**
     * 更新购物车
     *
     * @param   array   $param 商品信息
     */
    public function editGoods($data,$condition) {
        $result = $this->table("member_goods")->where($condition)->update($data);        
        return $result;
    }    
    /**
     * 删除购物车商品
     *
     * @param string $type 存储类型 db,cookie
     * @param unknown_type $condition
     */
    public function delGoods($condition = array()) {
        $result =  $this->table("member_goods")->where($condition)->delete();        
        return $result;
    }
}
