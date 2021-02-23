<?php
/**
 * 进货单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_sControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('store_bill,store_goods_index');
    }

    /**
     * 新增进货单
     *
     */
    public function addOp() {
        $store_id = $_SESSION['store_id'];
        $supplier_model = Model('store_supplier');
        $warehouse_model = Model('erp_warehouse');
        $wholesale_model = Model('jxc_wholesale');
        $express_model = Model('express');
        $company_model = Model('company');//$_SESSION['store_company_id']
        $companylist = $company_model->getCompanyList(array('is_deleted'=>0, 'sd_company_id'=>$_SESSION['store_company_id']), 1000, '', ' `id`, `company_name`, `is_shengdai`,`sd_company_id` ');//属于省代公司
        $jxc_wholesale = array();
        if(!empty($companylist)){
            $companylist = array_column($companylist, 'id');
            //根据公司找出批发客户
            $jxc_wholesale = $wholesale_model->getJxcWholesaleList(array('wholesale_status'=>1, 'sign_company'=>array('in', $companylist)));
        }
        //$supplier_list = $supplier_model->getStoreSupplierList(array('sup_store_id'=>$store_id,'is_enabled'=>1));
        //$warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$store_id,'is_enabled'=>1));

        $expresslist = $express_model->getExpressList();
        //$companylist = $company_model->getCompanyList(array('is_deleted'=>0));
        //$from_company = array($_SESSION['store_company_id']=>$_SESSION['store_company_name']);
        $management_api = data_gateway('imanagement');
        $pifa_type = $management_api->get_dictlist(array('name'=>'warehouse.ptype'));
        $is_settled = $management_api->get_dictlist(array('name'=>'warehouse.dep_settlement_type'));
        Tpl::output('step',1);
        Tpl::output('out_warehouse_type',array(1=>'购买', 4=>'借货'));
        //Tpl::output('is_shengdai', $is_shengdai);
        //Tpl::output('sales_type', array('PF'=>'批发', 'WX'=>'维修'));
        Tpl::output('pifa_type',$pifa_type);
        //Tpl::output('from_company', $from_company);
        //Tpl::output('is_settled', $is_settled);
        Tpl::output('expresslist', $expresslist);
        //Tpl::output('companylist', $companylist);
        Tpl::output('jxc_wholesale', $jxc_wholesale);
        //Tpl::output('supplier_list',$supplier_list);
        //Tpl::output('warehouse_list',$warehouse_list);
        Tpl::showpage('erp_bill_s.add');
    }

    /**
     * 新增出货单 保存
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
            $result['msg'] = "请选择销售类型";
            exit(json_encode($result));
        }
        $sales_type = trim($_POST['sales_type']);

        if($sales_type == 'PF'){
            /**
             * 批发客户
             */
            if(empty($_POST['jxc_wholesale'])){
                $result['msg'] = "请选择批发客户";
                exit(json_encode($result));
            }else{
                $jxc_wholesale = trim($_POST['jxc_wholesale']);
                $wholesale_model = Model('jxc_wholesale');
                $wholesaleinfo = $wholesale_model->getJxcWholesaleList(array('wholesale_status'=>1, 'wholesale_id'=>$jxc_wholesale));
                $to_company_id = isset($wholesaleinfo[0]['sign_company'])?$wholesaleinfo[0]['sign_company']:'';

                /* 入库公司
                */
               if(empty($to_company_id)){
                   $result['msg'] = "批发客户未绑定签收公司！";
                   exit(json_encode($result));
               }
               
            }

            /**
             * 批发类型
             */
            if(empty($_POST['out_warehouse_type'])){
                $result['msg'] = "请选择出库类型";
                exit(json_encode($result));
            }else{
                $out_warehouse_type = trim($_POST['out_warehouse_type']);
            }

            /**
             * 类别 非必填
             */
            $pifa_type = trim($_POST['pifa_type']);
            /**
             * 批发价
             */
            /*if(empty($_POST['pifajia'])){
                $result['msg'] = "请填写批发价";
                exit(json_encode($result));
            }else{
                $pifajia = $_POST['pifajia'];
            }*/
            $pifajia = $_POST['pifajia'];
            /**
             * 管理费
             */
            if(empty($_POST['guanlifei'])){
                $result['msg'] = "请填写管理费";
                exit(json_encode($result));
            }else{
                $guanlifei = $_POST['guanlifei'];
            }
        } else{
            $to_company_id = "";
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
        /*if(empty($_POST['remark'])){
            $result['msg'] = "请填写备注";
            exit(json_encode($result));
        }else{*/
            $remark = $_POST['remark'];//备注
        //}*/
        
        $erp_bill_model = Model('erp_bill');
        $goods_model = Model('erp_goods');
        $bill_goods_list = array();
        /*$goods_itemid = str_replace(' ',',',$goods_itemid);
        $goods_itemid = str_replace('，',',',$goods_itemid);
        $goods_itemid = str_replace(array("\r\n", "\r", "\n"),',',$goods_itemid);
        $goods_arr = explode(",", $goods_itemid);
        $goods_arr = array_unique($goods_arr);
        */
        $goods_itemid_list = preg_replace("/\s+/is",",",$goods_itemid);
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        $goods_itemid_list = array_unique($goods_itemid_list);
        $chengben_total = 0;
        $goods_total = 0;
        foreach ($goods_itemid_list as $key => $goods_id) {
            $goods_list = $goods_model->getErpBillList(array('goods_id'=>$goods_id));
            $goods_list = isset($goods_list[0])?$goods_list[0]:array();
            $goods_info = array();
            $goods_info['goods_itemid'] = $goods_list['goods_id'];
            $goods_info['goods_sn'] = $goods_list['goods_sn'];
            $goods_info['goods_name'] = $goods_list['goods_name'];
            $goods_info['goods_count'] = 1;
            //$goods_info['remark'] = $goods_list['goods_id'];
            $goods_info['from_company_id'] = $_SESSION['store_company_id'];//出库公司默认当前公司
            $goods_info['from_store_id'] = $_SESSION['store_id'];
            //$goods_info['from_house_id'] = $goods_list['goods_id'];
            $goods_info['to_company_id'] = $to_company_id;
            $goods_info['to_house_id'] = "";//公司默认空，签收更改
            $goods_info['in_warehouse_type'] = $out_warehouse_type?$out_warehouse_type:$goods_list['put_in_type'];
            //$goods_info['is_settled'] = $goods_list['goods_id'];
            //$goods_info['settle_user'] = $goods_list['goods_id'];
            $goods_info['yuanshichengben'] = $goods_list['yuanshichengbenjia'];
            $goods_info['mingyichengben'] = $goods_list['mingyichengben'];
            $goods_info['jijiachengben'] = $goods_list['jijiachengben'];
            $goods_info['sale_price'] = 0;
            $goods_info['management_fee'] = 0;
            $goods_info['goods_data'] = json_encode($goods_list,JSON_UNESCAPED_UNICODE);
            if($sales_type == 'PF'){
                if($goods_list['is_on_sale']!=2){
                    $result['msg'] = "{$goods_list['goods_id']}货号不是库存状态";
                    exit(json_encode($result));
                }
                if(!isset($pifajia[$key]) || empty($pifajia[$key])){
                    $result['msg'] = "{$goods_list['goods_id']}请填写批发价";
                    exit(json_encode($result));
                }
                if(!isset($guanlifei[$key])){
                    $result['msg'] = "{$goods_list['goods_id']}请填写管理费";
                    exit(json_encode($result));
                }
                if(!is_numeric($guanlifei[$key])){
                    $result['msg'] = "{$goods_list['goods_id']}输入的管理费不是数值型";
                    exit(json_encode($result));
                }
                $goods_info['sale_price'] = bcadd($pifajia[$key], $guanlifei[$key], 2);
                $goods_info['management_fee'] = $guanlifei[$key];
                //$pifajia_total = bcadd($pifajia_total, $goods_list['sale_price'], 2);
                //$guanlifei_total = bcadd($guanlifei_total, $goods_list['management_fee'], 2);
                $chengben_total = bcadd($chengben_total, $goods_list['yuanshichengbenjia'], 2);
                //$sale_price = bcadd($goods_info['sale_price'], $goods_info['management_fee'], 2);
                $goods_total = bcadd($goods_total, $goods_info['sale_price'], 2);
            }else{
                if($goods_list['is_on_sale']!=3){
                    $result['msg'] = "货号{$goods_list['goods_id']}不是已销售状态";
                    exit(json_encode($result));
                }
            }
            $bill_goods_list[] = $goods_info;
        }
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>"S",
            'item_type'=>$sales_type,
            'bill_status'=>1,
            'from_company_id'=>$_SESSION['store_company_id'],
            'wholesale_id'=>$jxc_wholesale,
            'express_id'=>$_POST["express_id"],
            'express_sn' =>$_POST["express_sn"],
            'out_warehouse_type'=>$out_warehouse_type,
            'in_warehouse_type'=>$out_warehouse_type,
            'chengben_total'=>$chengben_total,
            'goods_total'=>$goods_total,
            'from_store_id'=>$_SESSION['store_id'],
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>$remark,
            'pifa_type'=>$pifa_type,
            'to_company_id'=>$to_company_id,
        );
         if($sales_type == 'PF'){
             $bill_info['supplier_id'] = $_SESSION['store_company_id'];
         }
        $res = $erp_bill_model->createBill($bill_info,$bill_goods_list,"S");
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
        $goods_ids = $goods_itemid == ""?$_POST['goods_ids']:$goods_itemid;
        $sales_type = $_POST['sales_type'];
        if ($sales_type == "") {
            $result['msg'] = "请选择退货类型!";
            echo json_encode($result);exit();
        }
        if (!in_array($sales_type, array('PF', 'WX'))) {
            $result['msg'] = "退货类型错误!";
            echo json_encode($result);exit();
        }
        if ($goods_ids == "") {
            $result['msg'] = "货号不能为空!";
            echo json_encode($result);exit();
        }
        /*
        $goods_ids = str_replace(' ',',',$goods_ids);
        $goods_ids = str_replace('，',',',$goods_ids);
        $goods_ids = str_replace(array("\r\n", "\r", "\n"),',',$goods_ids);
        $goods_arr = explode(",", $goods_ids);
        $goods_arr = array_unique($goods_arr);
        */
        $goods_itemid_list = preg_replace("/\s+/is",",",$goods_ids);
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        $goods_itemid_list = array_unique($goods_itemid_list);
        $error_goods = $error_house_lock = $error_goods_pf = $error_goods_wx = $error_weixiu_status = array();
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        $data = array();
        foreach ($goods_itemid_list as $key => $goods_id) {
            $goods_list = Model('erp_goods')->getErpBillList(array('goods_id'=>$goods_id));
            if(empty($goods_list)){
                $error_goods[] = $goods_id;
                continue;
            }
            if($sales_type == "PF" && $goods_list[0]['is_on_sale'] != 2){
                $error_goods_pf[] = $goods_id;
                continue;
            }
            if($sales_type == "WX" && $goods_list[0]['is_on_sale'] != 3){
                $error_goods_wx[] = $goods_id;
                continue;
            }
            if($sales_type == "PF" && !empty($goods_list[0]['order_sn'])){
                $result['msg'] = $goods_id."已经绑定了订单,请先解绑";
                echo json_encode($result);exit();
            }
            if($_SESSION['store_company_id'] != $goods_list[0]['company_id']){
                if($sales_type == "WX" && KELA_SPECIAL_BUSINESS){}else{
                    $result['msg'] = $goods_id."货号所在公司和当前制单人所在公司不一致!";
                    echo json_encode($result);exit();
                }
            }
            if($sales_type == "WX" && $goods_list[0]['weixiu_status'] !=3){
                $error_weixiu_status[] = $goods_id;
                continue;
            }
            
            $warehouse = Model("erp_warehouse")->getWareHouseInfo(array('house_id'=>$goods_list[0]['warehouse_id']));
            if(isset($warehouse) && $warehouse['lock']>0){
                $error_house_lock[] = $goods_id;
                continue;
            }
            
            if(!$show_chengben){
                $goods_list[0]['yuanshichengbenjia'] = '/';
            }
            $data[] = $goods_list[0];
        }

        if (!empty($error_goods)) {
            $result['msg'] = implode(",", $error_goods)."货号不正确，未查到货品!";
            echo json_encode($result);exit();
        }elseif(!empty($error_goods_pf) && $sales_type == "PF"){
            $result['msg'] = implode(",", $error_goods_pf)."货号不是库存状态，只有库存状态的货品才能做批发销售单!";
            echo json_encode($result);exit();
        }elseif(!empty($error_goods_wx) && $sales_type == "WX"){
            $result['msg'] = implode(",", $error_goods_wx)."货号不是已销售状态，只有已销售状态的货品才能做维修出货单!";
            echo json_encode($result);exit();
        }elseif(!empty($error_weixiu_status) && $sales_type == "WX"){
            $result['msg'] = implode(",", $error_weixiu_status)."货号维修状态为维修受理的商品才能制维修出货单!";
            echo json_encode($result);exit();
        }elseif(!empty($error_house_lock)){
            $result['msg'] = implode(",", $error_house_lock)."货号所在仓库正在盘点中!";
            echo json_encode($result);exit();
        }
        $result['success'] = 1;
        $result['data'] = $data;
        if($goods_itemid ==""){
            echo json_encode($result);
        }else{
            return $result;
        }
    }

    //获取批发客户信息
    public function getWholesaleOp($jxc_wholesale="")
    {
        $result = array('success'=>0, 'msg'=>'');
        $wholesale_id = $jxc_wholesale ==""?$_POST['jxc_wholesale']:$jxc_wholesale;
        if($wholesale_id){
            $res = Model("jxc_wholesale")->getJxcWholesaleInfo(array('wholesale_id'=>$wholesale_id, 'wholesale_status'=>1), " sign_required ");
            if(isset($res['sign_required']) && $res['sign_required'] == 1){
                if(!$jxc_wholesale){
                    echo json_encode($result);exit();
                }else{
                    return true;
                }
                
            }
        }
        if(!$jxc_wholesale){
           $result['success'] = 1;
            echo json_encode($result);exit(); 
        }else{
            return false;
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

