<?php
/**
 * 店铺模型管理
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class diamond_jiajialvModel extends Model {
    public function __construct(){
        parent::__construct('diamond_jiajialv');
    }

    /**
     * 裸钻加价率列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getDiamondJiajialvList($condition, $field = '*', $page = 0, $order = '', $limit = ''){
        $on = 'diamond_jiajialv.channel_id = store.store_id';
        $result = $this->table('diamond_jiajialv,store')->field($field)->join('inner')->on($on)->where($condition)->page($page)->order($order)->limit($limit)->select();
        $this->cls();
        return $result;
    }

    /**
     * 版式详细信息
     * @param array $condition
     * @return array
     */
    public function getDiamondJiajialvInfo($condition) {
        return $this->where($condition)->find();
    }

    public function getStorePlateInfoByID($plate_id, $fields = '*') {
        $info = $this->_rStorePlateCache($plate_id, $fields);
        if (empty($info)) {
            $info = $this->getStorePlateInfo(array('plate_id' => $plate_id));
            $this->_wStorePlateCache($plate_id, $info);
        }
        return $info;
    }

    /**
     * 添加版式
     * @param unknown $insert
     * @return boolean
     */
    public function addDiamondJiajialv($insert) {
        return $this->insert($insert);
    }

    /**
     * 更新版式
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editDiamondJiajialv($update, $condition) {

        $result = $this->where($condition)->update($update);
        return $result;
    }

    /**
     * 删除版式
     * @param array $condition
     * @return boolean
     */
    public function delDiamondJiajialv($condition) {

        $result = $this->where($condition)->delete();

        return $result;
    }

    /**
     * 读取店铺关联板式缓存缓存
     * @param int $plate_id
     * @param string $fields
     * @return array
     */
    private function _rStorePlateCache($plate_id, $fields) {
        return rcache($plate_id, 'store_plate', $fields);
    }

    /**
     * 写入店铺关联板式缓存缓存
     * @param int $plate_id
     * @param array $info
     * @return boolean
     */
    private function _wStorePlateCache($plate_id, $info) {
        return wcache($plate_id, $info, 'store_plate');
    }

    /**
     * 删除店铺关联板式缓存缓存
     * @param int $plate_id
     * @return boolean
     */
    private function _dStorePlateCache($plate_id) {
        return dcache($plate_id, 'store_plate');
    }
}
