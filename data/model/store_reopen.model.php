<?php
/**
 * 店铺续签
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class store_reopenModel extends Model {
    public function __construct() {
        parent::__construct('store_reopen');
    }

    /**
     * 取得列表
     * @param unknown $condition
     * @param string $pagesize
     * @param string $order
     */
    public function getStoreReopenList($condition = array(), $pagesize = '', $order = 're_id desc') {
        return $this->where($condition)->order($order)->page($pagesize)->select();
    }

    /**
     * 增加新记录
     * @param unknown $data
     * @return
     */
    public function addStoreReopen($data) {
        return $this->insert($data);
    }

    /**
     * 取单条信息
     * @param unknown $condition
     */
    public function getStoreReopenInfo($condition) {
        return $this->where($condition)->find();
    }

    /**
     * 更新记录
     * @param unknown $condition
     * @param unknown $data
     */
    public function editStoreReopen($data,$condition) {
        return $this->where($condition)->update($data);
    }

    /**
     * 取得数量
     * @param unknown $condition
     */
    public function getStoreReopenCount($condition) {
        return $this->where($condition)->count();
    }

    /**
     * 删除记录
     * @param unknown $condition
     */
    public function delStoreReopen($condition) {
        return $this->where($condition)->delete();
    }
}
