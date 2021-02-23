<?php
/**
 * 退货单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_dControl extends BaseSellerControl {

    public $out_warehouse_type = array(1=>'购买', 2=>'借货');
    public function __construct() {
        parent::__construct();
        Language::read('store_bill,store_goods_index');
    }

    /**
     * 批发退货单
     *
     */
    public function addOp() {
        //$store_id = $_SESSION['store_id'];
        //$supplier_model = Model('store_supplier');
        //$warehouse_model = Model('erp_warehouse');
        //$wholesale_model = Model('jxc_wholesale');
        $express_model = Model('express');
        $company_model = Model('company');

        //$supplier_list = $supplier_model->getStoreSupplierList(array('sup_store_id'=>$store_id,'is_enabled'=>1));
        //print_r($supplier_list);
        //$warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$store_id,'is_enabled'=>1));
        //$jxc_wholesale = $wholesale_model->getJxcWholesaleList(array('wholesale_status'=>1));
        $express_list = $express_model->getExpressList();
        //$companylist = $company_model->getCompanyList(array('is_deleted'=>0));
        //$from_company = array($_SESSION['store_company_id']=>$_SESSION['store_company_name']);
        //$management_api = data_gateway('imanagement');
        //$ptype = $management_api->get_dictlist(array('name'=>'warehouse.ptype'));
        $data = $company_model->get_put_company();//var_dump($JxcWholesale, $put_company);die;
        $company_list = $data['company_list'];
        $wholesale_list = $data['wholesale_list'];
        //var_dump($companylist);die;
        $put_type_list = goods_itemsModel::getGoodsPutTypeList();
        
        Tpl::output('step',1);
        Tpl::output('put_type_list',$put_type_list);

        //Tpl::output('ptype',$ptype);
        //Tpl::output('sales_type', array('PF'=>'批发', 'WX'=>'维修'));
        //Tpl::output('pifa_type', $this->out_warehouse_type);
        //Tpl::output('from_company', $from_company);
        Tpl::output('express_list', $express_list);
        Tpl::output('company_list', $company_list);
        Tpl::output('wholesale_list', $wholesale_list);
        //Tpl::output('supplier_list',$supplier_list);
        //Tpl::output('warehouse_list',$warehouse_list);
        Tpl::showpage('erp_bill_d.add');
    }

    /**
     * 新增进货单 保存
     *
     */
    public  function insertOp(){
        set_time_limit(0);
        $result = array("success"=>0,'msg'=>'');
        if(!$_POST){
            $result['msg'] = "非法提交";
            exit(json_encode($result));
        }

        /**
         * 销售类型
         */
        if(empty($_POST['sales_type'])){
            $result['msg'] = "请选择退货类型";
            exit(json_encode($result));
        }
        $sales_type = trim($_POST['sales_type']);
        if($sales_type == 'PF'){
            /**
             * 出库类型
             */
            if(empty($_POST['put_out_type'])){
                $result['msg'] = "请选择出库方式";
                exit(json_encode($result));
            }else{
                $put_out_type = trim($_POST['put_out_type']);
            }

            /**
             * 批发客户
             */
            if(empty($_POST['jxc_wholesale'])){
                $result['msg'] = "请选择退货客户";
                exit(json_encode($result));
            }else{
                $jxc_wholesale = trim($_POST['jxc_wholesale']);
            }

            $jxc_wholesale_model = new jxc_wholesaleModel();
            $jxc_wholesale_info = $jxc_wholesale_model->getJxcWholesaleInfo(array('wholesale_id'=>$jxc_wholesale),'sign_company');
            $sign_company = $jxc_wholesale_info['sign_company'];
            if($sign_company != $_SESSION['store_company_id']){
                $result['msg'] = "退货客户所属公司不是当前门店所属公司";
                exit(json_encode($result));
            }




            /**
             * 批发价
             */
            /* if(empty($_POST['pifajia'])){
                $result['msg'] = "请填写退货价";
                exit(json_encode($result));
            }else{
                $pifajia = $_POST['pifajia'];
            } */

            /**
             * 入库公司
             */
            if(empty($_POST['to_company_id'])){
                $result['msg'] = "请选择供应商";
                exit(json_encode($result));
            }else{
                $to_company_id = trim($_POST['to_company_id']);
            }
            $to_house_id = "";
        }else{
            $to_company_id = $_SESSION['store_company_id'];
            if(empty($_POST['to_house_id'])){
                $result['msg'] = "请选择入库仓库";
                exit(json_encode($result));
            }else{
                $to_house_id = $_POST['to_house_id'];
            }            
        }




        /**
         * 货号
         */
        if(empty($_POST['goods_itemid'])){
            $result['msg'] = "请填写货号";
            exit(json_encode($result));
        }else{
            $goods_itemid = $_POST['goods_itemid'];
            $res = $this->get_warehouse_goods_ajaxOp($goods_itemid);
            if($res['success']){
                $goods_lists = $res['data'];
            }
        }
        

        /**
         * 备注
         */
        if(empty($_POST['remark'])){
            $result['msg'] = "请填写备注";
            exit(json_encode($result));
        }else{
            $remark = $_POST['remark'];//备注
        }
        $pifajia = $_POST['pifajia'];
        
        
        $erp_bill_model	= Model('erp_bill');
        $goods_model = Model('erp_goods');
        $bill_goods_list = array();
        /*
        $goods_itemid = str_replace(' ',',',$goods_itemid);
        $goods_itemid = str_replace('，',',',$goods_itemid);
        $goods_itemid = str_replace(array("\r\n", "\r", "\n"),',',$goods_itemid);
        $goods_arr = explode(",", $goods_itemid);
        $goods_arr = array_unique(array_filter($goods_arr));
        */
        $goods_itemid_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        $goods_itemid_list = array_unique($goods_itemid_list);
        
        $pifajia_total = 0;
        $guanlifei_total = 0;
        $total_chengben = 0;
        $from_company_id = $_SESSION['store_company_id'];
        if($sales_type == "WX"){
            $to_company_id = $_SESSION['store_company_id'];
        }





        foreach ($goods_itemid_list as $key => $goods_id) {
            $goods_list = $goods_model->getErpBillList(array('goods_id'=>$goods_id));
            $goods_list = isset($goods_list[0])?$goods_list[0]:array();
            $goods_info = array();
            $goods_info['goods_itemid'] = $goods_list['goods_id'];
            $goods_info['goods_sn'] = $goods_list['goods_sn'];
            $goods_info['goods_name'] = $goods_list['goods_name'];
            $goods_info['goods_count'] = 1;
            //$goods_info['remark'] = $goods_list['goods_id'];
            $goods_info['from_company_id'] = $from_company_id;
            //$goods_info['from_house_id'] = $goods_list['goods_id'];
            $goods_info['to_company_id'] = $to_company_id;
            $goods_info['to_house_id'] = $to_house_id;
            $goods_info['in_warehouse_type'] = $goods_list['put_in_type'];
            //$goods_info['yuanshichengben'] = $goods_list['yuanshichengbenjia'];
            $goods_info['yuanshichengben'] = $goods_list['jijiachengben'];
            $goods_info['management_fee'] = $goods_list['management_fee'];
            //$goods_info['is_settled'] = $goods_list['goods_id'];
            //$goods_info['settle_user'] = $goods_list['goods_id'];
            if($sales_type == 'PF'){
               if(!isset($pifajia[$key]) || empty($pifajia[$key])){
                    $result['msg'] = "请填写退货价";
                    exit(json_encode($result));
                } 
            }
            /*if(!isset($guanlifei[$key]) || empty($guanlifei[$key])){
                $result['msg'] = "请填写管理费";
                exit(json_encode($result));
            }*/
            $goods_info['sale_price'] = $pifajia[$key];
            //$goods_info['management_fee'] = $guanlifei[$key];
            $bill_goods_list[] = $goods_info;
            $pifajia_total = bcadd($pifajia_total, $goods_info['sale_price'], 2);
            //$guanlifei_total = bcadd($guanlifei_total, $goods_info['management_fee'], 2);
            $total_chengben = bcadd($total_chengben, $goods_list['yuanshichengbenjia'], 2);
        }
        $bill_type = "D";
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'item_type'=>$sales_type,
            'bill_status'=>1,
            'from_company_id'=>$from_company_id,
            'wholesale_id'=>$jxc_wholesale,
            'chengben_total'=>$total_chengben,
            'express_id'=>$_POST["express_id"],
            'express_sn' => $_POST["express_sn"],
            'from_store_id'=>$_SESSION['store_id'],
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>$remark,
            'to_company_id'=>$to_company_id,
            'to_house_id'=>$to_house_id,
        );
        if($sales_type == "PF"){
            $bill_info['in_warehouse_type'] = $put_out_type;
            $bill_info['out_warehouse_type'] = $put_out_type;
        }
        $res = $erp_bill_model->createBill($bill_info,$bill_goods_list,$bill_type);
        if($res['success']==1){
            $result['success'] = 1;
            $result['data'] = $res['data'];
            exit(json_encode($result));
        }else{
            $result['success'] = 0;
            $result['msg'] = $res['msg'];
            exit(json_encode($result));
        }        
        
    }
    /**
     * 获取货品
     *
     */
    public function get_warehouse_goods_ajaxOp($goods_itemid="") {

        $result = array('success'=>0, 'msg'=>'' ,'data'=>array());
        $goods_ids = !empty($goods_itemid)?$goods_itemid:$_POST['goods_ids'];
        $sales_type = $_POST['sales_type'];
        if ($sales_type == "") {
            $result['msg'] = "请选择退货类型!";
            echo json_encode($result);exit();
        }
        if (!in_array($sales_type, array('PF', 'WX'))) {
            $result['msg'] = "退货类型错误!";
            echo json_encode($result);exit();
        }
        if($sales_type == "PF"){
            if(empty($_POST['put_out_type'])){
                $result['msg'] = "请选择出库方式!";
                echo json_encode($result);exit();
            }
            if(empty($_POST['to_company_id'])){
                $result['msg'] = "请选择供应商!";
                echo json_encode($result);exit();
            }
        }
  
        if ($goods_ids == "") {
            $result['msg'] = "货号不能为空!";
            echo json_encode($result);exit();
        }
        /*$goods_ids = str_replace(' ',',',$goods_ids);
        $goods_ids = str_replace('，',',',$goods_ids);
        $goods_ids = str_replace(array("\r\n", "\r", "\n"),',',$goods_ids);
        $goods_arr = explode(",", $goods_ids);
        $goods_arr = array_unique(array_filter($goods_arr));
        */
        $goods_itemid_list = preg_replace("/\s+/is",",",$goods_ids);
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        $goods_itemid_list = array_unique($goods_itemid_list);
        
        $error_goods = $error_goods_pf = $error_goods_wx = $error_weixiu_status = array();
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        $data = array();
        foreach ($goods_itemid_list as $key => $goods_id) {
            $item_where = array('goods_id'=>$goods_id, 'company_id'=>$_SESSION['store_company_id']);
            if($sales_type == "WX" && KELA_SPECIAL_BUSINESS) unset($item_where['company_id']);
            $goods_list = Model('erp_goods')->getErpBillList($item_where);            
            if(empty($goods_list)){                
                $error_goods[] = $goods_id;
                continue;
            }
            $goods_info = $goods_list[0];
            $warehouse = Model('erp_warehouse')->getWareHouseInfo(array('house_id'=>$goods_info['warehouse_id']));
            if(isset($warehouse) && $warehouse['lock']>0){
                $result = callback(false,"{$goods_id}货号所属仓库正在盘点中");
                exit(json_encode($result));
            }                        
            if($sales_type == "WX"){
                if(!empty($goods_info['order_sn']) && !empty($goods_info['order_detail_id'])){
                    $model_refund_return = Model('refund_return');
                    $return_list = $model_refund_return->getRefundReturnInfo(array('order_goods_id'=>$goods_info['order_detail_id'], 'seller_state'=>1));
                    if(!empty($return_list)){
                        $result['msg'] = "新增维修退货单时货品“".$goods_id."”不能存在待审核的退货记录!";
                        echo json_encode($result);exit();
                    }
                }else{
                    /* 因部分货品已销售，但不在系统下单，此处放开限制
                    $result['msg'] = $goods_id."货号没有查询到绑定的订单号和商品明细ID，不能做维修退货单!";
                    echo json_encode($result);exit();
                    */
                }
                if($goods_info['is_on_sale'] != 3){
                    $error_goods_wx[] = $goods_id;
                    continue;
                }
                if(in_array($goods_info['weixiu_status'], array(2,3,5,6))){
                    $error_weixiu_status[] = $goods_id;
                    continue;
                }
                
            }else{   
                if($goods_info['is_on_sale'] != 2){
                    $error_goods_pf[] = $goods_id;
                    continue;
                }             
                if($goods_info['put_in_type'] != $_POST['put_out_type']){
                    $result['msg'] = $goods_id."货号入库方式与当前所选出库方式不一致!";
                    echo json_encode($result);exit();
                }
                
                if($goods_info['prc_id'] != $_POST['to_company_id']){
                    $result['msg'] = $goods_id."货号的供应商与当前所选供应商不一致!";
                    echo json_encode($result);exit();
                }

               //判断是否有S单，没有可以跳过下面判断
                $bill_haver = Model("erp_bill")->getBillGooodsInfoPWhereGoodsId($goods_id);
                if(!empty($bill_haver)){
                    $bill_info = Model("erp_bill")->getBillGooodsInfoPWhereCompany($goods_id);
                    if(empty($bill_info)){
                        $result['msg'] = $goods_id."货号没有查询到最新已审核的批发销售单!";
                        echo json_encode($result);exit();
                    }else{
                        if($bill_info['wholesale_id'] != $_POST['jxc_wholesale']){
                            $result['msg'] = $goods_id."退货客户与批发过来的批发客户不一致";
                            echo json_encode($result);exit();
                        }
                    }
                }

                $goods_info['yuanshichengben'] = !empty($bill_info['yuanshichengben'])?$bill_info['yuanshichengben']:"";
                //$goods_info['mingyichengben'] = !empty($bill_info['mingyichengben'])?$bill_info['mingyichengben']:"";
                //$goods_info['jijiachengben'] = !empty($bill_info['jijiachengben'])?$bill_info['jijiachengben']:"";
                //$goods_info['sale_price'] = !empty($bill_info['sale_price'])?$bill_info['sale_price']:"";
                $goods_info['out_warehouse_type'] = !empty($bill_info['out_warehouse_type'])?$this->out_warehouse_type[$bill_info['out_warehouse_type']]:"";
                          
            }       
            if(!$show_chengben){
                $goods_info['yuanshichengbenjia'] = '/';
                $goods_info['jijiachengben'] = '/';
            }     
            $data[] = $goods_info;     
           
        }
        if (!empty($error_goods)) {
            $result['msg'] = implode(",", $error_goods)."货号不正确，未查到货品!";
            echo json_encode($result);exit();
        }elseif(!empty($error_goods_pf) && $sales_type == "PF"){
            $result['msg'] = implode(",", $error_goods_pf)."货号不是库存状态，只有库存状态的货品才能做批发退货单!";
            echo json_encode($result);exit();
        }elseif(!empty($error_goods_wx) && $sales_type == "WX"){
            $result['msg'] = implode(",", $error_goods_wx)."货号不是已销售状态，只有已销售状态的货品才能做维修退货单!";
            echo json_encode($result);exit();
        }elseif(!empty($error_weixiu_status) && $sales_type == "WX"){
            $result['msg'] = implode(",", $error_weixiu_status)."货号已经申请维修!";
            echo json_encode($result);exit();
        }else{}
        $result['success'] = 1;
        $result['data'] = $data;
        if(empty($goods_itemid)){
            echo json_encode($result);exit();
        }else{
            return $result;
        }
    }

    /**
     * 下载进货单导入模板
     */
    /*public function down_templateOp(){
        set_time_limit(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=进货单模板.csv");
        header('Cache-Control: max-age=0');
        $title_arr = array(
            'SKU码','成本价',"入库数量","商品成色(1-10整数范围内)"
        );
        foreach ($title_arr as $k => $v) {
            $title_arr[$k] = iconv('utf-8', 'GB18030', $v);
        }
        echo "\"".implode("\",\"",$title_arr)."\"\r\n";
    }*/
    
    
    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @param boolean $allow_promotion
     * @return
     */
    private function profile_menu($menu_type,$menu_key, $allow_promotion = array()) {
        $menu_array = array();
        switch ($menu_type) {
            case 'show':
                $menu_array = array(
                array('menu_key' =>'bill','menu_name'=>'单据信息','menu_url' =>urlShop('erp_bill_l', 'show',array('bill_id'=>$_GET['bill_id']))),
                array('menu_key' =>'bill_goods', 'menu_name' =>'单据商品列表', 'menu_url' => urlShop('erp_bill_l','show_bill_goods',array('bill_id'=>$_GET['bill_id'],'menu_key'=>'bill_goods')))
                );
                break;
            case 'edit':
                $menu_array = array(
                    array('menu_key' =>'bill','menu_name'=>'编辑单据','menu_url' =>urlShop('erp_bill_l', 'edit')),
                    array('menu_key' =>'bill_goods', 'menu_name' =>'编辑单据商品', 'menu_url' => urlShop('erp_bill_l', 'edit',array('bill_id'=>$_GET['bill_id'],'menu_key'=>'bill_goods')))
                );
                break;
        }
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }
}
