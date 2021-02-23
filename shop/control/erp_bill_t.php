<?php
/**
 * 其他入库单
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_tControl extends BaseSellerControl {
    /**
     * 每次导出多少条记录
     * @var unknown
     */
    const EXPORT_SIZE = 1000;
    
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
        
        $supplier_model = new store_supplierModel();
        $warehouse_model = new erp_warehouseModel();
        
        
        $supplier_list = $supplier_model->getStoreSupplierList(array('sup_store_id'=>$store_id,'is_enabled'=>1));
        $warehouse_list = $warehouse_model->getWareHouseList(array('store_id'=>$store_id,'is_enabled'=>1));
        
        Tpl::output('step',1);
        Tpl::output('supplier_list',$supplier_list);
        Tpl::output('warehouse_list',$warehouse_list);
        
        Tpl::showpage('erp_bill_t.add');
    }
    /**
     * 新增其他入库单 保存
     *
     */
    public  function insertOp(){
        set_time_limit(0);
        $result = array("success"=>0,'msg'=>'');
        if(!$_POST){
            $result['msg'] = "非法提交";
            exit(json_encode($result));
        }
        $bill_type = "T";
        /**
         * 供应商判定
         */
        if(empty($_POST['supplier_id'])){
            $result['msg'] = "供应商不能为空";
            exit(json_encode($result));
        }else{
            $supplier_id = trim($_POST['supplier_id']);
        }
        /**
         * 入库仓库不能为空
         */
        if(empty($_POST['to_house_id'])){
            $result['msg'] = "入库仓库不能为空";
            exit(json_encode($result));
        }else{
            $house_arr = explode("|",$_POST['to_house_id']);
            if(count($house_arr)==2){
                $to_chain_id = (int)$house_arr[0];
                $to_house_id = (int)$house_arr[1];
            }else{
                $result['msg'] = "入库仓库参数错误！";
                exit(json_encode($result));
            }
        }
        //入库柜位
        if(empty($_POST['to_box_id'])){
            $result['msg'] = "入库柜位不能为空";
            exit(json_encode($result));
        }else{
            $to_box_id = (int)$_POST['to_box_id'];
        }
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
        
        $erp_bill_model	= new erp_billModel();
        $goods_model = new goodsModel();
        $warehouse_model = new erp_warehouseModel();
        
        $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$to_house_id));
        if(isset($warehouse) && $warehouse['lock']>0){
            $result['msg'] = "入库仓库正在盘点中！";
            exit(json_encode($result));
        }        
        
        
        $file_handler = fopen($file['tmp_name'],"r");
        $is_empty_data = true;
        
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
                $result['msg'] = $tip."SKU码不能为空";
                exit(json_encode($result));
            }
            if(empty($datav[1])){
                $result['msg'] = $tip."成本价不能为空";
                exit(json_encode($result));
            }else if(!is_numeric($datav[1])){
                $result['msg'] = $tip."成本价必须为数字";
                exit(json_encode($result));
            }
            if(empty($datav[2])){
                $result['msg'] = $tip."建议零售价不能为空";
                exit(json_encode($result));
            }else if(!is_numeric($datav[2])){
                $result['msg'] = $tip."建议零售价必须为数字";
                exit(json_encode($result));
            }
            if(empty($datav[3])){
                $result['msg'] = $tip."商品数量不能为空";
                exit(json_encode($result));
            }else if(!is_numeric($datav[3])){
                $result['msg'] = $tip."商品数量必须为数字";
                exit(json_encode($result));
            }
            $goods_sn = trim($datav[0]);//货号
            $chengben_price = trim($datav[1]);//成本价
            $sale_price = trim($datav[2]);//建议零售价
            $goods_count = trim($datav[3]);//商品数量
            $is_empty_data = false;
            //根据sku码查询商品 (goods_sn+store_id 为主键)
            $goodsInfo = $goods_model->getGoodsInfo(array('goods_sn'=>$goods_sn,'store_id'=>$_SESSION['store_id']));
            if(empty($goodsInfo)){
                $result['msg'] = $tip."SKU码不存在:".$goods_sn;
                exit(json_encode($result));
            }
            $bill_goods_list[] = array(
                'goods_id'=>$goodsInfo['goods_id'],
                'goods_sn'=>$goodsInfo['goods_sn'],
                'goods_commonid'=>$goodsInfo['goods_commonid'],
                'goods_name'=>$goodsInfo['goods_name'],
                'goods_count'=>$goods_count,
                'chengben_price'=>$chengben_price,
                'sale_price'=>$sale_price,
            );
        }
        fclose($file_handler);
        if($is_empty_data===true){
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
            'to_chain_id'=>$to_chain_id,
            'to_house_id'=>$to_house_id,
            'to_box_id'=>$to_box_id,
            'supplier_id'=>$supplier_id
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
     * 下载其他入库单导入模板
     */
    public function down_templateOp(){
        set_time_limit(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=其他入库单模板.csv");
        header('Cache-Control: max-age=0');
        $title_arr = array(
            'SKU码','成本价','建议零售价',"数量"
        );
        foreach ($title_arr as $k => $v) {
            $title_arr[$k] = iconv('utf-8', 'GB18030', $v);
        }
        echo "\"".implode("\",\"",$title_arr)."\"\r\n";
    }

}
