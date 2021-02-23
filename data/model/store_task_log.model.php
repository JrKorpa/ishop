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
class store_task_logModel extends Model {
    public function __construct(){
        parent::__construct('store_task_log');
    }

    /**
     * 日志列表
     * @param array $condition
     * @param string $field
     * @param int $page
     * @return array
     */
    public function getLogList($condition, $field = '*', $page = 0, $order = '', $limit = ''){
        return $this->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }


    /**
     * 添加日志
     * @param unknown $insert
     * @return boolean
     */
    public function addLog($insert) {
        return $this->insert($insert);
    }
    



}
