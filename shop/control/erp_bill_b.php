<?php
/**
 * 退货返厂单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_bControl extends BaseSellerControl {
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
     * 新增退货返厂单
     *
     */
    public function addOp() {
        $store_id = $_SESSION['store_id'];
        $company_model = Model('company'); 
        $express_model = Model('express');
        
        $data = $company_model->get_put_company();//var_dump($JxcWholesale, $put_company);die;
        $company_list = $data['company_list'];
        $wholesale_list = $data['wholesale_list'];
        
        $put_type_list = goods_itemsModel::getGoodsPutTypeListBybillB();
        $express_list = $express_model->getExpressList();
        
        Tpl::output('step',1);
        Tpl::output('express_list', $express_list);
        Tpl::output('put_type_list',$put_type_list);
        Tpl::output('company_list',$company_list); 
        Tpl::output('wholesale_list',$wholesale_list);
        
        $_GET['put_out_type'] = $_GET['put_out_type']?$_GET['put_out_type']:5;
        if($_GET['put_out_type']==5){
            Tpl::showpage('erp_bill_b.add');
        }else{
            Tpl::showpage('erp_bill_d.add');
        }
    }
    /**
     * 新增退货返厂单 保存
     *
     */
    public function insertOp(){
        set_time_limit(300);
        $bill_type = "B";
        $result = array("success"=>0,'msg'=>'');
        if(!$_POST){
            $result = callback(false,"非法提交");
            exit(json_encode($result));
        }
        $from_company_id = $_SESSION['store_company_id'];
        $from_store_id = $_SESSION['store_id'];
               
        /**
         * 供应商
         */
        if(empty($_POST['supplier_id'])){
            $result = callback(false,"供应商不能为空");
            exit(json_encode($result));
        }else{
            $supplier_id = (int)$_POST['supplier_id'];
        }
        /**
         * 出库方式
         */
        if(empty($_POST['put_out_type'])){
            $result = callback(false,"出库方式不能为空");
            exit(json_encode($result));
        }else{
            $put_out_type = (int)$_POST['put_out_type'];
        }
        
        //货号
        $goods_itemid_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        $goods_itemid_list = array_unique($goods_itemid_list);
        if(empty($goods_itemid_list)){
            $result = callback(false,"货号不能为空");
            exit(json_encode($result));
        }
        if(empty(trim($_POST['remark']))){
            $result = callback(false,"请填写单据备注");
            exit(json_encode($result));
        }else{
            $remark = trim($_POST['remark']);
        }
        $erp_bill_model	= new erp_billModel();
        $goods_items_model = new goods_itemsModel();
        $warehouse_model = new erp_warehouseModel();
        //$_GET['debug'] = 1;
        foreach ($goods_itemid_list as $goods_itemid) {            
            $goods_info = $goods_items_model->getGoodsItemInfo(array('goods_id'=>$goods_itemid));
            if(empty($goods_info)){
                $result = callback(false,"货号{$goods_itemid}不存在");
                exit(json_encode($result));
            }else if($goods_info['is_on_sale'] != 2){
                $result = callback(false,"货号{$goods_itemid}不是库存状态");
                exit(json_encode($result));
            }else if($goods_info['company_id'] != $from_company_id){
                $result = callback(false,"货号{$goods_itemid}不是本公司货品");
                exit(json_encode($result));
            }else if($goods_info['prc_id'] != $supplier_id){
                $result = callback(false,"货号{$goods_itemid}的供应商与所选供应商不一致");
                exit(json_encode($result));
            }
            $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$goods_info['warehouse_id']));
            if(isset($warehouse) && $warehouse['lock']>0){
                $result = callback(false,"{$goods_itemid}货号所属仓库正在盘点中");
                exit(json_encode($result));
            }
            $bill_goods_list[] = array(
                'goods_itemid'=>$goods_itemid,
                'goods_sn'=>$goods_info['goods_sn'],
                'goods_name'=>$goods_info['goods_name'],
                'goods_count'=>1,
                'yuanshichengben'=>$goods_info['yuanshichengbenjia'],
                'mingyichengben'=>$goods_info['mingyichengben'],
                'jijiachengben'=>$goods_info['jijiachengben'],
                'from_store_id'=>$goods_info['store_id'],
                'from_house_id'=>$goods_info['warehouse_id'],
                'from_box_id'=>$goods_info['box_sn'],                
                'to_company_id'=>$to_company_id,
                'to_store_id'=>$to_store_id,
            );
        }
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'bill_status'=>1,
            'express_id'=>$_POST["express_id"],
            'express_sn' =>$_POST["express_sn"],
            'from_store_id'=>$from_store_id,
            'from_company_id'=>$from_company_id,
            'supplier_id'=>$supplier_id,
            'out_warehouse_type'=>$put_out_type,
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>$remark,            
        );
        $res = $erp_bill_model->createBillB($bill_info,$bill_goods_list);
        if($res['success']==0){
            $result = callback(false,$res['error']);
            exit(json_encode($result));
        }else{
            $result = callback(true,"保存成功",$res['data']);
            exit(json_encode($result));
        }
    
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