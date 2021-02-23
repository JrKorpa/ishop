<?php
/**
 * 维修入库单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
/**
 * 维修入库单
 * @author Administrator
 *
 */
class erp_bill_dwControl extends BaseSellerControl{
    public function __construct() {
        parent::__construct();
        Language::read('store_bill,store_goods_index');
    }
    

    /**
     * 新增入库单
     *
     */
    public function addOp() {
        $store_id = $_SESSION['store_id'];
        $warehouse_model = Model("erp_warehouse");
        $warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$store_id,'is_enabled'=>1,'type'=>11));
        Tpl::output('step',1);
        Tpl::output('warehouse_list',$warehouse_list);
        Tpl::showpage('erp_bill_dw.add');
    }
}
