<?php
/**
 * 盘点单
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_wControl extends BaseSellerControl {
    
    public function __construct() {
        parent::__construct() ;
    }
    public function indexOp() {
        $this->addOp();
        exit;
    }
    /**
     * 添加其他入库单     *
     */
    public function addOp() {
        $store_id = $_SESSION['store_id'];
        
        $warehouse_model = new erp_warehouseModel();
        $warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$store_id,'is_enabled'=>1,'lock'=>0));
        
        Tpl::output('step',1);
        Tpl::output('warehouse_list',$warehouse_list);        
        Tpl::showpage('erp_bill_w.add');
    }
    /**
     * 新增盘点单 保存
     *
     */
    public  function insertOp(){

        $result = array("success"=>0,'msg'=>'');
        if($_POST['warehouse_id']===""){
            $result = callback(false,"请选择盘点仓库！");
            exit(json_encode($result));
        }
        $bill_type = "W";
        $warehouse_id = $_POST['warehouse_id'];        
        $remark = trim($_POST['remark']);
        $warehouse_model = new erp_warehouseModel();
        if($warehouse_id==0){
            $lock_exist = $warehouse_model->getWareHouseInfo(array('store_id'=>$_SESSION['store_id'],'lock'=>1));
            if($lock_exist){
                $result = callback(false,"存在正在盘点的仓库，不能选择全部盘点！");
                exit(json_encode($result));
            }
            $warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$_SESSION['store_id'],'is_enabled'=>1,'lock'=>0));
        }else{
            //$_GET['debug'] = 1;
            $warehouse_list = $warehouse_model->getWareHouseList(array('house_id'=>$warehouse_id,'store_id'=>$_SESSION['store_id'],'is_enabled'=>1,'lock'=>0));
            if(empty($warehouse_list)){
                $result = callback(false,"当前仓库无效或仓库已被锁定，请刷新页面重新选择仓库！");
                exit(json_encode($result));
            }
        }
        if(empty($warehouse_list)){
            $result = callback(false,"未找到可以盘点的仓库！");
            exit(json_encode($result));
        }
        //盘点单单头       
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'bill_status'=>1,
            'from_company_id'=>$_SESSION['store_company_id'],
            'from_store_id'=>$_SESSION['store_id'],
            'from_house_id'=>$warehouse_id,
            'remark'=>$remark,
            'item_status'=>0,
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
        );
        //盘点单附属表数据
        $erp_bill_w_list = array();
        foreach ($warehouse_list as $warehouse){
            $erp_bill_w_list[] = array(
                'warehouse_id'=>$warehouse['house_id'],
                'status'=>0,
            );
        }
        $erp_bill_model = new erp_billModel();
        $res = $erp_bill_model->createBillW($bill_info,$erp_bill_w_list);
        if($res['success']==1){
            $result = callback(true,"添加成功",$res['data']);
            exit(json_encode($result));
        }else{
            $result = callback(false,$res['msg']);
            exit(json_encode($result));
        }
            
    }
    

}
