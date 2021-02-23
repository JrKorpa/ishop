<?php
/**
 * ERP单据查询
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_billControl extends BaseSellerControl {
    /**
     * 每次导出多少条记录
     * @var unknown
     */
    const EXPORT_SIZE = 1000;
    private $_bill_info;
    //public $settle = array('待结算', '已结算', '已退货');
    public $item_type = array('LS'=>'零售', 'PF'=>'批发', 'WX'=>'维修', 'ZC'=>'转仓');
    public $warehouse_type = array('无', '购买', '委托加工', '代销', '借入');

    public function __construct() {
        parent::__construct() ;
        Language::read('store_bill,store_goods_index');
        
    }

    /**
     * 单据查询
     *
     */
    public function indexOp() {
        $erp_bill_model = new erp_billModel();//Model('erp_bill');
        //$where = $_REQUEST;
        $condition = $this->convertParams($_REQUEST);
        $bill_list = $erp_bill_model->searchErpBillList($condition,'*',10,'bill_id desc');
        Tpl::output('show_page',$erp_bill_model->showpage());
        $bill_list = $erp_bill_model->formatErpBillList($bill_list);

        $company_model = Model('company');
        $companyInfo = $company_model->getCompanyInfo(array('id'=>$_SESSION['store_company_id']));
        $is_shengdai = false;
        if($companyInfo['is_shengdai'] == 1){
            $is_shengdai = true;
        }
        Tpl::output('is_shengdai', $is_shengdai);

        $bill_type_list = $erp_bill_model->getBillTypeList();
        $bill_status_list = $erp_bill_model->getBillStatusList();

        $erp_warehouse_model =Model('erp_warehouse');
        $goods_items_model = Model('goods_items');
        $house_list = $erp_warehouse_model->getWareHouseList(array('store_id' =>$_SESSION['store_id']), 'house_id,name', 10000);
        $company_list = $goods_items_model->getCompanyList("id,company_name");
        //$prc_list=$goods_items_model->getSupplierList("sup_id,sup_name");
        $wholesale_list=$goods_items_model->getWholesaleList("wholesale_id,wholesale_name",array('sd_company_id'=>$_SESSION['store_company_id']));
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        Tpl::output('show_chengben', $show_chengben);
        Tpl::output('house_list', $house_list);
        Tpl::output('company_list', $company_list);
        //Tpl::output('prc_list', $prc_list);
        Tpl::output('wholesale_list', $wholesale_list);
        Tpl::output('item_type', $this->item_type);
        
        Tpl::output('bill_type_list',$bill_type_list);
        Tpl::output('bill_status_list',$bill_status_list);
        Tpl::output('bill_list',$bill_list);
        Tpl::output('store_company_id', $_SESSION['store_company_id']);

        self::profile_menu('list',$_GET['bill_type']);
        Tpl::showpage('erp_bill.index');
    }
    public function  convertParams($where=array()){
        $condition = array();
        //单据ID
        if(isset($where['ids']) && !empty($where['ids'])){
            $condition['bill_id'] = array('in',explode(",",$where['ids']));
        }
        //单据创建时间
        if(!empty($where['create_time_begin'])){
            $condition['create_time'][] = array('egt',$where['create_time_begin']);
        }
        if(!empty($where['create_time_end'])){
            $condition['create_time'][] = array('elt',$where['create_time_end'].' 23:59:59');
        }
        //单据类型
        if(!empty($where['bill_type'])){
            $bill_type = $where['bill_type'];
            if($bill_type == "L"){
                //进货单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='L' or (erp_bill.bill_type='S' and erp_bill.item_type='PF' and erp_bill.to_company_id={$_SESSION['store_company_id']})");
            }else if($bill_type == "M"){
                //调拨单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='M' and erp_bill.item_type='ZC'");
            }else if($bill_type == "MW"){
                //维修调拨单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='M' and erp_bill.item_type='WX'");
            }else if($bill_type == "S"){
                //销售出库单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='S' and erp_bill.item_type in('PF','LS') and erp_bill.from_company_id={$_SESSION['store_company_id']}");
            }else if($bill_type == "SW"){
                //维修出库单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='S' and erp_bill.item_type='WX'");
            }else if($bill_type == "D"){
                //销售退货单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='D' and (erp_bill.item_type='LS' OR (erp_bill.item_type='PF' and erp_bill.to_company_id={$_SESSION['store_company_id']}))");
            }else if($bill_type == "DW"){
                //维修入库单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='D' and erp_bill.item_type='WX'");
            }else if($bill_type == "B"){
                //退货返厂单
                $condition['bill_type'] = array("exp","erp_bill.bill_type='B' or (erp_bill.bill_type='D' and erp_bill.item_type='PF' and erp_bill.from_company_id={$_SESSION['store_company_id']})");
            }else{
                $condition['bill_type'] = $where['bill_type'];
            }
        }
        //单据状态
        if(!empty($where['bill_status'])){
            $condition['bill_status'] = $where['bill_status'];
        }
        //批发类型
        if(!empty($where['out_warehouse_type'])){
            $condition['out_warehouse_type'] = $where['out_warehouse_type'];
        }
        //单据编号
        if(!empty($where['bill_no'])){
            $condition['bill_no'] = $where['bill_no'];
        }
        //审核时间
        if(!empty($where['check_time_begin'])){
            $condition['check_time'][] = array('egt',$where['check_time_begin']);
        }
        if(!empty($where['check_time_end'])){
            $condition['check_time'][] = array('elt',$where['check_time_end'].' 23:59:59');
        }
        //是否打印
        if(isset($where['is_print']) && $where['is_print']!=''){
            $condition['is_print'] = $where['is_print'];
        }
        //单据打印时间
        if(!empty($where['print_time_begin'])){
            $condition['print_time'][] = array('egt',$where['print_time_begin']);
        }
        if(!empty($where['print_time_end'])){
            $condition['print_time'][] = array('elt',$where['print_time_end'].' 23:59:59');
        }
        //入库公司
        if(!empty($where['to_company_id'])){
            $condition['to_company_id'] = $where['to_company_id'];
        }
        //入库仓库
        if(!empty($where['to_house_id'])){
            $condition['to_house_id'] = $where['to_house_id'];
        }
        //单据创建人
        if(!empty($where['create_user'])){
            $condition['create_user'] = $where['create_user'];
        }
        //订单号
        if(!empty($where['order_sn'])){
            $condition['sql']=array("exp"," ((erp_bill.from_store_id={$_SESSION['store_id']}) or (erp_bill.bill_type='S' and erp_bill.item_type='PF' and erp_bill.to_store_id={$_SESSION['store_id']}) or (erp_bill.bill_type='M' and erp_bill.item_type in ('ZC','WX') and erp_bill.to_store_id={$_SESSION['store_id']}) or (erp_bill.bill_type='D' and erp_bill.item_type='PF' and erp_bill.to_store_id={$_SESSION['store_id']})) ");

            $condition['order_sn'] = $where['order_sn'];
        }
        //货号
        if(!empty($where['goods_id'])){
            $condition['goods_id']=array("exp"," erp_bill.bill_no in (select distinct(bill_no) from erp_bill_goods where goods_itemid='{$where['goods_id']}') ");
        }
        //供应商
        if(!empty($where['supplier_id'])){
            $condition['supplier_id'] = $where['supplier_id'];
        }
        //批发客户
        if(!empty($where['wholesale_id'])){
            $condition['wholesale_id'] = $where['wholesale_id'];
        }
        //款号
        if(!empty($where['goods_sn'])){
            $condition['goods_id']=array("exp"," erp_bill.bill_no in (select distinct(bill_no) from erp_bill_goods where goods_sn='{$where['goods_sn']}') ");
        }
        //结算时间
        if(!empty($where['settle_time_begin'])){
            $condition['settle_time'][] = array('egt',$where['settle_time_begin']);
        }
        if(!empty($where['settle_time_end'])){
            $condition['settle_time'][] = array('elt',$where['settle_time_end'].' 23:59:59');
        }
        //批发类型
        if(!empty($where['pifa_type'])){
            $condition['pifa_type'] = $where['pifa_type'];
        }
        //批发类型-类别
        if(!empty($where['pifa_sub_type'])){
            $condition['pifa_sub_type'] = $where['pifa_sub_type'];
        }
        //门店结算类型
        if(isset($where['is_settle']) && in_array($where['is_settle'], ['0', '1'])){
            $condition['is_settled'] = $where['is_settle'];
        }
        //备注
        if(!empty($where['remark'])){
            $condition['remark'] = array('like',"%{$where['remark']}%");
        }
        //销售类型
        if(!empty($where['sales_type'])){
            $condition['item_type'] = $where['sales_type'];
            //$condition['bill_type'] = "S";
        }
        //调拨类型
        if(!empty($where['allot_type'])){
            $condition['item_type'] = $where['allot_type'];
            // $condition['bill_type'] = "M";
        }
        //制单人
        if(!empty($where['create_user'])){
            $condition['create_user'] = array('like',"%{$where['create_user']}%");
        }
        //审核人
        if(!empty($where['check_user'])) {
            $condition['check_user'] = array('like', "%{$where['check_user']}%");
        }
        $condition['sql']=array("exp"," (erp_bill.from_company_id={$_SESSION['store_company_id']} or (erp_bill.to_company_id={$_SESSION['store_company_id']} and ((erp_bill.bill_type='L') or (erp_bill.bill_type='M' and erp_bill.item_type in ('WX')) or (erp_bill.bill_type='D' and erp_bill.item_type='PF' and erp_bill.item_status=1) or erp_bill.bill_status in (2, 4))) ) ");
        //$condition['sql']=array("exp"," from_company_id={$_SESSION['store_company_id']} or to_company_id={$_SESSION['store_company_id']}");
        return $condition;
    }
    public function showOp(){
        $bill_id = (int)$_GET['bill_id'];
        $bill_type = $_GET['bill_type'];
        $menu_key = "bill";
        
        $bill_model	= Model('erp_bill');
        
        $bill_info = $bill_model->getErpBillInfo(array('bill_id'=>$bill_id));
        $bill_info = $bill_model->formatErpBillList($bill_info,'one');
        if(empty($bill_info)){
            showMessage("单据不存在！",'','html','error');
        }
        $bill_status = $bill_info['bill_status'];
        $item_status = $bill_info['item_status'];
        $bill_type = $bill_info['bill_type'];
        $item_type = $bill_info['item_type'];
        $from_company_id = $bill_info['from_company_id'];
        $to_company_id = $bill_info['to_company_id'];
        $bill_type_name = billType($bill_info);
        
        //审核按钮显示判断
        $confirm_check = $bill_status==1 ? true:false;
        if($bill_status==1 && $bill_type=="D" && $item_type=="PF"){
            if($from_company_id == $_SESSION['store_company_id'] &&  empty($item_status)){
                $confirm_check = true;
            }else if($to_company_id == $_SESSION['store_company_id'] &&  $item_status==1){
                $confirm_check = true;
            }else{
                $confirm_check = false;
            }
        }
        if(!$this->check_seller_limit("limit_pandian_checked") && $bill_type=='W'){
            $confirm_check = false;
        }
        //签收按钮
        $sign_check = false;
        if(in_array($bill_status,array(2)) && $to_company_id == $_SESSION['store_company_id']){
            if(in_array($bill_type,array("S", "D")) && $item_type == "PF"){
                $sign_check = true;
            }else if($bill_type=="M" && $item_type=="WX"){
                $sign_check = true;
            }                
        }
        //结算
        $settle_check = false;
        if($bill_status==4 && empty($bill_info['is_settled']) && $to_company_id == $_SESSION['store_company_id']){
            if(in_array($bill_type,array("D")) && $item_type == "PF"){
                $settle_check = true;
            }
        }
        //var_dump($bill_info['to_company_id'],$_SESSION['store_company_id']);die;
        //Tpl::output('now_company_id', $_SESSION["store_company_id"]);
        Tpl::output('bill_type',$bill_type);
        Tpl::output('bill_info',$bill_info);
        Tpl::output('confirm_check', $confirm_check);
        Tpl::output('sign_check', $sign_check);
        Tpl::output('settle_check', $settle_check);
        
        Tpl::output('settle',paramsHelper::getParams('settle_type'));
        Tpl::output('item_type', $this->item_type);
        $this->profile_menu('show', $menu_key,$bill_type_name);
        
        if($bill_type == "W" && $bill_status==1 && $item_status==0 && $this->check_seller_limit("erp_bill_w")){
            //盘点操作页面
            Tpl::showpage('erp_bill_w.pandian');
        }else{
            Tpl::showpage('erp_bill.show');
        }
    }
    
    public function show_bill_goodsOp(){
        
        $bill_id = (int)$_GET['bill_id'];
        $menu_key = "bill_goods";
        $bill_model	= new erp_billModel();//Model('erp_bill');
        $bill_info = $bill_model->getErpBillInfo(array('bill_id'=>$bill_id));//,'from_store_id'=>$_SESSION['store_id']
        if(empty($bill_info)){
            showMessage("无效单据！",'','html','error');
        }
        //$bill_info = $bill_model->formatErpBillList($bill_info,'one');
        $bill_status = $bill_info['bill_status'];
        $item_status = $bill_info['item_status'];
        $bill_type = $bill_info['bill_type'];
        $item_type = $bill_info['item_type'];
        $from_company_id = $bill_info['from_company_id'];
        $to_company_id = $bill_info['to_company_id'];
        $bill_type_name = billType($bill_info);
        $bill_goods_model	= new erp_bill_goodsModel();
        
        $where = array('erp_bill_goods.bill_id'=>$bill_id);
        if($bill_type=='W'){
             $_GET['pandian_status'] = (int)$_GET['pandian_status'];
             $where['erp_bill_goods.bill_type'] = 'W';
             if($_GET['pandian_status']==1 || $_GET['pandian_status']==2){                 
                 $where['pandian_status'] = $_GET['pandian_status'];
             }else if($_GET['pandian_status']==3){
                 $where['pandian_status'] = array('exp','pandian_status=3 and pandian_adjust>0');
             }else{
                 $where['pandian_status'] = array('exp','pandian_status<>3 and (pandian_adjust=0 or pandian_adjust is null)');
             }
        }
        $bill_goods_list = $bill_goods_model->searchBillGoodsList($where,10);
        Tpl::output('show_page',$bill_goods_model->showpage());
        
        $bill_goods_list = $bill_goods_model->formatErpBillGoodsList($bill_goods_list,'list');

        $company_model = Model('company');
        $companylist = $company_model->getCompanyInfo(array('id'=>$_SESSION['store_company_id']));
        $is_shengdai = false;
        if($companylist['is_shengdai'] == 1){
            $is_shengdai = true;
        }
        
        //审核按钮显示判断
        $confirm_check = $bill_status==1 ? true:false;
        if($bill_status==1 && $bill_type=="D" && $item_type=="PF"){
            if($from_company_id == $_SESSION['store_company_id'] &&  empty($item_status)){
                $confirm_check = true;//确认提交
            }else if($to_company_id == $_SESSION['store_company_id'] &&  $item_status==1){
                $confirm_check = true;//省代确认
            }else{
                $confirm_check = false;//省代确认
            }
        }
        if(!$this->check_seller_limit("limit_pandian_checked")){
            $confirm_check = false;
        }
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        Tpl::output('show_chengben', $show_chengben);
        Tpl::output('is_shengdai',$is_shengdai);
        Tpl::output('bill_type',$bill_type);
        Tpl::output('item_type',$item_type);
        Tpl::output('bill_goods_list',$bill_goods_list);
        Tpl::output('settle', paramsHelper::getParams('bill_goods_settle_type'));
        Tpl::output('bill_info',$bill_info);
        Tpl::output('confirm_check',$confirm_check);
        $this->profile_menu('show', $menu_key,$bill_type_name);
        if($bill_type =='W'){ 
            $bill_goods_tj = $bill_model->getPandianGoodsTj($bill_id); 
            $bill_info = array_merge($bill_goods_tj,$bill_info);            
            Tpl::output('bill_info',$bill_info);                        
            Tpl::showpage('erp_bill_w.show_bill_goods');
        }else{
            Tpl::showpage('erp_bill.show_bill_goods');
        }
    }
      
    /**
     * 盘点商品操作
     */
    public function pandian_goodsOp(){
        $bill_id = (int)$_POST['bill_id'];
        $goods_id = trim($_POST['goods_id']);
        if(!$bill_id || !$goods_id){
            $result = callback(false,"参数错误");
            exit(json_encode($result));
        }
        $billModel = new erp_billModel();
        $goodsModel = new goods_itemsModel();
        $billInfo = $billModel->getErpBillInfo(array('bill_id'=>$bill_id,'bill_type'=>'W','item_status'=>0));
        if(empty($billInfo)){
            $result = callback(false,"盘点已结束！");
            exit(json_encode($result));
        }
        $goodsInfo = $goodsModel->getGoodsItemInfo(array('goods_id'=>$goods_id));
        if(empty($goodsInfo)){
            $result = callback(false,"{$goods_id} 货号不存在");
            exit(json_encode($result));
        }
                
        $billGoodsModel = new erp_bill_goodsModel();
        $billGoodsInfo = $billGoodsModel->getErpBillGoodsInfo(array('bill_id'=>$bill_id,'goods_itemid'=>$goods_id));
        if(!empty($billGoodsInfo)){
            //已盘点
            if($billGoodsInfo['pandian_time']!=''){
                $pandianTotal = $billModel->getPandianGoodsTotal($bill_id);
                $result = callback(true,"{$goods_id} 已盘点",$pandianTotal);
                exit(json_encode($result));
            }else{
                $billWInfo = $billModel->getErpBillWInfo(array('bill_id'=>$bill_id,'warehouse_id'=>$goodsInfo['warehouse_id']));
                $pandian_status = !empty($billWInfo)?3:1;//3正常 1 盘亏
                $billGoodsData = array(
                    'pandian_status'=>$pandian_status,
                    'pandian_user'=>$_SESSION['seller_name'],
                    'pandian_time'=>date("Y-m-d H:i:s")
                );
                $res = $billGoodsModel->editErpBillGoods(array('id'=>$billGoodsInfo['id']),$billGoodsData);                
            }
        }else{
            //插入盘点明细 盘亏
            $billGoodsData = array(
                'bill_id'=>$bill_id,
                'bill_no'=>$billInfo['bill_no'],
                'bill_type'=>$billInfo['bill_type'],
                'goods_itemid'=>$goods_id,
                'goods_sn'=>$goodsInfo['goods_sn'],
                'goods_name'=>$goodsInfo['goods_name'],
                'from_house_id'=>$billInfo['from_house_id'],
                'yuanshichengben'=>$goodsInfo['yuanshichengben'],
                'mingyichengben'=>$goodsInfo['mingyichengben'],
                'jijiachengben'=>$goodsInfo['jijiachengben'],
                'management_fee'=>$goodsInfo['management_fee'],
                'goods_count'=>1,
                'goods_data'=>json_encode($goodsInfo,JSON_UNESCAPED_UNICODE),
                'pandian_status'=>2,//盘盈
                'pandian_user'=>$_SESSION['seller_name'],
                'pandian_time'=>date("Y-m-d H:i:s")
            );
            $res = $billGoodsModel->addBillGoods($billGoodsData);
        }        
        if($res !==false){
            $pandianTotal = $billModel->getPandianGoodsTotal($bill_id);
            $result = callback(true,"{$goods_id} 盘点成功",$pandianTotal);
        }else{
            $result = callback(false,"{$goods_id} 盘点失败");
        }
        exit(json_encode($result));
    }
    /**
     * 盘点结束 操作
     */
    public function pandian_finishedOp(){
        $bill_id = (int)$_GET['bill_id'];      
        $billModel = new erp_billModel();
        $bill_info = $billModel->getErpBillInfo(array('bill_id'=>$bill_id,'bill_type'=>'W'));
        if(empty($bill_info)){
            $result = callback(false,"盘点单不存在");
            echo json_encode($result);
        }else if($bill_info['item_status']==1){
            $result = callback(false,"已结束盘点");
            echo json_encode($result);
        }
        try{            
            $billModel->beginTransaction();
            $res = $billModel->pandianAdjust($bill_id,false);
            if($res['state'] == false){
                throw new Exception($res['msg']);
            }
            $res = $billModel->updateBill(array('item_status'=>1),array("bill_id"=>$bill_id,'bill_type'=>'W'));
            if(!$res){
                throw new Exception("操作失败");
            }
            //仓库解锁
            $sql = "update erp_warehouse w inner join erp_bill_w bw on w.house_id=bw.warehouse_id set w.`lock`=0 where bw.bill_id={$bill_id}";
            $res = DB::query($sql);
            if(!$res){
                throw new Exception("仓库解锁失败！");
            }
            $bill_log_model = new erp_bill_logModel();    
            $bill_log_model->createBillLog($bill_id,"盘点结束");
            $billModel->commit();
            
            $result = callback(true,"盘点结束");
            echo json_encode($result);
        }catch (Exception $e){
            $billModel->rollback();
            $result = callback(false,$e->getMessage());
            echo json_encode($result);
        }
    }
    /**
     * 盘点商品刷新矫正 操作
     */
    public function pandian_adjustOp(){        
        $bill_id = (int)$_GET['bill_id'];
        $billModel = new erp_billModel();
        $bill_info = $billModel->getErpBillInfo(array('bill_id'=>$bill_id,'bill_type'=>'W'));
        if(empty($bill_info)){
            $result = callback(false,"盘点单不存在");
            exit(json_encode($result));
        }else if($bill_info['bill_status']!=1){
            $result = callback(false,"盘点单已审核，不能刷新货品！");
            exit(json_encode($result));
        }else if($bill_info['item_status']!=1){
            $result = callback(false,"盘点单还未结束盘点，不能刷新货品！");
            exit(json_encode($result));
        }

        $res = $billModel->pandianAdjust($bill_id);
        if($res['state'] == true){
            $bill_log_model = new erp_bill_logModel();
            $bill_log_model->createBillLog($bill_id,"刷新盘点单");
            $result = callback(true,"操作成功");
        }else{
            $result = callback(false,"操作失败");
        }
        echo json_encode($result);
    }
    /**
     * 单据明细下载
     */
    public function down_bill_goodsOp(){
        
        $bill_id = (int)$_GET['bill_id'];
        $bill_model	= new erp_billModel();
        $bill_info = $bill_model->getErpBillInfo(array('bill_id'=>$bill_id));
        if(empty($bill_info)){
            exit("无效单据");
        }  
              
        $bill_type = $bill_info['bill_type'];
        $bill_type_name = billType($bill_info);
        $bill_goods_model	= new erp_bill_goodsModel();
        $bill_goods_list = $bill_goods_model->searchBillGoodsList(array('bill_id'=>$bill_id),9999);
        $bill_goods_list = $bill_goods_model->formatErpBillGoodsList($bill_goods_list,'list');
        
        if($bill_type=='W'){            
            $statheader = array(
                array('key'=>'goods_itemid','text'=>'货号'),
                array('key'=>'goods_sn','text'=>'款号'),
                array('key'=>'goods_name','text'=>'商品名称'),
                array('key'=>'goods_count','text'=>'数量'),
                array('key'=>'pandian_status_name','text'=>'盘点状态'),
                array('key'=>'pandian_adjust_name','text'=>'调整状态'),
                array('key'=>'from_house_name','text'=>'盘点仓库'),
                array('key'=>'warehouse','text'=>'所属仓库'),
                array('key'=>'caizhi','text'=>'材质'),
                array('key'=>'jinse','text'=>'颜色'),
                array('key'=>'zhengshuleibie','text'=>'证书类别'),
                array('key'=>'zhengshuhao','text'=>'证书号'),
                array('key'=>'zuanshidaxiao','text'=>'钻石大小'),
                array('key'=>'zhushilishu','text'=>'主石粒数'),
                array('key'=>'shoucun','text'=>'指圈'),
                array('key'=>'jinzhong','text'=>'金重'),
                array('key'=>'pandian_user','text'=>'盘点人'),
                array('key'=>'pandian_time','text'=>'盘点时间'),
            );         
            
        }
        
        
        if(!empty($statheader)){
            $excel_data = array();
            //header
            foreach ($statheader as $k=>$v){
                $excel_data[0][] = array('styleid'=>'s_title','data'=>$v['text']);
            }
            //data
            foreach ($bill_goods_list as $k=>$v){
                foreach ($statheader as $h_k=>$h_v){
                    $excel_data[$k+1][] = array('data'=>$v[$h_v['key']]);
                }
            }
            
            import('libraries.excel');
            $excel_obj = new Excel();
            //设置样式
            $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));

            $excel_data = $excel_obj->charset($excel_data,CHARSET);
            $excel_obj->addArray($excel_data);
            $excel_obj->addWorksheet($excel_obj->charset($bill_type_name."明细记录",CHARSET));
            $excel_obj->generateXML($excel_obj->charset($bill_type_name."明细记录",CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        
    }
    /**
     * 单据日志
     */
    public function show_bill_logOp(){
    
        $bill_id = (int)$_GET['bill_id'];            
        $menu_key = "bill_log";    
    
        $bill_model	= Model('erp_bill');
        $bill_info = $bill_model->getErpBillInfo(array('bill_id'=>$bill_id));//,'from_store_id'=>$_SESSION['store_id']
        if(empty($bill_info)){
            showMessage("无效单据！",'','html','error');
        }
        $bill_type = $bill_info['bill_type'];
        $bill_type_name = billType($bill_info);
        $bill_log_model	=  Model('erp_bill_log');
        $bill_log_list = $bill_log_model->getErpBillLogList(array('bill_id'=>$bill_id),'*',10);
        $bill_log_list = $bill_log_model->formatErpBillLogList($bill_log_list,'list');
    
        Tpl::output('bill_info',$bill_info);
        Tpl::output('bill_log_list',$bill_log_list);
        Tpl::output('show_page',$bill_log_model->showpage());
        $this->profile_menu('show', $menu_key,$bill_type_name);
        Tpl::showpage('erp_bill.show_bill_log');
    }
    /**
     * 签收
     */
    public function sign_for_billOp(){
        $bill_id = isset($_GET['bill_id'])?(int)$_GET['bill_id']:0;
        $bill_model = Model('erp_bill');
        $bill_info = $bill_model->getErpBillInfo(array('bill_id'=>$bill_id));
        if(empty($bill_info)){
            exit("单据不存在！");
        }
        $warehouse_list = Model('erp_warehouse')->getWareHouseList(array('company_id'=>$bill_info['to_company_id']));
        if(empty($warehouse_list)){
            exit("入库公司未设置仓库！");
        }
        Tpl::output('bill_info',$bill_info);
        Tpl::output('warehouse_list', $warehouse_list);
        Tpl::showpage('erp_bill.sign_for_bill','null_layout');
    }
    /**
     * 签收保存
     */
    public function check_sign_saveOp(){
    
        $result = array("success"=>0,'msg'=>'');
        if(empty($_POST['bill_id'])){
            $result['msg'] = "bill_id 参数为空！";
            exit(json_encode($result));
        }
        if(empty(trim($_POST['warehouse_id']))){
            $result['msg'] = "请选择签入仓库！";
            exit(json_encode($result));
        }
        $bill_id = (int)$_POST['bill_id'];
        $warehouse_id = trim($_POST['warehouse_id']);
        $bill_model = new erp_billModel();//Model('erp_bill');
        $warehouse_model = new erp_warehouseModel();
        
        $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$warehouse_id));
        if(isset($warehouse) && $warehouse['lock']>0){
            $result['msg'] = "签收仓库正在盘点中！";
            exit(json_encode($result));
        }        
        
        $res = $bill_model->checkSignBill($bill_id,$warehouse_id);
        if($res['success']==1){
            $result['success'] = 1;
        }else{
            $result['success'] = 0;
            $result['msg'] = "签收失败：".$res['msg'];
        }
        exit(json_encode($result));
    }
    /**
     * 单据审核
     */
    public function check_billOp(){
        $bill_id = isset($_GET['bill_id'])?(int)$_GET['bill_id']:0;
        $bill_model	= Model('erp_bill');
        $bill_info = $bill_model->getErpBillInfo(array('bill_id'=>$bill_id));
        if(empty($bill_info)){
            exit("单据不存在！");
        }
        Tpl::output('bill_info',$bill_info);
        Tpl::showpage('erp_bill.check_bill','null_layout');
    }
    /**
     * 单据审核保存
     */
    public function check_bill_saveOp(){
              
        $result = array("success"=>0,'msg'=>'');
        if(empty($_POST['bill_id'])){
            $result['msg'] = "bill_id 参数为空！";
            exit(json_encode($result));
        }
        if(empty(trim($_POST['check_remark']))){
            $result['msg'] = "请填写审核备注！";
            exit(json_encode($result));
        }
        $bill_id = (int)$_POST['bill_id'];
        $check_remark = trim($_POST['check_remark']);
        if(empty($_POST['check_status'])){
            $result['msg'] = "请选择审核是否通过！";
            exit(json_encode($result));
        }
        $check_status = $_POST['check_status'];        
        $bill_model	= new erp_billModel();//Model('erp_bill');
        $res = $bill_model->checkBill($bill_id,$check_status,$check_remark);
        if($res['success']==1){
            if(isset($res['tip'])) {
                $result['tip'] = 1;
            }else{
                $result['tip'] = 0;
            }
            $result['success'] = 1;
        }else{
            $result['success'] = 0;
            $result['msg'] = $res['msg'];
        }
        exit(json_encode($result));
    }
    /**
     * 结算
     */
    public function settle_billOp(){
        $bill_id = isset($_GET['bill_id'])?(int)$_GET['bill_id']:0;
        $bill_model = Model('erp_bill');
        $bill_info = $bill_model->getErpBillInfo(array('bill_id'=>$bill_id));
        if(empty($bill_info)){
            exit("单据不存在！");
        }
        Tpl::output('bill_info',$bill_info);
        Tpl::showpage('erp_bill.settle_bill','null_layout');
    }
    /**
     * 结算保存
     */
    public function settle_bill_saveOp(){
        $result = array("success"=>0,'msg'=>'');
        if(empty($_POST['bill_id'])){
            $result['msg'] = "bill_id 参数为空！";
            exit(json_encode($result));
        }
        if(empty(trim($_POST['remark']))){
            $result['msg'] = "请填写审核备注！";
            exit(json_encode($result));
        }
        $bill_id = (int)$_POST['bill_id'];
        $remark = trim($_POST['remark']);
             
        $bill_model	= new erp_billModel();
        $res = $bill_model->settleBill($bill_id,$remark);
        if($res['success']==1){
            $result['success'] = 1;
        }else{
            $result['success'] = 0;
            $result['msg'] = $res['msg'];
        }
        exit(json_encode($result));
    }
    

    /**
     * 门店批发销售结算
     */
    public function store_checkoutOp(){
        $bill_model=Model('erp_bill');
        if(chksubmit()){
            try{
                $bill_no=$_POST['bill_no'];
                $process_type=$_POST['process_type'];
                if(empty($_POST['bill_no'])) throw new Exception("批发销售单号不能为空！");
                if(empty($_POST['goods_itemid'])) throw new Exception("货号不能为空！");
                $bill_info=$bill_model->getErpBillInfo(array('bill_no'=>$bill_no));
                if(empty($bill_info)) throw new Exception("门店批发销售单{$bill_no}不存在！");
                if($bill_info["bill_type"]!='S'||$bill_info["item_type"]!='PF') throw new Exception("订单号{$bill_no}非批发销售单！");

                $erp_bill_goods_model =Model('erp_bill_goods');
                $goods_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
                $goods_list = explode(',',$goods_list);
                $goods_list = array_filter($goods_list);
                $total_num = 0;
                $pass_goods_list = array();
                $bill_model->beginTransaction();
                foreach ($goods_list as $goods_itemid) {
                    if (in_array($goods_itemid, $pass_goods_list)) continue;
                    $pass_goods_list[] = $goods_itemid;
                    $total_num++;
                    if ($total_num > 10000)  throw new Exception("文件记录行不能大于10000！");
                    $erp_bill_goods_info= $erp_bill_goods_model->getErpBillGoodsInfo(array('bill_no' => $bill_no,'goods_itemid'=>$goods_itemid));
                    if (empty($erp_bill_goods_info)) throw new Exception("订单{$bill_no}中货号{$goods_itemid}不存在");
                    if ($erp_bill_goods_info["is_settled"]>0) throw new Exception("货号{$goods_itemid}已结算");

                    $erp_bill_goods_data=array();
                    $erp_bill_goods_data["settle_user"]=$_SESSION["seller_name"];
                    $erp_bill_goods_data["settle_time"]=date('Y-m-d H:i:s');
                    //JS：结算，TH:退货
                    if($process_type=="JS"){
                        $erp_bill_goods_data["is_settled"]=0;
                        $erp_bill_goods_model->editErpBillGoods(array('bill_no' => $bill_no,'goods_itemid'=>$goods_itemid),$erp_bill_goods_data);
                    }else if($process_type=="TH"){
                        //验证下此货号是否有【已审核】的【批发类型的销售退货单】
                        $condition["sql"]=array("exp"," bill_no in (select distinct(bill_no) from erp_bill_goods where goods_itemid='{$goods_itemid}' and bill_no like 'D%')");
                        $bill_info=$bill_model->getErpBillInfo($condition);
                        if(empty($bill_info)||$bill_info["bill_status"]!=2) throw new Exception("货号{$goods_itemid}没有【已审核】的销售退货单");
                        $erp_bill_goods_data["is_settled"]=1;
                        $erp_bill_goods_model->editErpBillGoods(array('bill_no' => $bill_no,'goods_itemid'=>$goods_itemid),$erp_bill_goods_data);
                    }
                }
                //改变单据的结算状态
                $un_settle_list=$erp_bill_goods_model->getErpBillGoodsList(array('is_settled'=>0,'bill_no'=>$bill_no));
                if(count($un_settle_list)==0){
                    $bill_data=array();
                    $bill_data["settle_time"]=date('Y-m-d H:i:s');
                    $bill_data["is_settled"]=1;
                    $bill_data["settle_user"]=$_SESSION["seller_name"];
                    $bill_model->editErpBill(array('bill_no'=>$bill_no),$bill_data);
                }
                $bill_model->commit();
                $result['success'] = 1;
                $result['data'] = 1;
                exit(json_encode($result));
            }catch (Exception $ex){
                $result = callback(false,$ex->getMessage());
                $bill_model->rollback();
                exit(json_encode($result));
            }
        }
        Tpl::showpage('erp_bill_check_out');
    }


    /**
     * 打印单据货品日志
     */
    public function  print_bill_logOp(){
                
        if(empty($_POST['bill_ids']) || !isset($_POST['print_type'])){
            $result = callback(false,"参数错误");
            exit(json_encode($result));
        }
        
        $bill_flag = false;
        if($_POST['print_type']=="print_bill_goods"){
            $print_remark = "打印货品标签";
            $bill_flag = false;
        }else{
            $result = callback(false,"未知打印类型");
            exit(json_encode($result));
        }
        
        $bill_id_list = explode(',',$_POST['bill_ids']);//单据ID列表
        $bill_type = $_POST['bill_type'];

        $bill_model = new erp_billModel();       
        $bill_log_model = new erp_bill_logModel();
              
        $bill_model->beginTransaction();        
        foreach ($bill_id_list as $bill_id){
            if($bill_flag == true){
                $update = array(
                    'is_print'=>1,
                    'print_user'=>$_SESSION['seller_name'],
                    'print_time'=>date('Y-m-d H:i:s')                
                );
                $res = $bill_model->updateBill($update, array('bill_id'=>$bill_id));
                if($res!==false){                
                    $bill_log_model->createBillLog($bill_id,$print_remark);
                }  
            } else{
                $bill_log_model->createBillLog($bill_id,$print_remark);
            }
        }
        $bill_model->commit();
        $result = callback(true,"日志写入成功");
        exit(json_encode($result));
    }
    /**
     * 打印批发销售单 form表单页
     */
    public function print_bill_goods_formOp(){

        $bill_ids = $_GET['_ids'];
        if($bill_ids){
            //$bill_ids = array_filter(explode(",", $bill_ids));
        }
        $bill_model = new erp_billModel();
        
        $billWhere = array();
        $billWhere['bill_id'] = array('in',$bill_ids);
        $billWhere['bill_type'] = 'S';
        $billWhere['item_type'] = 'PF';
        $bill_list = $bill_model->getErpBillList($billWhere,"bill_id,wholesale_id,to_company_id");
        $error = false;
        $company_id = 0;
        if(!empty($bill_list)){
            $bill_ids = array();
            $wholesale_ids = array();
            foreach ($bill_list as $vo){
                if(!in_array($vo['wholesale_id'],$wholesale_ids)){
                    $wholesale_ids[] = $vo['wholesale_id'];
                }
                $bill_ids[] = $vo['bill_id'];
                $company_id = $vo['to_company_id'];
            }
            if(count($wholesale_ids)>1){
                $error = "不同批发客户的单据不能一起打印！";
            }else if($company_id==0){
                $error = "批发入库公司不能为空！";
            }
        }else{
            $error = "没有符合条件的单据！";
        }
        //if($error === false){
           $store_list = Model("store")->getStoreList(array("store_company_id"=>$company_id));
           Tpl::output('store_list', $store_list);
        //}
        Tpl::output('error', $error);        
        Tpl::showpage('erp_bill.print_bill_goods_form','null_layout');
    }
    /**
     * 打印货品价格标签
     */
    public function print_bill_goodsOp(){
        header("Content-type:text/html;charset=utf-8");
        
        $bill_ids = $_GET['_ids'];
        $store_id = $_GET['store_id'];
        $bill_model = new erp_billModel();
        $bill_goods_model	= new erp_bill_goodsModel();
        $goods_items_model	= new goods_itemsModel();
        $warehouse_model	= new warehouse_apiModel();
        
        $billWhere = array();
        $billWhere['bill_id'] = array('in',$bill_ids);
        $billWhere['bill_type'] = 'S';
        $billWhere['item_type'] = 'PF';
        $bill_list = $bill_model->getErpBillList($billWhere,"bill_id,wholesale_id");
        $bill_goods_list = array();
        if(!empty($bill_list)){
            $bill_ids = array();
            $wholesale_ids = array();
            foreach ($bill_list as $vo){
                if(!in_array($vo['wholesale_id'],$wholesale_ids)){
                    $wholesale_ids[] = $vo['wholesale_id'];
                }
                $bill_ids[] = $vo['bill_id'];
            }
            if(count($wholesale_ids)>1){
                $errorlist[] = "不同批发客户的单据不能一起打印！";
            } else{
                $bill_goods_list = $bill_goods_model->searchBillGoodsList(array('bill_id'=>array('in',$bill_ids)),1000);
            }
        }
        
        $goodslist = array();
        $errorlist = array(); 
        if(empty($errorlist) && empty($bill_goods_list)){
            $errorlist[] = "没有找到需要打印的货号，请确保打印单据是批发销售出库单！";
        }
        if(empty($errorlist)){
            $goods_ids = array();
            foreach ($bill_goods_list as $key=>$billGoods){
                
                unset($billGoods['goods_data']);
                $sale_price = $billGoods['sale_price'];
                $goods_id   = $billGoods['goods_id'];
                $company_id  = $billGoods['to_company_id'];
                if(in_array($goods_id,$goods_ids)){
                    continue;
                }else{
                    $goods_ids[] = $goods_id;
                }
                if($store_id==0){
                    $errorlist[] = "货号{$goods_id}:无法确定归属门店,请确保单据{$billGoods['bill_no']}已签收！";
                    continue;
                }
                $billGoods['jijiachengben'] = $sale_price;
                $policyGoodsModel = new app_salepolicy_goodsModel();
                $billGoods['goods_price'] = $policyGoodsModel->getXianhuoPrice($billGoods, $store_id, $company_id);
                //$billGoods['goods_price'] = $warehouse_model->getGoodsPrice($billGoods, $store_id, $company_id);
                if($billGoods['goods_price']<=0){
                    $errorlist[] = "货号{$goods_id}找不到销售政策!";
                    continue;
                }else{
                    $goodslist[] = $billGoods;
                }
            }
        }
        if(!empty($errorlist)){  
            header("Content-type:text/html;charset=utf-8");
            echo implode("<br/>", $errorlist);
            exit;
        }
        
        $this->down_goods_list($goodslist);
        
        /*
        Tpl::output('goodslist',$goodslist);
        Tpl::output('print_type',"print_bill_goods");
        Tpl::showpage('store_goods_items.print_price','null_layout');
        */
    }
    
    protected function down_goods_list($goods_list){
        $tihCz = array(
            '24K' =>'足金',
            '千足金银'    =>'足金银',
            'S990'    =>'足银',
            '千足银' =>'足银',
            '千足金' =>'足金',
            'PT900'   =>'铂900',
            'PT999'   =>'铂999',
            'PT950'   =>'铂950',
            '18K玫瑰黄'=>'18K金',
            '18K玫瑰白'=>'18K金',
            '18K玫瑰金'=>'18K金',
            '18K黄金'=>'18K金',
            '18K白金'=>'18K金',
            '18K黑金'=>'18K金',
            '18K彩金'=>'18K金',
            '18K红'=>'18K金',
            '18K黄白'=>'18K金',
            '18K分色'=>'18K金',
            '18K黄'=>'18K金',
            '18K白'=>'18K金',
            '9K玫瑰黄'=>'9K金',
            '9K玫瑰白'=>'9K金',
            '9K玫瑰金'=>'9K金',
            '9K黄金'=>'9K金',
            '9K白金'=>'9K金',
            '9K黑金'=>'9K金',
            '9K彩金'=>'9K金',
            '9K红'=>'9K金',
            '9K黄白'=>'9K金',
            '9K分色'=>'9K金',
            '9K黄'=>'9K金',
            '9K白'=>'9K金',
            '10K玫瑰黄'=>'10K金',
            '10K玫瑰白'=>'10K金',
            '10K玫瑰金'=>'10K金',
            '10K黄金'=>'10K金',
            '10K白金'=>'10K金',
            '10K黑金'=>'10K金',
            '10K彩金'=>'10K金',
            '10K红'=>'10K金',
            '10K黄白'=>'10K金',
            '10K分色'=>'10K金',
            '10K黄'=>'10K金',
            '10K白'=>'10K金',
            '14K玫瑰黄'=>'14K金',
            '14K玫瑰白'=>'14K金',
            '14K玫瑰金'=>'14K金',
            '14K黄金'=>'14K金',
            '14K白金'=>'14K金',
            '14K黑金'=>'14K金',
            '14K彩金'=>'14K金',
            '14K红'=>'14K金',
            '14K黄白'=>'14K金',
            '14K分色'=>'14K金',
            '14K黄'=>'14K金',
            '14K白'=>'14K金',
            '19K黄'=>'19K金',
            '19K白'=>'19K金',
            '19K玫瑰黄'=>'19K金',
            '19K玫瑰白'=>'19K金',
            '19K玫瑰金'=>'19K金',
            '19K黄金'=>'19K金',
            '19K白金'=>'19K金',
            '19K黑金'=>'19K金',
            '19K彩金'=>'19K金',
            '19K红'=>'19K金',
            '19K黄白'=>'19K金',
            '19K分色'=>'19K金',
            '20K黄'=>'20K金',
            '20K白'=>'20K金',
            '20K玫瑰黄'=>'20K金',
            '20K玫瑰白'=>'20K金',
            '20K玫瑰金'=>'20K金',
            '20K黄金'=>'20K金',
            '20K白金'=>'20K金',
            '20K黑金'=>'20K金',
            '20K彩金'=>'20K金',
            '20K红'=>'20K金',
            '20K黄白'=>'20K金',
            '20K分色'=>'20K金',
            '21K黄'=>'21K金',
            '21K白'=>'21K金',
            '21K玫瑰黄'=>'21K金',
            '21K玫瑰白'=>'21K金',
            '21K玫瑰金'=>'21K金',
            '21K黄金'=>'21K金',
            '21K白金'=>'21K金',
            '21K黑金'=>'21K金',
            '21K彩金'=>'21K金',
            '21K红'=>'21K金',
            '21K黄白'=>'21K金',
            '21K分色'=>'21K金',
            '22K黄'=>'22K金',
            '22K白'=>'22K金',
            '22K玫瑰黄'=>'22K金',
            '22K玫瑰白'=>'22K金',
            '22K玫瑰金'=>'22K金',
            '22K黄金'=>'22K金',
            '22K白金'=>'22K金',
            '22K黑金'=>'22K金',
            '22K彩金'=>'22K金',
            '22K红'=>'22K金',
            '22K黄白'=>'22K金',
            '22K分色'=>'22K金',
            '23K黄'=>'23K金',
            '23K白'=>'23K金',
            '23K玫瑰黄'=>'23K金',
            '23K玫瑰白'=>'23K金',
            '23K玫瑰金'=>'23K金',
            '23K黄金'=>'23K金',
            '23K白金'=>'23K金',
            '23K黑金'=>'23K金',
            '23K彩金'=>'23K金',
            '23K红'=>'23K金',
            '23K黄白'=>'23K金',
            '23K分色'=>'23K金',
            'S925黄'=>'S925',
            'S925白'=>'S925',
            'S925玫瑰黄'=>'S925',
            'S925玫瑰白'=>'S925',
            'S925玫瑰金'=>'S925',
            'S925黄金'=>'S925',
            'S925白金'=>'S925',
            'S925黑金'=>'S925',
            'S925彩金'=>'S925',
            'S925红'=>'S925',
            'S925黄白'=>'S925',
            'S925分色'=>'S925',
            'S925'    =>'银925'
        );
        
        $stone_arr = array('红宝'=>'红宝石',
            '珍珠贝'=>'贝壳',
            '白水晶'=>'水晶',
            '粉晶'=>'水晶',
            '茶晶'=>'水晶',
            '紫晶'=>'水晶',
            '紫水晶'=>'水晶',
            '黄水晶'=>'水晶',
            '彩兰宝'=>'蓝宝石',
            '彩色蓝宝'=>'蓝宝石',
            '蓝晶'=>'水晶',
            '黄晶'=>'水晶',
            '柠檬晶'=>'水晶',
            '红玛瑙'=>'玛瑙',
            '黑玛瑙'=>'玛瑙',
            '奥泊'=>'宝石',
            '黑钻'=>'钻石',
            '琥铂'=>'琥铂',
            '虎晴石'=>'宝石',
            '大溪地珍珠'=>'珍珠',
            '大溪地黑珍珠'=>'珍珠',
            '淡水白珠'=>'珍珠',
            '淡水珍珠'=>'珍珠',
            '南洋白珠'=>'珍珠',
            '南洋金珠'=>'珍珠',
            '海水香槟珠'=>'珍珠',
            '混搭珍珠'=>'珍珠',
            '蓝宝'=>'蓝宝石',
            '宝石石'=>'宝石',
            '黄钻'=>'钻石');
        
        $tihCt = array(
            '男戒'=>'戒指',
            '女戒'=>'戒指',
            '情侣戒'=>'戒指',
            '对戒'=>'戒指'
        );
        $content .= "货号,款号,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,国际证书,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,样板可做镶口范围,原价,镶口,原始货号\r\n";
        foreach ($goods_list as $key => $line) {
            $goods_id = $line['goods_id'];
            
            if($line['goods_name'] != ''){
                //$line['goods_name'] = str_replace("千", "", $line['goods_name']);
                $line['goods_name'] = str_replace('锆石','合成立方氧化锆',$line['goods_name']);
                $line['goods_name'] = str_replace(array_keys($tihCz), array_values($tihCz), $line['goods_name']);
                $line['goods_name'] = str_replace(array_keys($stone_arr), array_values($stone_arr), $line['goods_name']);
                $line['goods_name'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['goods_name']);
            }
        
            if($line['zhushi'] != ''){
                $line['zhushi'] = str_replace('锆石','合成立方氧化锆',$line['zhushi']);
            }
        
            //$fushishu = !empty($line['fushilishu'])?$line['fushilishu']."p":'';
            $fushizhong = !empty($line['fushizhong'])?($line['fushizhong']/1)."ct/".($line['fushilishu']/1)."p":'';
            
            $shoucun = !empty($line['shoucun'])?$line['shoucun']."#":'';
            //$shuzhishu=$line['zhushilishu']>0 ? "/".$line['zhushilishu']."p" : "" ;
        
            $zhushizhong = !empty($line['zuanshidaxiao']) ? ($line['zuanshidaxiao']/1)."ct/".($line['zhushilishu']/1).'p' : '';
            $jinzhong = !empty($line['jinzhong'])?round($line['jinzhong'], 2)."g":'';
            $tiemp = '';
            if(trim($line['zhushijingdu']) != '' && trim($line['zhushijingdu']) != '0'){
                $tiemp = $line['zhushijingdu'];
            }
            if(trim($line['zhushiyanse']) != '' && trim($line['zhushiyanse']) != '0'){
                $tiemp.="/".$line['zhushiyanse'];
            }
        
            $content .=
            "\"" . $line['goods_id'] . "\"," .//货号
            "\"" . $line['goods_sn'] . "\"," .//款号
            "\"" . '' . "\"," .//基因吗
            "\"" . $line['shoucun'] . "\"," .//手寸
            "\"" . '' . "\"," .//长度
            "\"" . $line['zhushishu'] . "\"," .//主石粒数
            "\"" . $line['zhushizhong'] . "\"," .//主石重
            "\"" . '' . "\"," .//副石数
            "\"" . '' . "\"," .//副石重
            "\"" .  "\"," .       //加工商编号
            "\"" . $line['jinzhong'] . "\"," .//总重
            "\"" . $line['zhushijingdu'] . "\"," .//净度
            "\"" . $line['zhushiyanse'] . "\"," .//颜色
            "\"" . $line['zhengshuhao'] . '"' . "," .//证书号
            "\"" . '' . "\"," .//国际证书
            "\"" . $line['zhushiqiegong'] . "\"," .//主石切工
            "\"" . '' . "\"," .                //标签备注
            "\"" . '' . "\"," .//主石
            "\"" . '' . "\"," .//副石
            "\"" . $line['caizhi'].$line['jinse'] . "\"," .//主成色
            "\"" . '' . "\"," .//饰品分类
            "\"" . '' . "\"," .//款式分类
            "\"" . $line['goods_name'] . "\"," .//名称
            "\"" . '' . "\"," .            //石3副石
            "\"" . '' . "\"," .            //石3粒数
            "\"" . '' . "\"," .            //石3重
            "\"" .  "\"," .           //石4副石
            "\"" .  "\"," .           //石4粒数
            "\"" .  "\"," .               //石4重
            "\"" .  "\"," .       //石5副石
            "\"" .  "\"," .           //石5粒数
            "\"" .  "\"," .           //石5重
            "\"" . $line['jinzhong'] . "\"," .
            "\"" . '' . "\"," .//副成色
            "\"" . '' . "\"," .//副成色重
            "\"" . '' . "\"," .//买入工费
            "\"" . '' . "\"," .//计价工费
            "\"" . '' . "\"," .//加价率
            //"\"" . $line['jiajialv']. "\"," .
            "\"" . '' . "\"," .//最新零售价
            "\"" . '' . "\"," .//模号
            "\"" . '' . "\"," .//品牌
            "\"" . '' . "\"," .           //证书数量
            "\"" . '' . "\"," . //配件数量
            "\"" .  "\"," .               //时尚款
            "\"" . '' . "\",".           //系列
            "\"" .  "\"," .           //属性
            "\"" .  "\"," .           //类别
            "\"" . $line['jijiachengben'] . "\"," .//成本价
            "\"" . '' . "\"," .//入库时间
            "\"" .  "\"," .           //加价率代码
            "\"" .  "\"," .           //主石粒重
            "\"" .  "\"," .           //副石粒重
            "\"" .  "\"," .           //标签手寸
            "\"" . '' . "\"," .                //ziyin
            "\"" . '' . "\"," .//最新零售价
            "\"" . '' . "\"," .//买入成本
            "\"" . $line['goods_price'] . "\"," .//新零售价
            "\"" . '' . "\"," .//一口价
            "\"" . '' . "\"," .//标价
            "\"" . '' . "\",".//定制价
            "珂兰钻石,".                  // A
            "\"" .$line['goods_name']."\",".   // B
            "\"" .'￥'.round($line['goods_price'])."\",". // C
            "\"" .$zhushizhong."\",". // d
            "\"" .$fushizhong."\",". // e
            "\"" .$jinzhong."\",". // f
            "\"" .$line['zhengshuhao']."\",". // g
            "\"" .$tiemp."\",".   // h
            "\"" .$shoucun."\",". // i
            "\"" . '' ."\",".  // hb_f
            "\"" . '' ."\",".    // hb_g
            "\"" . '' ."\",".   // 样板可做镶口范围
            "\"" . '' ."\",".    // 原价
            "\"" . $line['jietuoxiangkou'] ."\",".   // 镶口
            "\"" . '' ."\"\r\n".    // 原始货号$line['ygoods_id']
            "";
        }
        
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . "tiaoma.csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
    }


    /**
     * 打印单据明细
     */
    public function print_bill_detailsOp(){

        $bill_ids = $_GET['_ids'];
        $bill_id_list = explode(",",$bill_ids);
        if(count($bill_id_list) != 1){
            //exit('请选择一张单据打印!');
        }
        $bill_model = new erp_billModel();
        $bill_goods_model   = new erp_bill_goodsModel();
        $goods_items_model  = new goods_itemsModel();
        $salepolicy_model   = new app_salepolicy_goodsModel();
        $order_model = new orderModel();
        $express_model = new expressModel();

        $billWhere = array();
        $billWhere['bill_id'] = array('in',$bill_id_list);
        $bill_info = $bill_model->getErpBillList($billWhere);
        $bill_info = $bill_model->formatErpBillList($bill_info);
        $bill_goods_list = array();
        if(!empty($bill_info)){
            $bill_ids = array_column($bill_info,'bill_id');
            $bill_goods_list = $bill_goods_model->searchBillGoodsList(array('bill_id'=>array('in',$bill_ids)),1000);

            $bill_info = $bill_info[0];
            $bill_info['create_time'] = substr($bill_info['create_time'], 0, 10);
        }
        $bill_type = $bill_info['bill_type'];
        $item_type = $bill_info['item_type'];
        $goodslist = array();
        $tongji = array(
            'zuanshidaxiao'=>0,
            'zhushilishu'=>0,
            'fushizhong'=>0,
            'fushilishu'=>0,
            'jinzhong'=>0,
            'num'=>0,
            'sale_price'=>0,
            'management_fee'=>0
            );
        $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
        foreach ($bill_goods_list as $key=>$billGoods){
            $sale_price = $billGoods['sale_price'];
            $goods_id   = $billGoods['goods_id'];
            $store_id   = $billGoods['to_store_id'];
            $company_id  = $billGoods['to_company_id'];
            if(!$show_chengben){
                $billGoods['yuanshichengben'] = '/';
            }
            $tongji['zuanshidaxiao'] = bcadd($tongji['zuanshidaxiao'], $billGoods['zuanshidaxiao'],3);
            $tongji['zhushilishu'] = bcadd($tongji['zhushilishu'], $billGoods['zhushilishu'],3);
            $tongji['fushizhong'] = bcadd($tongji['fushizhong'], $billGoods['fushizhong'],3);
            $tongji['fushilishu'] = bcadd($tongji['fushilishu'], $billGoods['fushilishu'],3);
            $tongji['jinzhong'] = bcadd($tongji['jinzhong'], $billGoods['jinzhong'],3);
            $tongji['goods_count'] = bcadd($tongji['goods_count'], $billGoods['goods_count'],3);
            $tongji['sale_price'] = bcadd($tongji['sale_price'], $sale_price,3);
            $tongji['management_fee'] = bcadd($tongji['management_fee'], $billGoods['management_fee'],3);
            $goodslist[] = $billGoods;
        }

        if($bill_info['bill_type'] == 'S' &&
         in_array($bill_info['item_type'], array('PF','LS')) &&
         $bill_info['from_company_id'] == $_SESSION['store_company_id']
         ){

            $showpage = 'store_erp_bill_s.print';
        }elseif($bill_info['bill_type'] == 'B' ||
         ($bill_info['bill_type'] == 'D' && 
            $bill_info['item_type'] == 'PF' && 
            $bill_info['from_company_id']==$_SESSION['store_company_id'])){

            $showpage = 'store_erp_bill_d.print';
        }elseif($bill_info['bill_type'] == 'M' && $bill_info['item_type'] == 'WX'){
            if(!empty($bill_info['order_sn'])){
                $order = $order_model->getOrderInfo(array('order_sn'=>$bill_info['order_sn']));
                $bill_info['buyer_name'] = $order['buyer_name'];
            }
            $express_list = $express_model->getExpressList();
            $express_list = array_column($express_list, 'e_name', 'id');
            $bill_info['express_name']=isset($express_list[$bill_info['express_id']])?$express_list[$bill_info['express_id']]:"";
            $bill_status = $bill_model->getBillStatusList();
            $showpage = 'store_erp_bill_m.print';
        }else{
            $showpage = '';
            showDialog('此类型单据无法打印！');
        }

        Tpl::output('now_time', date('Y-m-d H:i:s'));
        Tpl::output('billlist', $bill_info);
        Tpl::output('goodslist', $goodslist);
        Tpl::output('tongji', $tongji);
        Tpl::output('bill_status', $bill_status);
        Tpl::output('print_type',"print_bill_goods");
        Tpl::showpage($showpage,'null_layout');
    }

    /**
     * 完成打包操作
     */
    public function complete_pickOp() {
        $bill_ids = $_GET['bill_ids'];
        if (empty($bill_ids)) {
            showDialog('销售出库单不能为空');
        }         
        $erp_bill_model = new erp_billModel();
        $order_model = new orderModel();        
        $bill_list = $erp_bill_model->getErpBillList(array('bill_type' =>'S', 'bill_status' => 1, 'bill_id' => array('in', explode(',', $bill_ids))));
        if (empty($bill_list)) {
            showDialog('没有找到符合要求的销售出库单');
        }
        try{
            $erp_bill_model->beginTransaction();
            foreach ($bill_list as $bill_info){
                 $bill_id  = $bill_info['bill_id'];
                 $bill_no  = $bill_info['bill_no'];
                 $order_sn = $bill_info['order_sn'];
                 if(empty($order_sn)){
                     throw new Exception("单据{$bill_no}未绑定订单号！");
                 }
                 
                 $result = $erp_bill_model->checkBill($bill_id,1,"审核通过",false);
                 if($result['success']==0){
                     throw new Exception($result['msg']);
                 }
                 $order = $order_model->getOrderInfo(array('order_sn'=>$order_sn));
                 if(empty($order)){
                     throw new Exception("单据{$bill_no}绑定的订单号{$order_sn}不存在");
                 }else if($order['order_state'] != ORDER_STATE_PICKING){
                     throw new Exception("单据{$bill_no}绑定的订单号{$order_sn}状态不对");
                 }
                 $res = $order_model->editOrder(array("order_state"=>ORDER_STATE_PRESEND),array('order_sn'=>$order_sn));
                 if(!$res){
                     throw new Exception("更新订单状态失败,订单号{$order_sn}");
                 }
                 //写入订单日志
                 $res = $order_model->addOrderLog(array('order_id' =>$order['order_id'],'log_role'=>'seller', 'log_msg' => '打包完成：销售出库单'.$bill_no."审核通过", 'log_user' =>$_SESSION['seller_name'],'log_orderstate' =>ORDER_STATE_PRESEND));
                 if(!$res){
                     throw new Exception("订单日志写入失败！");
                 } 
            } 
            $erp_bill_model->commit();
        }catch (Exception $e){
            $erp_bill_model->rollback();
            showDialog($e->getMessage());
        }         
        showDialog('操作成功，商品已打包出库', urlShop('erp_bill', 'index',array('bill_type'=>'S', 'bill_status' => 2)), 'succ');
    }   
    
    /**
     * 用户中心右边，小导航
     *
     * @param string $menu_type 导航类型
     * @param string $menu_key 当前导航的menu_key
     * @param boolean $allow_promotion
     * @return
     */
    private function profile_menu($menu_type,$menu_key,$bill_type_name = '') {
        $menu_array = array();
        switch ($menu_type) {
            case 'show':                
                $menu_array = array(
                array('menu_key'=>$_GET['bill_type'],'menu_name'=>"返回单据列表",'menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>$_GET['bill_type']))),
                array('menu_key' =>'bill','menu_name'=>$bill_type_name.'信息','menu_url' =>urlShop('erp_bill', 'show',array('bill_id'=>$_GET['bill_id'],'bill_type'=>$_GET['bill_type']))),
                array('menu_key' =>'bill_goods', 'menu_name' =>$bill_type_name.'商品列表', 'menu_url' => urlShop('erp_bill','show_bill_goods',array('bill_id'=>$_GET['bill_id'],'menu_key'=>'bill_goods','bill_type'=>$_GET['bill_type']))),
                array('menu_key' =>'bill_log', 'menu_name' =>$bill_type_name.'日志列表', 'menu_url' => urlShop('erp_bill','show_bill_log',array('bill_id'=>$_GET['bill_id'],'menu_key'=>'bill_goods','bill_type'=>$_GET['bill_type'])))
                );
                if($bill_type_name=="盘点单" && !$this->check_seller_limit("limit_show_pandian_goods")){
                    unset($menu_array[2]);
                }
                break;
            case 'edit':
                $menu_array = array(
                array('menu_key' =>'bill','menu_name'=>'编辑'.$bill_type_name,'menu_url' =>urlShop('erp_bill_l', 'edit')),
                array('menu_key' =>'bill_goods', 'menu_name' =>'编辑'.$bill_type_name.'商品', 'menu_url' => urlShop('erp_bill', 'edit',array('bill_id'=>$_GET['bill_id'],'menu_key'=>'bill_goods')))
                );
                break;
            case 'list':
                $menu_array = array(
                array('menu_key'=>'','menu_name'=>"所有单据",'menu_url'=>urlShop('erp_bill', 'index')),
                array('menu_key'=>'L','menu_name'=>"进货单",'menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'L'))),
                array('menu_key'=>'M','menu_name'=>"调拨单",'menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'M'))),
                array('menu_key'=>'D','menu_name'=>"销售退货单",'menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'D'))),
                array('menu_key'=>'B','menu_name'=>"退货返厂单",'menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'B'))),
                //array('menu_key'=>'C','menu_name'=>"其他出库单",'menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'C'))),
                array('menu_key'=>'S','menu_name'=>'销售出库单','menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'S'))),
                array('menu_key'=>'MW','menu_name'=>'维修调拨单','menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'MW'))),
                array('menu_key'=>'SW','menu_name'=>'维修出库单','menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'SW'))),
                array('menu_key'=>'DW','menu_name'=>'维修入库单','menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'DW'))),
                array('menu_key'=>'W','menu_name'=>'盘点单','menu_url'=>urlShop('erp_bill', 'index',array('bill_type'=>'W'))),
                );
                break;
        }
        Tpl::output ( 'member_menu', $menu_array );
        Tpl::output ( 'menu_key', $menu_key );
    }


   //导出单据列表
    public function downloadOp() {
        $erp_bill_model = new erp_billModel();//Model('erp_bill');
        $condition=$this->convertParams($_GET);
        $erp_bill_model->searchErpBillList($condition,'*',1);
        $countNum = $erp_bill_model->gettotalnum();//获取总数量
        if($countNum>=200000){
            $data=null;
            $this->downExcel($data);
            exit();
        }
        $data = $erp_bill_model->searchErpBillList($condition,'*',$countNum,'bill_id desc');
        $data = $erp_bill_model->formatErpBillList($data);
        //print_r($data);exit;
        
        if (empty($data)) {
            $xls_content = '没有数据！或者数据太多请联系技术导出';
        } else {
            $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
            $company_model = Model('company');
            $companyInfo = $company_model->getCompanyInfo(array('id'=>$_SESSION['store_company_id']));
            $pifajia = "";
            if($companyInfo['is_shengdai'] == 1){
                $pifajia = "批发价,";
            }

            $xls_content = "单据编号,单据类型,单据状态,成本价,{$pifajia} 数量,订单号,制单时间,制单人,审核时间,审核人,出库公司,出库门店,入库公司,入库门店,入库仓,供应商,批发客户,备注\r\n";
            foreach ($data as $val) {
                $xls_content .= $val['bill_no'] . ",";
                $xls_content .= billType($val) . ",";
                $xls_content .= billStatus($val) . ",";
                $xls_content .= billChengbenTotal($val,$show_chengben) . ",";
                if($companyInfo['is_shengdai'] == 1){
                    if($val['bill_type'] =='S' && $val['item_type']=='PF'){
                        $xls_content .= $val['goods_total'] . ",";
                    }else{
                        $xls_content .= "/,";
                    }
                }
                $xls_content .= $val['goods_num'] . ",";
                $xls_content .= $val['order_sn'] . ",";
                $xls_content .= $val['create_time'] . ",";
                $xls_content .= $val['create_user'] . ",";
                $xls_content .= $val['check_time'] . ",";
                $xls_content .= $val['check_user'] . ",";
                $xls_content .= $val['from_company_name'] . ",";
                $xls_content .= $val['from_store_name'] . ",";
                $xls_content .= $val['to_company_name'] . ",";
                $xls_content .= $val['to_store_name'] . ",";
                $xls_content .= $val['to_house_name'] . ",";
                $xls_content .= $val['supplier_name'] . ",";
                $xls_content .= $val['wholesale_name'] . ",";
                $xls_content .= $val['remark'] . "\n";

            }
        }
        exportcsv($xls_content ,'导出单据');
    }





    //导出单据列表
    public function detail_downloadOp() {
        $bill_goods_model	= new erp_bill_goodsModel();//Model('erp_bill');
        $where=$this->convertParams($_GET);
        $condition = array();
        foreach ($where as $key=>$val){
            if($key == 'sql'){
                $condition[$key] = $val;
            }else{
                $condition['erp_bill.'.$key] = $val;
            }

        }
        //print_r($condition);exit;
        $bill_goods_model->searchErpBillGoodsList($condition,1,'');
        $countNum = $bill_goods_model->gettotalnum();//获取总数量

        if($countNum>=200000){
            $data=null;
            $this->downExcel($data);
            exit();
        }
        $data = $bill_goods_model->searchErpBillGoodsList($condition,$countNum,'erp_bill.bill_id desc');
       //print_r($data);exit;
        foreach ($data as $key=>$val){
            $data[$key]['from_company_id'] = $val['b_from_company_id'];
            $data[$key]['to_company_id'] = $val['b_to_company_id'];
            $data[$key]['from_store_id'] = $val['b_from_store_id'];
            $data[$key]['to_store_id'] = $val['b_to_store_id'];
        }
        $data = $bill_goods_model->formatErpBillGoodsList($data);


        if (empty($data)) {
            $xls_content = '没有数据！或者数据太多请联系技术导出';
        } else {


            $show_chengben = $this->check_seller_limit('limit_show_goods_chengben');
            $xls_content = "单号,单据类型,货号,款号,商品名称,材质, 颜色,证书类型,证书号,钻石大小,主石粒数,指圈,金重,数量,成本价,销售价,管理费,退货价,出库公司,出库门店,入库公司,入库门店,入库方式,入库仓库,出库仓库,结算状态,结算人,结算时间\r\n";
            foreach ($data as $val) {
                $xls_content .= $val['bill_no'] . ",";
                $xls_content .= billType($val) . ",";
                $xls_content .= $val['goods_itemid'] . ",";
                $xls_content .= $val['goods_sn'] . ",";
                $xls_content .= $val['goods_name'] . ",";
                $xls_content .= $val['caizhi'] . ",";
                $xls_content .= $val['jinse'] . ",";
                $xls_content .= $val['zhengshuleibie'] . ",";
                $xls_content .= $val['zhengshuhao'] . ",";
                $xls_content .= $val['zuanshidaxiao'] . ",";
                $xls_content .= $val['zhushilishu'] . ",";
                $xls_content .= $val['shoucun'] . ",";
                $xls_content .= $val['jinzhong'] . ",";
                $xls_content .= $val['goods_count'] . ",";
                //成本价
                if(in_array($val['bill_type'],array('L','B','C','D'))){
                    $xls_content .= billgoodsChengbenShow($val, $val,$show_chengben) . ",";
                }else if(in_array($val['bill_type'],array('S')) && in_array($val['item_type'],array('PF','LS'))){
                   if($_SESSION['store_company_id'] == $val['from_company_id']){
                       $xls_content .= billgoodsChengbenShow($val, $val,$show_chengben) . ",";
                   }elseif($_SESSION['store_company_id'] == $val['to_company_id']){
                       $xls_content .= $val['sale_price'] . ",";
                   }else{
                       $xls_content .=  "/,";
                   }
                }else{
                    $xls_content .=  "/,";
                }
                //销售价
                if(in_array($val['bill_type'],array('S')) && in_array($val['item_type'],array('PF','LS')) && $_SESSION['store_company_id'] == $val['from_company_id']){
                    $xls_content .= $val['sale_price'] . ",";
                }else{
                    $xls_content .=  "/,";
                }

                //管理费
                if(in_array($val['bill_type'],array('S')) && in_array($val['item_type'],array('PF'))){
                    $xls_content .= $val['management_fee'] . ",";
                }else{
                    $xls_content .=  "/,";
                }
                //退货价
                if(in_array($val['bill_type'],array('D')) && in_array($val['item_type'],array('PF','LS'))){
                    $xls_content .= $val['sale_price'] . ",";
                }else{
                    $xls_content .=  "/,";
                }

                $xls_content .= $val['from_company_name'] . ",";
                $xls_content .= $val['from_store_name'] . ",";
                $xls_content .= $val['to_company_name'] . ",";
                $xls_content .= $val['to_store_name'] . ",";


                //入库方式
                if(in_array($val['bill_type'],array('L'))){
                    $xls_content .= paramsHelper::echoOptionText("in_warehouse_type",$val['in_warehouse_type']). ",";
                }else if(in_array($val['bill_type'],array('S')) && $val['item_type']=='PF' ){
                    $xls_content .= paramsHelper::echoOptionText("in_warehouse_type",$val['in_warehouse_type']). ",";
                }else{
                    $xls_content .=  "/,";
                }

                //入库仓库
                if(in_array($val['bill_type'],array('L','M'))){
                    $xls_content .= $val['to_house_name'] . ",";
                }else{
                    $xls_content .=  "/,";
                }

                //出库仓库
                if(in_array($val['bill_type'],array('M'))){
                    if($val['b_from_company_id'] == 58){
                        $xls_content .=  "总公司维修库,";
                    }else{
                        $xls_content .= $val['from_house_name'] . ",";
                    }

                }else{
                    $xls_content .=  "/,";
                }

                //结算状态、结算人、结算时间
                if(in_array($val['bill_type'],array('S')) && in_array($val['item_type'],array('PF'))){
                    $xls_content .= paramsHelper::echoOptionText('bill_goods_settle_type',$val['is_settled']) . ",";
                    $xls_content .= $val['settle_user'] . ",";
                    $xls_content .= $val['settle_time'] . "\n";

                }else{
                    $xls_content .=   "/,";
                    $xls_content .=   "/,";
                    $xls_content .=   "/\n";
                }





            }
        }
        exportcsv($xls_content ,'导出单据明细');
    }

}
