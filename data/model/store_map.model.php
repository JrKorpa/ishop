<?php
/**
 * 店铺地址百度地图
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class store_mapModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加店铺地址
     *
     * @param
     * @return int
     */
    public function addStoreMap($map_array) {
        $map_id = $this->table('store_map')->insert($map_array);
        return $map_id;
    }

    /**
     * 删除店铺地址记录
     *
     * @param
     * @return bool
     */
    public function delStoreMap($condition) {
        if (empty($condition)) {
            return false;
        } else {
            $result = $this->table('store_map')->where($condition)->delete();
            return $result;
        }
    }

    /**
     * 修改店铺地址记录
     *
     * @param
     * @return bool
     */
    public function editStoreMap($condition, $data) {
        if (empty($condition)) {
            return false;
        }
        if (is_array($data)) {
            $result = $this->table('store_map')->where($condition)->update($data);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 店铺地址记录
     *
     * @param
     * @return array
     */
    public function getStoreMapList($condition = array(), $page = '', $limit = '', $order = 'map_id desc') {
        $result = $this->table('store_map')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }
}
