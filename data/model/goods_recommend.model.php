<?php
/**
 * 商品推荐
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class goods_recommendModel extends Model{

    public function __construct(){
        parent::__construct('goods_recommend');
    }

    /**
     * 读取列表
     * @param array $condition
     *
     */
    public function getGoodsRecommendList($condition, $page = '', $order = '', $field = '*', $gourpby = '', $key = '',$count = null) {
        return $this->field($field)->where($condition)->page($page,$count)->order($order)->group($gourpby)->key($key)->select();
    }

    /**
     * 读取单条记录
     * @param array $condition
     *
     */
    public function getGoodsRecommendInfo($condition) {
        return $this->where($condition)->find();
    }

    /*
     * 增加
     * @param array $data
     * @return bool
     */
    public function addGoodsRecommend($data){
        return $this->insertAll($data);
    }

    public function editGoodsRecommend($data,$condition) {
        return $this->where($condition)->update($data);
    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     */
    public function delGoodsRecommend($condition){
        return $this->where($condition)->delete();
    }

    public function getGoodsRecommendCount($condition = array(), $field = '') {
        return $this->where($condition)->count($field);
    }

}
