<?php
/**
 * 字典模型
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class dictModel extends Model {
    public function __construct() {
        parent::__construct('dict');
    }

    /**
     * 添加字典
     * @param array $insert
     * @return boolean
     */
    public function addDict($insert) {
        return $this->insert($insert);
    }

    /**
     * 编辑字典
     * @param array $condition
     * @param array $update
     * @return boolean
     */
    public function editDict($condition, $update) {
        return $this->where($condition)->update($update);
    }


    /**
     * 字典列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param number $page
     * @param string $limit
     * @return array
     */
    public function getDictList($condition, $field = '*', $page = 0, $order = 'id asc', $limit = '') {
        return $this->where($condition)->field($field)->order($order)->page($page)->limit($limit)->select();
    }

    /**
     * 取单个字典内容
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getDictInfo($condition, $field = '*') {
        return $this->field($field)->where($condition)->find();
    }
}
