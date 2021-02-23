<?php
/**
 * 维修出库单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
/**
 * 维修出库
 * @author Administrator
 *
 */
class erp_bill_swControl extends BaseSellerControl{
    public function __construct() {
        parent::__construct();
        Language::read('store_bill,store_goods_index');
    }
    

    /**
     * 新增维修出库
     *
     */
    public function addOp() {
        Tpl::output('step',1);
        Tpl::showpage('erp_bill_sw.add');
    }
}
