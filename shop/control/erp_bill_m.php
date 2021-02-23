<?php
/**
 * 调拨单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_mControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('store_bill,store_goods_index');
    }

    /**
     * 订单列表
     *
     */
    public function indexOp() {
        $this->addOp();
        exit;
    }
    
    /**
     * 新增调拨单
     *
     */
    public function addOp() {
        $store_id = $_SESSION['store_id'];
        $company_id = $_SESSION['store_company_id'];

        $warehouse_model = Model('erp_warehouse');
        $company_model = Model("company");
        $express_model = Model("express");
        $bill_m_type = array("ZC"=>"内部转仓","WX"=>"外部维修");
    
        $warehouse_list = $warehouse_model->getWareHouseList(array('company_id'=>$company_id,'is_enabled'=>1));
        $company_list  = $company_model->getCompanyList(array('is_deleted'=>0),5000,'','id,company_sn,company_name');
        $express_list = $express_model->getExpressList();
        
        
        Tpl::output('step',1);
        
        Tpl::output('express_list',$express_list);
        Tpl::output('warehouse_list',$warehouse_list);
        Tpl::output('bill_m_type',$bill_m_type);
        Tpl::output('company_list',$company_list);
        
        Tpl::output('company_id',$company_id);
        
        Tpl::showpage('erp_bill_m.add');
    }
    /**
     * 新增进货单 保存
     *
     */
    public function insertOp(){
        set_time_limit(300);
        $bill_type = "M";
        $result = array("success"=>0,'msg'=>'');
        if(!$_POST){
            $result = callback(false,"非法提交");
            exit(json_encode($result));
        }
        /**
         * 调拨类型
         */
        if(empty($_POST['bill_m_type'])){
            $result = callback(false,"调拨类型不能为空");
            exit(json_encode($result));
        }else{
            $bill_m_type = $_POST['bill_m_type'];
        }
        /**
         * 出库公司
         */
        if(empty($_POST['from_company_id'])){
            $result = callback(false,"出库公司不能为空");
            exit(json_encode($result));
        }else{
            $from_company_id = (int)$_POST['from_company_id'];
        }
        /**
         * 入库公司
         */
        if(empty($_POST['to_company_id'])){
            $result = callback(false,"入库公司不能为空");
            exit(json_encode($result));
        }else{
            $to_company_id = (int)$_POST['to_company_id'];
            $to_store_id = (int)$_POST['to_store_id'];
        }
        /**
         * 入库仓库
         */
        if(empty($_POST['to_house_id'])){
            $result = callback(false,"请选择入库仓库");
            exit(json_encode($result));
        }else{
            list($to_store_id,$to_house_id) = explode("|",$_POST['to_house_id']);
        }
        
              
        if(empty($_POST['goods_itemid'])){
            $result = callback(false,"货号不能为空");
            exit(json_encode($result));
        }        
        
        $goods_itemid_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        $goods_itemid_list = array_unique($goods_itemid_list);
        if(empty($goods_itemid_list)){
            $result = callback(false,"货号不能为空");
            exit(json_encode($result));
        }
        
        $remark = trim($_POST['remark']);

        $erp_bill_model	= new erp_billModel();
        $goods_items_model = new goods_itemsModel();
        $warehouse_model = new erp_warehouseModel();
        
        $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$to_house_id));
        if(isset($warehouse) && $warehouse['lock']>0){
            $result = callback(false,"入库仓库正在盘点中！");
            exit(json_encode($result));
        }
        //$_GET['debug'] = 1;
        foreach ($goods_itemid_list as $goods_itemid) {
            //根据库存编号查询商品 (item_id+store_id)
            $goods_info = $goods_items_model->getGoodsItemInfo(array('goods_id'=>$goods_itemid));
            if(empty($goods_info)){
                $result = callback(false,"{$goods_itemid}货号不存在");
                exit(json_encode($result));
            }else if($goods_info['company_id'] != $from_company_id){
                if($bill_m_type == "WX" && KELA_SPECIAL_BUSINESS){}else{
                    $result = callback(false,"{$goods_itemid}货号所在公司与当前出库公司不匹配！");
                    exit(json_encode($result)); 
                }
                
            }else if($goods_info['warehouse_id'] == $to_house_id){
                $result = callback(false,"{$goods_itemid}货号所在仓库与调拨仓库相同！");
                exit(json_encode($result));
            }
            $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$goods_info['warehouse_id']));
            if(isset($warehouse) && $warehouse['lock']>0){
                $result = callback(false,"{$goods_itemid}货号所在仓库正在盘点中！");
                exit(json_encode($result));
            }            
            if($bill_m_type == "ZC"){
                if($goods_info['is_on_sale'] != 2){
                    $result = callback(false,"{$goods_itemid}货号不是库存状态！");
                    exit(json_encode($result));
                }else if(in_array($goods_info['weixiu_status'],array(2,3,5,6))){
                    $result = callback(false,"{$goods_itemid}货号正在维修中！");
                    exit(json_encode($result));
                }
            }else{  
                if($goods_info['put_in_type']==5 && $to_company_id==58){
                    $result = callback(false,"{$goods_itemid}货号是自采商品，维修公司不能选总公司！");
                    exit(json_encode($result));
                }

                if($goods_info['is_shopzc'] == 1 && $to_company_id == 58){
                    $result['msg'] = "{$goods_itemid}货号原始入库方式是自采，维修公司不能选在总公司!";
                    echo json_encode($result);exit();
                }                
                if($goods_info['is_shopzc'] == 0 && $goods_info['put_in_type']!=5 && $to_company_id!=58){
                    $result = callback(false,"{$goods_itemid}货号是非自采商品，维修公司必须选总公司！");
                    exit(json_encode($result));
                }            
                if($goods_info['is_on_sale'] ==2){
                    //
                }else{
                    if($goods_info['is_on_sale'] ==5){
                        $result = callback(false,"{$goods_itemid}货号正在调拨中！");
                        exit(json_encode($result));
                    }else if($goods_info['is_on_sale'] !=3){
                        $result = callback(false,"{$goods_itemid}货号不是已销售状态！");
                        exit(json_encode($result));
                    }else if($goods_info['weixiu_status']!=3){
                        $result = callback(false,"{$goods_itemid}货号不是维修受理状态！");
                        exit(json_encode($result));
                    }
                }
                
            }
            $bill_goods_list[] = array(
                'goods_itemid'=>$goods_itemid,                
                'goods_sn'=>$goods_info['goods_sn'],
                'goods_name'=>$goods_info['goods_name'],
                'goods_count'=>1,
                'yuanshichengben'=>$goods_info['yuanshichengbenjia'],
                'mingyichengben'=>$goods_info['mingyichengben'],
                'jijiachengben'=>$goods_info['jijiachengben'],
                'sale_price'=>$goods_info['jijiachengben'],
                'from_store_id'=>$goods_info['store_id'],
                'from_house_id'=>$goods_info['warehouse_id'],
                'from_box_id'=>$goods_info['box_sn'],                
                'to_company_id'=>$to_company_id,
                'to_store_id'=>$to_store_id,
                'to_house_id'=>$to_house_id,
                'is_on_sale'=>$goods_info['is_on_sale']
            );
        }
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'item_type'=>$bill_m_type,
            'bill_status'=>1,
            'from_store_id'=>$_SESSION['store_id'],
            'from_company_id'=>$from_company_id,
            'to_store_id'=>$to_store_id,
            'to_company_id'=>$to_company_id,
            'to_house_id'=>$to_house_id,
            'express_id'=>$_POST['express_id'],
            'express_sn' =>$_POST["express_sn"],
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s"),
            'remark'=>$remark,            
        );
        /*
        if($bill_m_type=="WX"){
            $bill_info['bill_status'] = 2;
            $bill_info['check_time'] = date("Y-m-d H:i:s",TIMESTAMP);
            $bill_info['check_user'] = $_SESSION['seller_name'];
        }*/
        //$erp_bill_model->beginTransaction();
        $res = $erp_bill_model->createBillM($bill_info,$bill_goods_list,true);
        if($res['success'] == 0){  
            //$erp_bill_model->rollback();
            $result = callback(false,$res['msg']);
            exit(json_encode($result));
        }else{
            $bill_info = $res['data'];
        }
        /*
        if($bill_m_type == "WX"){
            $res = $erp_bill_model->checkBill($bill_id,1,"审核通过",false);
            if($res['success']==0){
                $erp_bill_model->rollback();
                $result = callback(false,$res['msg']);
                exit(json_encode($result));
            }  
            
            if ($bill_info['to_company_id'] == '58') {
                // 维修调拨单M到总部
                EventEmitter::dispatch("erp", array('event' => 'sync_bill', 'bill_id' => $bill_id, 'bill_no' => $bill_no));
            }
        }      
        $erp_bill_model->commit();        
        */
        $result = callback(true,"保存成功",$bill_info);
        exit(json_encode($result));
    
    }    
    
    /**
     * 获取货品信息
     */
    public function get_goods_listOp(){
        $goods_itemid_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        if(empty($goods_itemid_list)){
            $result = callback(false,"货号不能为空");
            exit(json_encode($result));
        }
        $goods_model = Model('goods_items');
        $goods_list = $goods_model->getGoodsItemsList(array("goods_id"=>array('in',$goods_itemid_list)));
        
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        if(!empty($goods_list)){
            foreach ($goods_list as &$goods) {
                if(!$show_chengben){
                    $goods['mingyichengben'] = "/";
                }
            }
        }
    
        $result = callback(true,"");
        $result['data']['goods_list']=$goods_list;
        $result['data']['total_count']=count($goods_list);
        $result['data']['total_chengben']=array_sum(array_column($goods_list,"mingyichengben"));
        exit(json_encode($result));
    }
    
}