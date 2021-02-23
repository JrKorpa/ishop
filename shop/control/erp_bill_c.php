<?php
/**
 * 调拨单
 *
 * @珂兰技术中心提供技术支持
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_cControl extends BaseSellerControl {
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
        
        $warehouse_model = new erp_warehouseModel();                
        $warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$store_id,'is_enabled'=>1));
        
        Tpl::output('step',1);
        Tpl::output('warehouse_list',$warehouse_list);
        Tpl::showpage('erp_bill_c.add');
    }
    /**
     * 新增进货单 保存
     *
     */
    public function insert_oldOp(){
        set_time_limit(600);        
        $result = array("success"=>0,'msg'=>'');
        if(!$_POST){
            $result['msg'] = "非法提交";
            exit(json_encode($result));
        }
        $bill_type = "C";//其他出库单
        /**
         * 出库仓库
         */
        /* if(empty($_POST['from_house_id'])){
            $result['msg'] = "出库仓库不能为空";
            exit(json_encode($result));
        }else{
            $house_arr = explode("|",$_POST['from_house_id']);
            if(count($house_arr)==2){
                $from_store_id = $house_arr[0];
                $from_house_id = $house_arr[1];
            }else{
                $result['msg'] = "出库仓库参数错误！";
                exit(json_encode($result));
            }
        } */
        /**
         * 上传文件存在判断
        */
        if(empty($_FILES['file']['tmp_name'])){
            $result['msg'] = "请选择上传文件！";
            exit(json_encode($result));
        }
        $remark = !empty($_POST['remark'])?trim($_POST['remark']):'';
        $file	= $_FILES['file'];
        /**
         * 文件类型判定
         */
        $file_name_array	= explode('.',$file['name']);
        if($file_name_array[count($file_name_array)-1] != 'csv'){
            $result['msg'] = "请上传CSV格式文件！";
            exit(json_encode($result));
        }
        /**
         * 文件大小判定
         */
        if($file['size'] > intval(ini_get('upload_max_filesize'))*1024*1024){
            $result['msg'] = "文件大小超出最大上传限制！";
            exit(json_encode($result));
        }  
        if(empty(trim($_POST['remark']))){
            $result['msg'] = "请填写出库原因！";
            exit(json_encode($result));
        }  
          
        $remark = trim($_POST['remark']);
        $erp_bill_model	= new erp_billModel();
        $goods_items_model = new goods_itemsModel();
        
        $file_handler = fopen($file['tmp_name'],"r");
        $line = 0;
        $itemsid_array = array();
        while ($datav = fgetcsv($file_handler)) {            
            $line ++;            
            $is_empty_line = true;//是否为空行
            foreach ($datav as  $col_k=>$col_v){
                if(!empty($col_v)){
                    $is_empty_line = false;
                }
                $datav[$col_k] = trim(iconv('gbk','utf-8',$col_v));
            }
            if($line>10000){
                $result['msg'] = "文件记录行不能大于10000！";
                exit(json_encode($result));
            }
            $tip = "第{$line}行：";
            if($line == 1 || $is_empty_line==true){
                continue;
            }
            if(empty($datav[0])){
                $result['msg'] = $tip."商品编号不能为空";
                exit(json_encode($result));
            }
           
            if(empty($datav[1])){
                $result['msg'] = $tip."商品数量不能为空";
                exit(json_encode($result));
            }else if(!is_numeric($datav[1])){
                $result['msg'] = $tip."商品数量必须为数字";
                exit(json_encode($result));
            }
            $goods_itemid = $datav[0];//货号
            $goods_count = 1;//数量       
            //过滤重复
            if(in_array($goods_itemid,$itemsid_array)){
                continue;
            }else{
                $itemsid_array[] = $goods_itemid;
            }     
            //根据库存编号查询商品 (item_id+store_id)
            $goodsItemInfo = $goods_items_model->getGoodsItemInfo(array('item_id'=>$goods_itemid));
            if(empty($goodsItemInfo)){
                $result['msg'] = "商品编号{$goods_itemid}不存在";
                exit(json_encode($result));
            }else if($goodsItemInfo['status']!= 1){
                $result['msg'] = "商品编号{$goods_itemid}不是上架状态！";
                exit(json_encode($result));
            }else if($goodsItemInfo['store_id']!=$_SESSION['store_id']){
                $result['msg'] = "商品编号{$goods_itemid}不属于当前门店！";
                exit(json_encode($result));
            }else if($goodsItemInfo['current_kc_num']<$goods_count){
                $result['msg'] = "商品编号{$goods_itemid}库存不足！";
                exit(json_encode($result));
            }
           
            $bill_goods_list[] = array(
                'goods_id'=>$goodsItemInfo['goods_id'],
                'goods_itemid'=>$goods_itemid,
                'goods_sn'=>$goodsItemInfo['goods_sn'],
                'goods_commonid'=>$goodsItemInfo['goods_commonid'],
                'goods_name'=>$goodsItemInfo['goods_name'],
                'goods_count'=>$goods_count,
                'chengben_price'=>$goodsItemInfo['chengben_price'],
                'sale_price'=>$goodsItemInfo['sale_price'],
                'from_chain_id'=>$goodsItemInfo['chain_id'],
                'from_house_id'=>$goodsItemInfo['house_id'],
                'from_box_id'=>$goodsItemInfo['box_id'],
            );
        }
        fclose($file_handler);
        if(empty($bill_goods_list)){
            $result['msg'] = "上传文件没有货品数据！";
            exit(json_encode($result));
        }
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'bill_status'=>1,
            'store_id'=>$_SESSION['store_id'],
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>$remark,
            'supplier_id'=>$goodsItemInfo['supplier_id']
        );
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
     * 新增进货单 保存
     *
     */
    public function insertOp(){
        set_time_limit(600);
        $result = array("success"=>0,'msg'=>'');
        if(!$_POST){
            $result['msg'] = "非法提交";
            exit(json_encode($result));
        }
        $bill_type = "C";//其他出库单
        if(empty($_POST['goods_itemid'])){
            $result = callback(false,"货号不能为空！");
            exit(json_encode($result));
        }
        $remark = !empty($_POST['remark'])?trim($_POST['remark']):'';
        if(empty(trim($remark))){
            $result['msg'] = "请填写出库原因！";
            exit(json_encode($result));
        }
        $erp_bill_model	= new erp_billModel();
        $goods_items_model = new goods_itemsModel();
        $warehouse_model = new erp_warehouseModel();
        
        $goods_itemid_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        if(empty($goods_itemid_list)){
            $result = callback(false,"货号不能为空！");
            exit(json_encode($result));
        }
        $total_num = 0;
        $itemsid_array = array();
        $chengben_total=0;
        $goods_total=0;
        foreach ($goods_itemid_list as $goods_itemid) {
            //过滤重复
            if(in_array($goods_itemid,$itemsid_array)){
                continue;
            }else{
                $itemsid_array[] = $goods_itemid;
            }
            $total_num++;
            if($total_num>10000){
                $result['msg'] = "文件记录行不能大于10000！";
                exit(json_encode($result));
            }
            //根据库存编号查询商品 (item_id+store_id)
            $goods_count=1;
            $goodsItemInfo = $goods_items_model->getGoodsItemInfo(array('goods_id'=>$goods_itemid));
            if(empty($goodsItemInfo)){
                $result['msg'] = "商品编号{$goods_itemid}不存在";
                exit(json_encode($result));
            }else if($goodsItemInfo['is_on_sale']!= 2){
                $result['msg'] = "商品编号{$goods_itemid}不是上架状态！";
                exit(json_encode($result));
            }else if($goodsItemInfo['store_id']!=$_SESSION['store_id']){
                $result['msg'] = "商品编号{$goods_itemid}不属于当前门店！";
                exit(json_encode($result));
            }else if($goodsItemInfo['num']<$goods_count){
                $result['msg'] = "商品编号{$goods_itemid}库存不足！";
                exit(json_encode($result));
            }
            $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$goodsItemInfo['warehouse_id']));
            if(isset($warehouse) && $warehouse['lock']>0){
                $result = callback(false,"{$goods_itemid}货号所属仓库正在盘点中");
                exit(json_encode($result));
            }
            $bill_goods_list[] = array(
                'goods_itemid'=>$goodsItemInfo['goods_id'],
                'goods_sn'=>$goodsItemInfo['goods_sn'],
                'goods_name'=>$goodsItemInfo['goods_name'],
                'goods_count'=>1,
                'yuanshichengben'=>$goodsItemInfo['yuanshichengbenjia'],
                'mingyichengben'=>$goodsItemInfo['mingyichengben'],
                'jijiachengben'=>$goodsItemInfo['jijiachengben'],
                'sale_price'=>$goodsItemInfo['sale_price'],
                'sale_price'=>$goodsItemInfo['biaoqianjia'],
                'from_company_id'=>$goodsItemInfo['company_id'],
                'from_store_id'=>$_SESSION['store_id'],
                'from_house_id'=>$goodsItemInfo['warehouse_id'],
            );
            $chengben_total+=floatval($goodsItemInfo['yuanshichengbenjia']);
            $goods_total+=floatval($goodsItemInfo['sale_price']);
        }
        if(empty($bill_goods_list)){
            $result['msg'] = "上传文件没有货品数据！";
            exit(json_encode($result));
        }
        $bill_info = array(
            'bill_status'=>1,
            'from_company_id'=>$goodsItemInfo['company_id'],
            'from_store_id'=>$_SESSION['store_id'],
            'from_house_id'=>$goodsItemInfo['warehouse_id'],
            'supplier_id'=>$goodsItemInfo['prc_id'],
            'goods_num'=>$total_num,
            'goods_total'=>$goods_total,
            'chengben_total'=>$chengben_total,
            'to_store_id'=>$_SESSION['store_id'],
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>$remark,
        );
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
     * 下载其他出库单模板
     */
    public function down_templateOp(){
        set_time_limit(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=其他出库单模板.csv");
        header('Cache-Control: max-age=0');
        $title_arr = array(
            '商品编号'
        );
        foreach ($title_arr as $k => $v) {
            $title_arr[$k] = iconv('utf-8', 'GB18030', $v);
        }
        echo "\"".implode("\",\"",$title_arr)."\"\r\n";
    }

    /**
     * 获取货品信息
     */
    public function get_goods_infoOp(){
        $goods_itemid_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
        $goods_itemid_list = explode(',',$goods_itemid_list);
        $goods_itemid_list = array_filter($goods_itemid_list);
        if(empty($goods_itemid_list)){
            $result = callback(false,null);
            exit(json_encode($result));
        }
        $goods_model=Model('goods_items');
        $goods_list=$goods_model->getGoodsItemsList(array("goods_id"=>array('in',$goods_itemid_list)));
        $result = callback(true,null);
        $result['data']['goods_list']=$goods_list;
        $result['data']['total_count']=count($goods_list);
        $result['data']['total_chengben']=array_sum(array_column($goods_list,"mingyichengben"));
        exit(json_encode($result));
    }
    
}