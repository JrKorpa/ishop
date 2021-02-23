<?php
/**
 * 卖家账号模型
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class sellerModel extends Model{

    public function __construct(){
        parent::__construct('seller');
    }

    /**
     * 读取列表
     * @param array $condition
     *
     */
    public function getSellerList($condition, $page='', $order='', $field='*') {
        $result = $this->field($field)->where($condition)->page($page)->order($order)->select();
        return $result;
    }

    /**
     * 读取列表
     * @param array $condition
     *
     */
    public function getSellerStoreList($condition,$field="seller.`seller_id`,seller.`is_client`,seller.`last_login_time`,seller.`member_id`,seller.`seller_name`,seller_store.`is_admin`,seller_store.`seller_group_id`,seller_store.`seller_quicklink`,seller_store.`store_id`") {
        $result=$this->table('seller,seller_store')->field($field)->join('inner')->on('seller.`seller_id`=seller_store.`seller_id`')->where($condition)->select();
        return $result;
    }

    /**
     * 读取多条记录
     * @param array $condition
     *
     */
    public function getSellerallInfo() {
        $sql = "select seller_id,seller_name from seller";
        $result = $this->query($sql);
        return $result;
    }

    /**
     * 读取单条记录
     * @param array $condition
     *
     */
    public function getSellerInfo($condition) {
        $result = $this->where($condition)->find();
        return $result;
    }

    /**
     * 读取单条记录
     * @param array $condition
     *
     */
    public function getSellerStoreInfo($condition) {
        $result=$this->table('seller,seller_store')->field("seller.`seller_id`,seller.`is_client`,seller.`last_login_time`,seller.`member_id`,seller.`seller_name`,seller_store.`is_admin`,seller_store.`seller_group_id`,seller_store.`seller_quicklink`,seller_store.`store_id`")->join('inner')->on('seller.`seller_id`=seller_store.`seller_id`')->where($condition)->find();
        return $result;
    }

    /*
     *  判断是否存在
     *  @param array $condition
     *
     */
    public function isSellerExist($condition) {
        $result = $this->getSellerInfo($condition);
        if(empty($result)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /*
     * 增加
     * @param array $param
     * @return bool
     */
    public function addSeller($param){
        return $this->insert($param);
    }

    /*
     * 更新
     * @param array $update
     * @param array $condition
     * @return bool
     */
    public function editSeller($update, $condition){
        return $this->table('seller')->where($condition)->update($update);
    }

    /*
     * 删除
     * @param array $condition
     * @return bool
     */
    public function delSeller($condition){
        return $this->where($condition)->delete();
    }

}
