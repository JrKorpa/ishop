<?php
/**
 * 支付方式
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class order_payment_logModel extends Model {
    /**
     * 开启状态标识
     * @var unknown
     */
    const STATE_OPEN = 1;

    public function __construct() {
        parent::__construct('order_payment_log');
    }


    /**
     * 增加支付记录
     *
     * @param
     * @return int
     */
    public function addOrderPaymentLog($log_array) {
        $log_id = $this->table('order_payment_log')->insert($log_array);
        return $log_id;
    }

    /**
     * 读取单行信息
     *
     * @param
     * @return array 数组格式的返回结果
     */
    public function getOrderPaymentLogInfo($condition = array()) {
        return $this->where($condition)->find();
    }


    /**
     * 读取多行
     *
     * @param
     * @return array 数组格式的返回结果
     */
    public function getOrderPaymentLogList($condition = array()){
        return $this->where($condition)->select();
    }


    /**
     * 更新信息
     *
     * @param array $param 更新数据
     * @return bool 布尔类型的返回结果
     */
    public function editOrderPaymentLog($data, $condition){
        return $this->where($condition)->update($data);
    }

    /**
     * 读取支付方式信息by Condition
     *
     * @param
     * @return array 数组格式的返回结果
     */
    public function getRowByCondition($conditionfield,$conditionvalue){
        $param  = array();
        $param['table'] = 'order_payment_log';
        $param['field'] = $conditionfield;
        $param['value'] = $conditionvalue;
        $result = Db::getRow($param);
        return $result;
    }
}
