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
class store_taskModel extends Model {
    public function __construct(){
        parent::__construct('store_task');
    }

    /**
     * 列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getStoreTaskList($condition, $field = '*', $page = 0, $order = '', $limit = ''){
        $result = $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
        $this->cls();
        return $result;
    }

    /**
     * 详细信息
     * @param array $condition
     * @return array
     */
    public function getStoreTaskInfo($condition) {
        return $this->where($condition)->find();
    }



    /**
     * 添加
     * @param unknown $insert
     * @return boolean
     */
    public function addStoreTask($insert) {
        return $this->insert($insert);
    }

    /**
     * 版式
     * @param array $update
     * @param array $condition
     * @return boolean
     */
    public function editStoreTask($update, $condition) {

        $result = $this->where($condition)->update($update);
        return $result;
    }

    /**
     * 删除版式
     * @param array $condition
     * @return boolean
     */
    public function delStoreTask($condition) {

        $result = $this->where($condition)->delete();

        return $result;
    }


}
