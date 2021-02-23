<?php
/**
 * 进货单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_goodsControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('store_bill,store_goods_index');
    }

    /**
     * 订单列表
     *
     */
    public function indexOp() {
        Tpl::showpage('erp_bill_l.index');
    }
    
}
