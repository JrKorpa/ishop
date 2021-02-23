<?php
/**
 * 库存商品视图模型 
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_goodsModel extends Model {
    public function __construct(){
        parent::__construct('goods_items');
    }
    /**
     * 库存商品查询
     * @param unknown $condition
     * @param string $fields
     * @param string $pagesize
     * @param string $order
     * @param string $limit
     */
    public function getErpBillList($condition = array(), $fields = '*', $pagesize = null, $order = '', $limit = null) {
        return $this->table('goods_items')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
    
}    
?>