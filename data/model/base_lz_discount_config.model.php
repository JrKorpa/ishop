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
class base_lz_discount_configModel extends Model {
    public function __construct(){
        parent::__construct('base_lz_discount_config');
    }

    /**
     * 获取裸钻打折管理列表
     *
     * @return mixed
     */
    public function getBaseLzDiscountConfigList($condition = array(), $fields = '*', $order='', $group = '', $page = null) {
        return $this->where($condition)->field($fields)->page($page)->limit(false)->order()->group($group)->select();
    }

    /**
     * 裸钻折扣详细信息
     * @param array $condition
     * @return array
     */
    public function getBaseLzDiscountConfigInfo($condition) {
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
    public function addBaseLzDiscountConfig($insert) {
        return $this->insert($insert);
    }

    /**
     * 更新版式
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editBaseLzDiscountConfig($update, $condition) {
        $list = $this->getBaseLzDiscountConfigList($condition, 'id');
        if (empty($list)) {
            return true;
        }
        $result = $this->where($condition)->update($update);
        if ($result) {
            foreach ($list as $val) {
                $this->_dStorePlateCache($val['id']);
            }
        }
        return $result;
    }

    /**
     * 删除版式
     * @param array $condition
     * @return boolean
     */
    public function delStorePlate($condition) {
        $list = $this->getBaseLzDiscountConfigList($condition, 'id');
        if (empty($list)) {
            return true;
        }
        $result = $this->where($condition)->delete();
        if ($result) {
            foreach ($list as $val) {
                $this->_dStorePlateCache($val['id']);
            }
        }
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
