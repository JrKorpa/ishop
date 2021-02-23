<?php
/**
 * 会员模型
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class order_pay_actionModel extends Model {

    public function __construct(){
        parent::__construct('order_pay_action');
    }

    /**
     * 增加支付记录
     *
     * @param
     * @return int
     */
    public function addOrderPayAction($log_array) {
        $log_id = $this->table('order_pay_action')->insert($log_array);
        return $log_id;
    }


    /**
     * 会员列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getOrderPayActionList($condition = array(), $field = '*', $page = null, $order = 'pay_id desc', $limit = '') {
       return $this->table('order_pay_action')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }


    /**
     * 会员列表
     * @param array $condition
     * @param string $field
     * @param number $page
     * @param string $order
     */
    public function getOrderPayActionJoinList($condition = array(), $field = '*', $page = null, $order = 'pay_id desc', $limit = '') {
        return $this->table('order_pay_action,orders')->join('left')->on('orders.order_id = order_pay_action.order_id')->field($field)->where($condition)->page($page)->order($order)->limit($limit)->select();
    }

}
