<?php
/**
 * 维修调拨单
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
class erp_bill_mwControl extends BaseSellerControl{
    public function __construct() {
        parent::__construct();
        Language::read('store_bill,store_goods_index');
    }
    

    /**
     * 新增维修出库
     *
     */
    public function addOp() {
        $store_id = $_SESSION['store_id'];
        $company_id = $_SESSION['store_company_id'];

        //$warehouse_model = Model('erp_warehouse');
        $company_model = Model("company");
        $express_model = Model("express");
        $bill_m_type = array("ZC"=>"内部转仓","WX"=>"外部维修");
    
        //$warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$store_id,'is_enabled'=>1));
        $companyWhere = array('id'=>array('in',array($company_id,58)));
        $company_list  = $company_model->getCompanyList($companyWhere,2,'','id,company_sn,company_name');
        $express_list = $express_model->getExpressList();
        //$weixiu_company_list  = $company_model->getCompanyList(array('id'=>array($company_id,58)),2,'','id,company_sn,company_name');
        
        
        Tpl::output('step',1);
        
        Tpl::output('express_list',$express_list);
        Tpl::output('warehouse_list',$warehouse_list);
        Tpl::output('bill_m_type',$bill_m_type);
        Tpl::output('company_list',$company_list);        
        Tpl::output('company_id',$company_id);        
        Tpl::showpage('erp_bill_mw.add');
    }
}
