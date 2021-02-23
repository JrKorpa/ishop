<?php
/**
 * 单据单头模型
 *
 *
 *
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_billModel extends Model {

    public function __construct(){
        parent::__construct('erp_bill');
    }
    
    public static function getBillTypeList(){
       return array(
           'B'=>'退货返厂单',
           'C'=>'其他出库单',
           'D'=>'销售退货单',
           'DW'=>'维修入库单',
           'M'=>'调拨单',
           'MW'=>'维修调拨单',
           'L'=>'进货单',
           'S'=>'销售出库单',
           'SW'=>'维修出库单',
           'W'=>'盘点单',
       );        
    }
    public static function getBillType($code){
        $list = self::getBillTypeList();
        return isset($list[$code])?$list[$code]:$code;
    }
    public static function getBillStatusList(){
        return array(
            '1'=>'待审核',
            '2'=>'已审核',
            '3'=>'已取消',
        	'4'=>'已签收',
        );
    }
    public static function getBillStatus($bill_status){
        $list = self::getBillStatusList();
        return isset($list[$bill_status])?$list[$bill_status]:$bill_status;        
    }
    /**
     * 创建单据编号
     * @param unknown $bil_type
     */
    public static function createBillNo($bill_id,$bill_type){
        $bill_id = substr($bill_id, -5);
        $bill_no = $bill_type . substr(date('Ymd', time()),2,6) . rand(100, 999) . str_pad($bill_id, 5,
            "0", STR_PAD_LEFT);
        return $bill_no;
    }
    public static function createGoodsItemid($item_id,$type="3"){
        $item_id = substr($item_id, -5);
        $item_id = $type . substr(date('Ymd', time()),2,6) . rand(100, 999) . str_pad($item_id, 5,
            "0", STR_PAD_LEFT);
        return $item_id;
    }
    public function updateBill($update,$condition){
        return $this->table('erp_bill')->where($condition)->update($update);
    }
    /**
     * 结算单据
     * @param unknown $bill_id
     * @param unknown $remark
     * @param string $transMode
     * @throws Exception
     * @return multitype:number string multitype: NULL
     */
    public function settleBill($bill_id,$remark,$transMode=true){
        $result = array('success'=>0,'msg'=>'','data'=>array());
        try{
            if($transMode===true){
                $this->beginTransaction();
            }
            $update = array(
                'is_settled'=>1,
                'settle_user'=>$_SESSION['seller_name'],
                'settle_time'=>date("Y-m-d H:i:s"),
            );
            $res = $this->updateBill($update,array('bill_id'=>$bill_id));
            if(!$res){
                throw new Exception("结算失败");
            }
            $update = array(
                'is_settled'=>1,
                'settle_user'=>$_SESSION['seller_name'],
                'settle_time'=>date("Y-m-d H:i:s"),
            );
            $res = $this->table('erp_bill_goods')->where(array('bill_id'=>$bill_id))->update($update);
            if(!$res){
                throw new Exception("结算失败");
            }
            //单据日志写入        
            $bill_log_model = new erp_bill_logModel();
            $bill_remark = "结算单据：{$remark}";
            $bill_log_model->createBillLog($bill_id, $bill_remark);
            
            if($transMode===true){
                $this->commit();
            }
            $result['success'] = 1;
            $result['msg'] = '结算成功';
        }catch (Exception $e){
            $result['success'] = 0;            
            $result['msg'] = $e->getMessage();
            if($transMode===true){
                $this->rollback();
            }            
        }
        return $result;
    }
    /**
     * 创建单据
     * @param unknown $bill
     * @param unknown $bill_goods
     * @param unknown $bill_type
     * @$transMod 内部集成事物
     */
    public function createBill($bill_info,$bill_goods_list,$bill_type,$transMode=true){
        $result = array('success'=>0,'msg'=>'','data'=>array());

        try{
            if($transMode===true){
               $this->beginTransaction();
            }
            $bill_info['bill_no'] = uniqid();
            $bill_info['bill_type'] = $bill_type;
            $bill_type_name = self::getBillType($bill_type);
            $warehouse_name = "";
            if(isset($bill_info['warehouse_name'])){
                $warehouse_name = $bill_info['warehouse_name'];
                unset($bill_info['warehouse_name']);
            }
            // ensure
            if (!isset($bill_info['from_store_id']) || empty($bill_info['from_store_id'])) {
                $bill_info['from_store_id'] = $_SESSION['store_id'];
            }

            if (!isset($bill_info['from_company_id']) || empty($bill_info['from_company_id'])) {
                $bill_info['from_company_id'] = $_SESSION['store_company_id'];
            }

            //插入单据信息
            $bill_id = $this->table("erp_bill")->insert($bill_info);
            if(!$bill_id){
                throw new Exception("单据创建失败！");
            }
            $bill_no = $this->createBillNo($bill_id, $bill_type);            
            $chengben_total = 0;
            $goods_total = 0;
            $goods_num = 0;
            $goods_itemid_arr = array();
            foreach ($bill_goods_list as $key=>&$goods){
                $goods['bill_no'] = $bill_no;
                $goods['bill_type'] = $bill_type;
                $goods['bill_id'] = $bill_id;
                $item_type = $bill_info['item_type'];
                $goodsListAll = array();
                $goods_count = $goods['goods_count'];
                for($i=0;$i< $goods_count;$i++){
                    //所有单据 goods_itemid 不能为空
                    if(empty($goods['goods_itemid'])){
                        throw new Exception("货号不能为空！");
                    }
                    $goods_itemid = $goods['goods_itemid'];
                    //成本，数量统计
                    $chengben_total += $goods['yuanshichengben'];
                    $goods_total +=$goods['sale_price'];
                    $goods_num +=1;
                    $params_data = array();
                    if($bill_type == "M"){
                        $params_data = array('is_on_sale'=>5);
                    }
                    elseif($bill_type=="C"){
                        $params_data = array('is_on_sale'=>3);
                    }
                    elseif($bill_type =="S" && $item_type == 'PF'){
                        $params_data = array('is_on_sale'=>10);
                    }
                    elseif($bill_type =="S" && $item_type == 'WX'){
                        $params_data = array('weixiu_status'=>5);
                    }
                    elseif($bill_type =="D" && $item_type == 'PF'){
                        $params_data = array('is_on_sale'=>11);
                    }
                    elseif($bill_type =="D" && $item_type == 'WX'){
                        $params_data = array('weixiu_status'=>2);
                    }
                    elseif($bill_type =="S" && $item_type =="LS"){
                        $params_data = array('is_on_sale'=>3,'order_detail_id'=>$goods['order_detail_id'],'order_sn'=>$bill_info['order_sn']);
                    }
                    elseif($bill_type =="D" && $item_type =="LS"){
                        $params_data = array('is_on_sale'=>2,'order_detail_id'=>0,'order_sn'=>'','warehouse_id'=>$bill_info['to_house_id'], 'warehouse'=>$warehouse_name);
                    }else{

                    }
                    if(!empty($params_data)){
                        $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($params_data);
                    }
                    $goods['goods_count'] = 1;
                    $goodsListAll[] = $goods;
                }
                $res = $this->table("erp_bill_goods")->insertAll($goodsListAll);
                if(!$res){
                    Log::record('create erp_bill_goods items failed: ' . json_encode($goods) . '-' . json_encode($goodsListAll), Log::ERR);
                    throw new Exception("单据创建明细失败！");
                }           
            }
            //更新单据编号
            $update_bill_info = array(
                'bill_no'=>$bill_no,
                'chengben_total'=>$chengben_total,
                'goods_total'=>$goods_total,
                'goods_num'=>$goods_num
            );
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($update_bill_info);
            if(!$res){
                throw new Exception("单据创建失败！3");
            }  
            //单据日志写入
            $bill_remark = "创建{$bill_type_name},单据编号:{$bill_no}";
            $bill_log_model = new erp_bill_logModel();
            $res = $bill_log_model->createBillLog($bill_id, $bill_remark);
            if(!$res){
                throw new Exception("单据日志发生异常！");
            }
            if($transMode===true){
               $this->commit();
            }
            $result['success'] = 1;
            $result['data'] = array('bill_id'=>$bill_id,'bill_no'=>$bill_no);
            
        }catch (Exception $e){
            $result['success'] = 0;            
            $result['msg'] = $e->getMessage();
            if($transMode===true){
                $this->rollback();
            }            
        }
        return $result;
    } 
    /**
     * 创建调拨单
     * @param unknown $bill_info
     * @param unknown $bill_goods_list
     * @param unknown $bill_type
     * @param string $transMode
     * @throws Exception
     * @return multitype:number string multitype: NULL multitype:unknown string
     */
    public function createBillM($bill_info,$bill_goods_list,$transMode=true){
        $result = array('success'=>0,'error'=>'','data'=>array()); 
        try{
            if($transMode===true){
               $this->beginTransaction();
            }
            
            $bill_type = "M";
            $bill_info['bill_no'] = uniqid();
            $bill_info['bill_type'] = $bill_type;
            $item_type = $bill_info['item_type'];
            $to_company_id = $bill_info['to_company_id'];
            
            $goods_items_model = new goods_itemsModel();
            $goods_log_model = new goods_items_logModel();
            
            $company_list = $goods_items_model->getCompanyList("id,company_name");
            $company_list = array_column($company_list,'company_name','id');
            $to_company_name = isset($company_list[$to_company_id])?$company_list[$to_company_id]:"";
            //插入单据信息
            $bill_id = $this->table("erp_bill")->insert($bill_info);
            if(!$bill_id){
                throw new Exception("单据创建失败！");
            }
            $bill_no = $this->createBillNo($bill_id, $bill_type);            
            $chengben_total = 0;
            $goods_total = 0;
            $goods_num = 0;
            $goods_itemid_arr = array();
            foreach ($bill_goods_list as $key=>&$goods){ 
                if(empty($goods['goods_itemid'])){
                    throw new Exception("调拨单货号不能为空！");
                }
                $goods_itemid = $goods['goods_itemid'];
                $goods['bill_no'] = $bill_no;
                $goods['bill_type'] = $bill_type;
                $goods['bill_id'] = $bill_id; 

                //所有单据 goods_itemid 不能为空
                if(empty($goods['goods_itemid'])){
                    throw new Exception("货号不能为空！");
                }
                //商品数据修改
                if($item_type == "WX"){
                    if($goods['is_on_sale'] == 3){
                        $editData = array(
                            'weixiu_status'=>6,
                            'weixiu_company_id'=>$to_company_id, 
                            'weixiu_company_name'=>$to_company_name                            
                        );
                        $log_remark = "申请售后维修调拨，单据编号:{$bill_no}";
                    }else{
                        $editData = array(
                            'is_on_sale'=>5,
                            'weixiu_status'=>6,
                            'weixiu_company_id'=>$to_company_id,
                            'weixiu_company_name'=>$to_company_name
                        );//调拨中
                        $log_remark = "申请库存维修调拨，单据编号:{$bill_no}";
                    }
                }else{
                    $editData = array('is_on_sale'=>5);//调拨中
                    $log_remark = "申请内部转仓调拨，单据编号:{$bill_no}";
                }
                
                $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($editData);
                unset($goods['is_on_sale']);           
                //成本，数量统计
                $goods_num +=1;
                $chengben_total += $goods['mingyichengben'];                    
                $goods_total += $goods['sale_price'];                    
                $goods['goods_count'] = 1;

                $res = $this->table("erp_bill_goods")->insert($goods);
                if(!$res){
                    Log::record('create erp_bill_goods items failed: ' . json_encode($goods) . '-' . json_encode($goodsListAll), Log::ERR);
                    throw new Exception("单据创建明细失败！");
                }  

                $log_data = array(
                    'goods_itemid'=>$goods_itemid,
                    'log_remark'=>$log_remark,
                    'log_user_id'=>$_SESSION['seller_id'],
                    'log_user_name'=>$_SESSION['seller_name'],
                    'log_type' =>$goods_log_model::LOG_TYPE_BILL_M,
                    'bill_no'=>$vo['bill_no'],
                );
                $res = $goods_log_model->addGoodsItemLog($log_data);
                if(!$res){
                    throw new Exception("新增货品日志失败！");
                }                
                
            }            
            //更新单据编号
            $update_bill_info = array(
                'bill_no'=>$bill_no,
                'chengben_total'=>$chengben_total,
                'goods_total'=>$goods_total,
                'goods_num'=>$goods_num,
            );
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($update_bill_info);
            if(!$res){
                throw new Exception("单据创建失败！");
            }  
            //单据日志写入
            $bill_remark = "创建单据:{$bill_no}";
            
            $bill_log_model = new erp_bill_logModel();
            $res = $bill_log_model->createBillLog($bill_id, $bill_remark);
            if(!$res){
                throw new Exception("单据日志发生异常！");
            }
            if($transMode===true){
               $this->commit();
            }            
            $result['success'] = 1;
            $result['data'] = array('bill_id'=>$bill_id,'bill_no'=>$bill_no);

        }catch (Exception $e){
            if($transMode===true){
                $this->rollback();
            }
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    } 
    /**
     * 创建盘点单
     * @param unknown $bill_info
     * @param unknown $bill_goods_list
     * @param unknown $bill_type
     * @param string $transMode
     * @throws Exception
     * @return multitype:number string multitype: NULL multitype:unknown string
     */
    public function createBillW($bill_info,$erp_bill_w_list,$transMode=true){
        $result = array('success'=>0,'error'=>'','data'=>array());
        try{
            if($transMode===true){
                $this->beginTransaction();
            }
            $bill_type = "W";
            $bill_info['bill_no'] = uniqid();
            $bill_info['bill_type'] = $bill_type;

            //插入单据信息
            $bill_id = $this->table("erp_bill")->insert($bill_info);
            if(!$bill_id){
                throw new Exception("单据创建失败！");
            }
            //调拨单附属表            
            foreach ($erp_bill_w_list as $erp_bill_w){
                $erp_bill_w['bill_id'] = $bill_id;
                $bill_w_id = $this->table("erp_bill_w")->insert($erp_bill_w);
                if(!$bill_w_id){
                    throw new Exception("单据创建失败！");
                }
            }            
            $warehouse_ids = array_column($erp_bill_w_list,"warehouse_id");
            $res = $this->table("erp_warehouse")->where(array('house_id'=>array('in',$warehouse_ids)))->update(array('erp_warehouse.`lock`'=>1));
            if(!$res){
                throw new Exception("锁定盘点仓库失败！");
            }
            //更新单据编号
            $bill_no = $this->createBillNo($bill_id, $bill_type);
            $tjRow = $this->table("goods_items")->field("sum(jijiachengben) as chengben_total,count(1) as goods_num")->where(array('warehouse_id'=>array('in',$warehouse_ids),'is_on_sale'=>2))->find();
            $update_bill_info = array(
                'bill_no'=>$bill_no,
                'chengben_total'=>sprintf("%.2f",$tjRow['chengben_total']),
                'goods_total'=>0,
                'goods_num'=>$tjRow['goods_num'],
            );
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($update_bill_info);
            if(!$res){
                throw new Exception("单据创建失败！");
            }
            
            $goods_list = $this->table("goods_items")->field("*")->where(array('warehouse_id'=>array('in',$warehouse_ids),'is_on_sale'=>2))->page(999999)->select();
            if(empty($goods_list)){
                throw new Exception("所选仓库没有库存商品！");
            }
            foreach ($goods_list as $goods){
                $bill_goods = array(
                    'bill_id'=>$bill_id,
                    'bill_no'=>$bill_no,
                    'bill_type'=>$bill_info['bill_type'],
                    'goods_itemid'=>$goods['goods_id'],
                    'goods_sn'=>$goods['goods_sn'],
                    'goods_name'=>$goods['goods_name'],
                    'from_house_id'=>$bill_info['from_house_id'],
                    'yuanshichengben'=>$goods['yuanshichengben'],
                    'mingyichengben'=>$goods['mingyichengben'],
                    'jijiachengben'=>$goods['jijiachengben'],
                    'management_fee'=>$goods['management_fee'],
                    'goods_count'=>1,
                    'goods_data'=>json_encode($goods,JSON_UNESCAPED_UNICODE),
                    'pandian_status'=>1,
                );
                $res = $this->table("erp_bill_goods")->insert($bill_goods);
                if(!$res){
                    throw new Exception("创建单据明细失败！");
                }
            }
            //单据日志写入
            $bill_remark = "创建单据：{$bill_no}";    
            $bill_log_model = new erp_bill_logModel();
            $res = $bill_log_model->createBillLog($bill_id, $bill_remark);
            if(!$res){
                throw new Exception("单据日志发生异常！");
            }
            if($transMode===true){
                $this->commit();
            }
            $result['success'] = 1;
            $result['data'] = array('bill_id'=>$bill_id,'bill_no'=>$bill_no);
    
        }catch (Exception $e){
            if($transMode===true){
                $this->rollback();
            }
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    /**
     * 创建退货返厂单
     * @param unknown $bill_info
     * @param unknown $bill_goods_list
     * @param unknown $bill_type
     * @param string $transMode
     * @throws Exception
     * @return multitype:number string multitype: NULL multitype:unknown string
     */
    public function createBillB($bill_info,$bill_goods_list,$transMode=true){
        $result = array('success'=>0,'error'=>'');
        try{
            if($transMode===true){
                $this->beginTransaction();
            }
            $bill_type = "B";
            $bill_info['bill_no'] = uniqid();
            $bill_info['bill_type'] = $bill_type;
            $bill_type_name = self::getBillType($bill_type);
            //插入单据信息
            $bill_id = $this->table("erp_bill")->insert($bill_info);
            if(!$bill_id){
                throw new Exception("单据创建失败！");
            }
            $bill_no = $this->createBillNo($bill_id, $bill_type);
            $chengben_total = 0;
            $goods_total = 0;
            $goods_num = 0;
            $goods_itemid_arr = array();
            foreach ($bill_goods_list as $key=>&$goods){
                $goods['bill_no'] = $bill_no;
                $goods['bill_type'] = $bill_type;
                $goods['bill_id'] = $bill_id;
    
                $goodsListAll = array();
                $goods_count = $goods['goods_count'];
                for($i=0;$i< $goods_count;$i++){
                    //所有单据 goods_itemid 不能为空
                    if(empty($goods['goods_itemid'])){
                        throw new Exception("货号不能为空！");
                    }
                    //商品数据修改[返厂中]
                    $editData = array('is_on_sale'=>8);
                    $this->table("goods_items")->where(array("goods_id"=>$goods['goods_itemid']))->update($editData);
        
                    //成本，数量统计
                    $goods_num +=1;
                    $chengben_total += $goods['yuanshichengben'];                    
                    $goods_total += $goods['sale_price'];
                    $goods['goods_count'] = 1;
                    $goodsListAll[] = $goods;
                }
                $res = $this->table("erp_bill_goods")->insertAll($goodsListAll);
                if(!$res){
                    Log::record('create erp_bill_goods items failed: ' . json_encode($goods) . '-' . json_encode($goodsListAll), Log::ERR);
                    throw new Exception("单据创建明细失败！");
                }
    
            }
            //更新单据编号
            $update_bill_info = array(
                'bill_no'=>$bill_no,
                'chengben_total'=>$chengben_total,
                'goods_total'=>$goods_total,
                'goods_num'=>$goods_num,
            );
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($update_bill_info);
            if(!$res){
                throw new Exception("单据创建失败！3");
            }
            //单据日志写入
            $bill_remark = "创建{$bill_type_name},单据编号:{$bill_no}";
            $bill_log_model = new erp_bill_logModel();
            $res = $bill_log_model->createBillLog($bill_id, $bill_remark);
            if(!$res){
                throw new Exception("单据日志发生异常！");
            }
            if($transMode===true){
                $this->commit();
            }
            $result['success'] = 1;
            $result['data'] = array('bill_id'=>$bill_id,'bill_no'=>$bill_no);
        }catch (Exception $e){
            if($transMode===true){
                $this->rollback();
            }
            $result['success'] = 0;
            $result['error'] = $e->getMessage();
        }
        return $result;
    }
    /**
     * 创建进货单
     * @param unknown $bill_info
     * @param unknown $bill_goods_list
     * @param unknown $bill_type
     * @param string $transMode
     * @throws Exception
     * @return multitype:number string multitype: NULL multitype:unknown string
     */
    public function createBillL($bill_info,$bill_goods_list,$transMode=true){
        try{
            $result = array('success'=>0,'error'=>'');
            if($transMode===true){
                $this->beginTransaction();
            }
            $bill_type = "L";
            $bill_info['bill_no'] = uniqid();
            $bill_info['bill_type'] = $bill_type;
            $bill_type_name = self::getBillType($bill_type);
            //插入单据信息
            $bill_id = $this->table("erp_bill")->insert($bill_info);
            if(!$bill_id){
                throw new Exception("单据创建失败！");
            }
            $bill_no = $this->createBillNo($bill_id, $bill_type);
            $chengben_total = 0;
            $goods_total = 0;
            $goods_num = 0;
            $goods_itemid_arr = array();
            foreach ($bill_goods_list as $key=>&$goods){
                $goods['bill_no'] = $bill_no;
                $goods['bill_type'] = $bill_type;
                $goods['bill_id'] = $bill_id;
    
                $goodsListAll = array();
                $goods_count = $goods['goods_count'];
                for($i=0;$i< $goods_count;$i++){                   
                    //成本，数量统计
                    $goods_num +=1;
                    $chengben_total += $goods['yuanshichengben'];
                    $goods_total += $goods['sale_price'];
                    $goods['goods_count'] = 1;
                    $goodsListAll[] = $goods;
                }
                $res = $this->table("erp_bill_goods")->insertAll($goodsListAll);
                if(!$res){
                    Log::record('create erp_bill_goods items failed: ' . json_encode($goods) . '-' . json_encode($goodsListAll), Log::ERR);
                    throw new Exception("单据创建明细失败！");
                }
    
            }
            //更新单据编号
            $update_bill_info = array(
                'bill_no'=>$bill_no,
                'chengben_total'=>$chengben_total,
                'goods_total'=>$goods_total,
                'goods_num'=>$goods_num,
            );
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($update_bill_info);
            if(!$res){
                throw new Exception("单据创建失败！3");
            }
            //单据日志写入
            $bill_remark = "创建{$bill_type_name},单据编号:{$bill_no}";
            $bill_log_model = new erp_bill_logModel();
            $res = $bill_log_model->createBillLog($bill_id, $bill_remark);
            if(!$res){
                throw new Exception("单据日志发生异常！");
            }
            if($transMode===true){
                $this->commit();
            }
            $result['success'] = 1;
            $result['data'] = array('bill_id'=>$bill_id,'bill_no'=>$bill_no);
            //$result = callback(true,"保存成功",$data);
        }catch (Exception $e){
            if($transMode===true){
                $this->rollback();
            }
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
            //$result = callback(false,$e->getMessage());
        }
        return $result;
    }
    /**
     * 单据审核 （自动分配审核入口）
     * @param unknown $bill_id
     * @param unknown $check_status
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function checkBill($bill_id,$check_status,$check_remark,$transMode=true){
        $result = array('success'=>0,'msg'=>'');
        try{
            if($transMode===true){
               $this->beginTransaction();
            }
            if(empty($_SESSION['store_id'])){
                throw new Exception("登录异常，store_id丢失！");
            }
            //查询单据(bill_id + store_id)
            $store_id = $_SESSION['store_id'];
            $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id));
            if(empty($bill_info)){
                throw new Exception("无效单据！");
            }
            $bill_type = strtoupper($bill_info['bill_type']);
            $item_type = strtoupper($bill_info['item_type']);
            $bill_status = $bill_info['bill_status'];
            $to_company_id = $bill_info['to_company_id'];
            if($bill_status != 1){
                if($bill_type=="D" && $item_type=="PF" && $to_company_id==$_SESSION['store_company_id']){
                     if($bill_status !=2){
                         throw new Exception("已审核的单据不能再次审核！");
                     }
                }else{
                    throw new Exception("已审核的单据不能再次审核！");
                }
                
            }
            $lockFlag = true;//是否检查 仓库锁定
            if($bill_type == 'W' || ($bill_type =='L' && $check_status==2)){
                $lockFlag = false;
            }
            if($lockFlag){
                $warehouseLocks = $this->getBillLockWarhouseList($bill_id);
                $fromWarehouseLocks = array_column($warehouseLocks,"from_house_name");
                $fromWarehouseLocks = implode("】【", array_unique($fromWarehouseLocks));
                $toWarehouseLocks = array_column($warehouseLocks,"to_house_name");
                $toWarehouseLocks = implode("】【", array_unique($toWarehouseLocks));
                if(!empty($fromWarehouseLocks)){
                    throw new Exception("【".$fromWarehouseLocks."】 出库仓库正在盘点中！");
                }else if(!empty($toWarehouseLocks)){
                    throw new Exception("【".$toWarehouseLocks."】 入库仓库正在盘点中！");
                }
            }
            
            if($bill_type=="L"){
                $result = $this->checkBillL($bill_id,$check_status,$bill_info);
            }else if($bill_type=="S"){
                $result = $this->checkBillS($bill_id,$check_status,$bill_info);
            }else if($bill_type=="C"){
                $result = $this->checkBillC($bill_id,$check_status,$bill_info);
            }else if($bill_type=="D"){
                $result = $this->checkBillD($bill_id,$check_status,$bill_info);
            }else if($bill_type=="B"){
                $result = $this->checkBillB($bill_id,$check_status,$bill_info);
            }else if($bill_type=="M"){
                $result = $this->checkBillM($bill_id,$check_status,$bill_info);
            }else if($bill_type=="W"){
                $result = $this->checkBillW($bill_id,$check_status,$bill_info);
            }else{
               throw new Exception("单据类型{$bill_type}不支持此项操作:");
            }
            if($result['success']==0){
                throw new Exception($result['msg']);
            }
            
            //单据日志写入
            if($check_status==1){
               $bill_remark = "审核通过:{$check_remark}";
            }else{
               $bill_remark = "审核不通过:{$check_remark}";
            }
            $bill_log_model = new erp_bill_logModel();
            $res = $bill_log_model->createBillLog($bill_id, $bill_remark);
            if(!$res){
                throw new Exception("单据日志发生异常！");
            }
            
            if($transMode===true){
                $this->commit();
            }
            $result['success'] = 1;

            if (in_array($bill_type, ['D','M'])) {
                /*
                 * 批发退货单D审核到总部
                 */
                if ($bill_info['item_type'] == 'PF') {
                    EventEmitter::dispatch("erp", array('event' => 'sync_bill', 'bill_id' => $bill_id, 'bill_no' => $bill_info['bill_no']));
                }else if($bill_info['item_type']=="WX"){
                    // 维修调拨单M到总部
                    EventEmitter::dispatch("erp", array('event' => 'sync_bill', 'bill_id' => $bill_id, 'bill_no' =>$bill_info['bill_no']));
                }
            }

        }catch (Exception $e){            
            $result['success'] = 0;            
            $result['msg'] = $e->getMessage();
            if($transMode===true){
                $this->rollback();
            }
        }
        return $result;
    }

    /**
     * 批发销售单签收
     * @param unknown $bill_id
     * @param unknown $warehouse_id
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function checkSignBill($bill_id,$warehouse_id,$transMode=true){
        $result = array('success'=>0,'msg'=>'');
        try{
            if($transMode===true){
               $this->beginTransaction();
            }
            if(empty($_SESSION['store_id'])){
                throw new Exception("登录异常，store_id丢失！");
            }
            //查询单据(bill_id + store_id)
            $store_id = $_SESSION['store_id'];
            $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id));//,'to_company_id'=>$store_id
            if(empty($bill_info)){
                throw new Exception("无效单据！");
            }
            $bill_type = strtoupper($bill_info['bill_type']);
            $item_type = strtoupper($bill_info['item_type']);
            $to_company_id = $bill_info['to_company_id'];
            
            if($bill_info['bill_status']!=2){
                throw new Exception("已审核的单据才能签收！");
            }          

            $goods_total = $this->table("erp_bill_goods")->where(array('bill_id'=>$bill_id))->count();
            
            //$bill_goods = $this->table("erp_bill_goods")->page($goods_total)->where(array('bill_id'=>$bill_id))->select();
            $bill_goods = $this->table("erp_bill_goods,goods_items")->join("left")->on("erp_bill_goods.goods_itemid=goods_items.goods_id")->field("erp_bill_goods.id,goods_itemid,from_house_id,from_box_id,bill_no,is_on_sale")->page($goods_total)->where(array('bill_id'=>$bill_id))->select();
            
            
            $warehouse_model = new erp_warehouseModel();
            $company_model = new companyModel();
            
            $company_name =$company_model->getCompanyInfo(array('id'=>$to_company_id));
            $house_name =$warehouse_model->getWareHouseInfo(array('house_id'=>$warehouse_id));

            //更新单据状态$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP)
            $update_bill_info = array(
                'bill_status'=>4,
                'sign_user'=>$_SESSION['seller_name'],//签收人
                'sign_time'=>date('Y-m-d H:i:s',TIMESTAMP),//签收状态
                'to_house_id'=>$warehouse_id,
                'to_store_id'=>$store_id,
            );
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($update_bill_info);
            if(!$res){
                throw new Exception("更新单据状态失败！");
            }
            //更新货品信息
            foreach ($bill_goods as $key=>$goods){
                $goods_itemid = $goods['goods_itemid'];                
                //商品数据修改
                $editData = array(
                    'is_on_sale'=>2,
                    'company'=>$company_name['company_name'],
                    'company_id'=>$to_company_id,
                    'warehouse'=>$house_name['name'],
                    'warehouse_id'=>$warehouse_id,
                    'store_id'=>$store_id
                );                
                if($bill_type == 'M' && $item_type=="WX"){                   
                    $editData = array(
                        //维修调拨只更新维修仓库
                       // 'warehouse'=>$house_name['name'],
                       // 'warehouse_id'=>$warehouse_id,
                        'weixiu_company_id'=>$bill_info['to_company_id'],
                        'weixiu_warehouse_id'=>$warehouse_id,
                        'weixiu_warehouse_name'=>$house_name['name']
                    );
                    if($goods['is_on_sale']==3){
                        //售后
                        $editData['is_on_sale'] = 3;//已销售
                        $editData['weixiu_status'] = 3;//维修受理
                    }else{
                        //售前
                        $editData['is_on_sale'] = 2;//库存
                        $editData['weixiu_status'] = 4;//维修完成
                    }
                }else if($bill_type == 'D' && $item_type=="PF"){
                    //取货号最后一个p单的成本价
                    $pinfo = $this->getBillGooodsInfoP($goods_itemid);
                    if(empty($pinfo)){
                        throw new Exception("{$goods_itemid}货号没有批发销售单！");
                    }
                    //$chengbenjia = $pinfo['yuanshichengben'];//原始成本价
                    $put_in_type = isset($pinfo['goods_data']['put_in_type']) ? $pinfo['goods_data']['put_in_type'] : '1';
                    $prc_id = isset($pinfo['goods_data']['prc_id']) ? $pinfo['goods_data']['prc_id'] : '58';
                    $prc_name = isset($pinfo['goods_data']['prc_name']) ? $pinfo['goods_data']['prc_name'] : '总公司';
                    $management_fee = isset($pinfo['goods_data']['management_fee']) ? $pinfo['goods_data']['management_fee'] : 0;
                    $dataExt = array("prc_id"=>$prc_id,"prc_name"=>$prc_name,"put_in_type"=>$put_in_type,"yuanshichengbenjia"=>$pinfo['yuanshichengben'], "mingyichengben"=>$pinfo['mingyichengben'], "jijiachengben"=>$pinfo['jijiachengben'], 'management_fee' => $management_fee);//更新成本价
                    $editData = array_merge($editData,$dataExt);
                }
                $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($editData);
                if(!$res){
                    throw new Exception("更新货品信息失败！");
                }

                //修改单据明细
                if(($bill_type == 'S' && $item_type=="PF") || ($bill_type == 'M' && $item_type == "WX")){
                    $editBillGoods = array('to_store_id'=>$store_id,'to_house_id'=>$warehouse_id);
                }

                if(!empty($editBillGoods)){
                    $res = $this->table("erp_bill_goods")->where(array("id"=>$goods['id']))->update($editBillGoods);
                    if(!$res){
                        throw new Exception("更新单据明细失败！");
                    }
                }
            }
            //单据日志写入
            $bill_remark = "单据签收";
            $bill_log_model = new erp_bill_logModel();
            $res = $bill_log_model->createBillLog($bill_id, $bill_remark);
            if(!$res){
                throw new Exception("单据日志发生异常！");
            }
            
            if($transMode===true){
                $this->commit();
            }
            $result['success'] = 1;
      
            // 同步P,M单签收状态到erp
            EventEmitter::dispatch("erp", array('event' => 'sync_bill', 'bill_id' => $bill_id, 'bill_no' => $bill_info['bill_no'], 'ishop_sign' => 1));
        }catch (Exception $e){            
            $result['success'] = 0;            
            $result['msg'] = $e->getMessage();
            if($transMode===true){
                $this->rollback();
            }
        }
        return $result;
    }
    /**
     * 进货单或其他进货单 审核
     * @param unknown $bill_id
     * @return multitype:number string
     */
    private function checkBillL($bill_id,$check_status,$bill_info=array()){
        $result = array('success'=>0,'msg'=>'');
        try{
            $store_id = $_SESSION['store_id'];
            if(empty($bill_info)){
               $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id,'store_id'=>$store_id));
            }
            $bill_type = $bill_info['bill_type'];
            if($check_status==2){
                $updata = array('bill_status'=>3,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($updata);
                if($res===false){
                    throw new Exception("取消单据失败！");
                }
            }else{
                //更改单据为已审核状态
                $updata = array('bill_status'=>2,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($updata);
                if($res===false){
                    throw new Exception("更改已审核状态失败！");
                }
                $bill_goods = $this->table("erp_bill_goods")->page(9999)->where(array('bill_id'=>$bill_id))->select();
                $goods_log_model = new goods_items_logModel();               

                $goods_model = new goods_itemsModel();
                $house_list = $goods_model->getWareHouseList("house_id,name",array('company_id' =>$_SESSION['store_company_id']));
                $house_list = empty($house_list) ? [] : array_column($house_list, 'name','house_id');
                //$box_list = $goods_model->getBoxList("box_id,box_name",array('erp_warehouse.store_id'=>$_SESSION['store_id']));
                //$box_list = empty($box_list)?[]: array_column($box_list,"box_name",'box_id');
                $company_list = $goods_model->getCompanyList("id,company_name");
                $company_list = empty($company_list)?[]: array_column($company_list,"company_name",'id');
                //$supplier_list = $goods_model->getSupplierList("sup_id,sup_name",array());
                //$supplier_list = empty($supplier_list)?[]: array_column($supplier_list,"sup_name",'sup_id');
                
                $goods_storage_array = array();
                foreach ($bill_goods as $key=>$goods){
                    $goods_data = json_decode($goods['goods_data'],true);
                    if(empty($goods_data) ||  !is_array($goods_data)){
                        throw new Exception("数据异常，请取消单据！");
                    }
                    $goods = array_merge($goods_data,$goods);
                    $bill_goods_id = $goods['id'];
                    $bill_no = $goods['bill_no'];
                    if(!empty($house_list[$goods['to_house_id']])){
                        $warehouse_name = $house_list[$goods['to_house_id']];
                    }else{
                        $warehouse_name = "";
                    }                    
                    if(!empty($company_list[$goods['to_company_id']])){
                        $company_name = $company_list[$goods['to_company_id']];
                    }else{
                        $company_name = "";
                    }
                    $supplier_id = $bill_info['supplier_id'];
                    if(!empty($company_list[$supplier_id])){
                        $supplier_name = $company_list[$supplier_id];
                    }else{
                        $supplier_name = "";
                    }
                    $box_name = $goods['to_box_id'];
                    $goods_item = array(
                        "goods_id"=>"5".date("His").rand(100000,999999).($key+1),
                        "goods_sn"=>$goods['goods_sn'],
                        "product_type"=>$goods['product_type'],
                        "cat_type"=>$goods['cat_type'],
                        "is_on_sale"=>2,
                        "prc_id"=>$bill_info['supplier_id'],
                        "prc_name"=>$supplier_name,
                        "put_in_type"=>$goods['in_warehouse_type'],
                        "goods_name"=>$goods['goods_name'],
                        "company"=>$company_name,
                        "warehouse"=>$warehouse_name,
                        "company_id"=>$goods['to_company_id'],
                        "warehouse_id"=>$goods['to_house_id'],
                        "box_sn"=>$box_name,
                        "caizhi"=>$goods['caizhi'],
                        "jinse"=>$goods['jinse'],
                        "jinzhong"=>$goods['jinzhong'],
                        "jinhao"=>$goods['jinhao'],
                        "zongzhong"=>$goods['zongzhong'],
                        "shoucun"=>$goods['shoucun'],
                        "order_sn"=>$goods['order_sn'],
                        "buchan_sn"=>$goods['buchanhao'],
                        "pinpai"=>$goods['pinpai'],
                        "changdu"=>$goods['changdu'],
                        "zhengshuhao"=>$goods['zhengshuhao'],
                        "zhengshuhao2"=>$goods['zhengshuhao2'],
                        "peijianshuliang"=>$goods['peijianshuliang'],
                        "guojizhengshu"=>$goods['guojizhengshu'],
                        "zhengshuleibie"=>$goods['zhengshuleibie'],
                        "gemx_zhengshu"=>$goods['gemx_zhengshu'],
                        "num"=>1,
                        "yanse"=>$goods['yanse'],
                        "jingdu"=>$goods['jingdu'],
                        "qiegong"=>$goods['qiegong'],
                        "paoguang"=>$goods['paoguang'],
                        "duichen"=>$goods['duichen'],
                        "yingguang"=>$goods['yingguang'],
                        "zuanshizhekou"=>$goods['zuanshizhekou'],                        
                        "guojibaojia"=>$goods['guojibaojia'], 
                        "luozuanzhengshu"=>$goods['luozuanzhengshu'], 
                        "tuo_type"=>$goods['tuo_type'],
                        //"huopin_type"=>$goods['tuo_type'],
                        //"dia_sn"=>,
                        //"biaoqianjia"=>,
                        "jietuoxiangkou"=>$goods['jietuoxiangkou'],                        
                        //"jiejia"=>,
                        "color_grade"=>$goods['color_grade'],
                        "zhushi"=>$goods['zhushi'],
                        "zhushilishu"=>$goods['zhushilishu'],
                        "zuanshidaxiao"=>$goods['zuanshidaxiao'],
                        "zhushizhongjijia"=>$goods['zhushizhongjijia'],
                        "zhushiyanse"=>$goods['zhushiyanse'],
                        "zhushijingdu"=>$goods['zhushijingdu'],
                        "zhushiqiegong"=>$goods['zhushiqiegong'],
                        "zhushixingzhuang"=>$goods['zhushixingzhuang'],
                        "zhushibaohao"=>$goods['zhushibaohao'],
                        "zhushiguige"=>$goods['zhushiguige'],
                        "zhushitiaoma"=>$goods['zhushitiaoma'],
                        "fushi"=>$goods['fushi'],
                        "fushilishu"=>$goods['fushilishu'],
                        "fushizhong"=>$goods['fushizhong'],
                        "fushizhongjijia"=>$goods['fushizhongjijia'],
                        "fushibaohao"=>$goods['fushibaohao'],
                        "fushiguige"=>$goods['fushiguige'],
                        "fushiyanse"=>$goods['fushiyanse'],
                        "fushijingdu"=>$goods['fushijingdu'],
                        "fushixingzhuang"=>$goods['fushixingzhuang'],
                        "shi2"=>$goods['shi2'],
                        "shi2lishu"=>$goods['shi2lishu'],
                        "shi2zhong"=>$goods['shi2zhong'],
                        "shi2zhongjijia"=>$goods['shi2zhongjijia'],
                        "shi2baohao"=>$goods['shi2baohao'],
                        "shi3"=>$goods['shi3'],
                        "shi3lishu"=>$goods['shi3lishu'],
                        "shi3zhong"=>$goods['shi3zhong'],
                        "shi3zhongjijia"=>$goods['shi3zhongjijia'],
                        "shi3baohao"=>$goods['shi3baohao'],
                        "yuanshichengbenjia"=>$goods['yuanshichengben'],
                        "mingyichengben"=>$goods['mingyichengben'],
                        "jijiachengben"=>$goods['jijiachengben'],
                        "store_id"=>$goods['to_store_id'],
                        "addtime"=>date("Y-m-d H:i:s"),
                        'is_shopzc'=>1,
                    );
                    //print_r($goods_item);
                    $insert_id = $this->table("goods_items")->insert($goods_item);
                    if(!$insert_id){
                        throw new Exception("新增库存失败！");
                    }
                    $goods_itemid = $this->createGoodsItemid($insert_id,"5");
                    $res = $this->table("goods_items")->where(array('id'=>$insert_id))->update(array('goods_id'=>$goods_itemid));
                    if(!$res){
                        throw new Exception("创建货号失败1！");
                    }
                    //更新erp_bill_goods表货号
                    $res = $this->table("erp_bill_goods")->where(array('id'=>$bill_goods_id))->update(array('goods_itemid'=>$goods_itemid));
                    if(!$res){
                        throw new Exception("创建货号失败2！");
                    }
                    $log_remark = "自采进货，单据编号：".$bill_no;
                    $log_data = array(
                        'goods_itemid'=>$goods_itemid,
                        'log_remark'=>$log_remark,
                        'log_user_id'=>$_SESSION['seller_id'],
                        'log_user_name'=>$_SESSION['seller_name'],
                        'log_type' =>$goods_log_model::LOG_TYPE_BILL_L,
                        'bill_no'=>$vo['bill_no'],
                    );
                    $res = $goods_log_model->addGoodsItemLog($log_data);
                    if(!$res){
                        throw new Exception("新增货品日志失败！");
                    }
                }               
                
            }
          
            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    /**
     * M 调拨单 审核
     * @param unknown $bill_id
     * @return multitype:number string
     */
    private function checkBillM($bill_id,$check_status,$bill_info=array()){
        $result = array('success'=>0,'msg'=>'');
        try{
            $store_id = $_SESSION['store_id'];
            if(empty($bill_info)){
                $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id));
            }
            //更改单据状态
            if($check_status==2){
                //更改单据状态：已取消3
                $bill_data = array('bill_status'=>3,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
            }else{
                //更改单据状态：已审核2
                $bill_data = array('bill_status'=>2,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                /*if($bill_info['from_company_id'] == 58){
                    $bill_data['bill_status'] = 4;//已签收
                }*/
            }
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($bill_data);
            if($res===false){
                throw new Exception("更新单据状态出现异常！");
            }
            //$bill_goods = $this->table("erp_bill_goods")->field("goods_itemid,from_house_id,from_box_id,bill_no")->page(10000)->where(array('bill_id'=>$bill_id))->select();
            $bill_goods = $this->table("erp_bill_goods,goods_items")->join("left")->on("erp_bill_goods.goods_itemid=goods_items.goods_id")->field("goods_itemid,from_house_id,to_house_id,from_box_id,bill_no,is_on_sale")->page($goods_total)->where(array('bill_id'=>$bill_id))->select();
            
            $goods_log_model = new goods_items_logModel();
            if($check_status == 2){
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("调拨单货号为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];
                    $goods_item = array();
                    
                    if($bill_info['item_type']=="WX"){
                        if($vo['is_on_sale'] ==5){
                            $goods_item['is_on_sale'] = 2;//库存    
                            $goods_item['weixiu_status'] = 0;//维修取消                            
                        }else{
                            $goods_item['weixiu_status'] = 3;//维修受理
                        }
                        $goods_item['weixiu_company_id'] = 0;
                        $goods_item['weixiu_company_name'] = "";
                    }else{
                        $goods_item['is_on_sale'] = 2;//库存
                    }
                                        
                    $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                    if(!$res){
                        throw new Exception("货号{$goods_itemid}还原货品状态失败！");
                    }
                    $log_remark = "取消维修调拨，单据编号：".$vo['bill_no'];
                    $log_data = array(
                        'goods_itemid'=>$goods_itemid,
                        'log_remark'=>$log_remark,
                        'log_user_id'=>$_SESSION['seller_id'],
                        'log_user_name'=>$_SESSION['seller_name'],
                        'log_type' =>$goods_log_model::LOG_TYPE_BILL_M,
                        'bill_no'=>$vo['bill_no'],
                    );
                    $res = $goods_log_model->addGoodsItemLog($log_data);
                    if(!$res){
                        throw new Exception("新增货品日志失败！");
                    }
                }

            }else{

                $warehouse_model = new erp_warehouseModel();
                $company_model = new companyModel();
                $box_model = new erp_boxModel();
                
                $company_list = $company_model->getCompanyList(array(),9999);
                $company_list = empty($company_list)?[]: array_column($company_list,"company_name",'id');
                $house_list = $warehouse_model->getWareHouseList(array(), '*', 9999);
                $house_list = empty($house_list) ? [] : array_column($house_list, 'name','house_id');
                
    
                //批量更新库存表
                foreach ($bill_goods as $key=>$vo){               
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("调拨单货号为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];  
                    $from_house_name = $house_list[$vo['from_house_id']];
                    $from_box_name = $vo['from_box_id']; 

                    $to_company = $company_list[$bill_info['to_company_id']];
                    $to_house_name = $house_list[$bill_info['to_house_id']];
                    $to_box_name = $bill_info['to_box_id'];
                    
                    if($bill_info['item_type']=="ZC"){                      
                        $goods_item = array(
                            'store_id'=>$bill_info['to_store_id'],
                            'warehouse_id'=>$bill_info['to_house_id'],
                            'warehouse'=>$to_house_name,
                            'company_id'=>$bill_info['to_company_id'],
                            'company'=>$to_company,
                            'is_on_sale'=>2//库存
                        );
                        $log_remark = "内部转仓调拨完成，单据编号：".$vo['bill_no'];                                             
                    }else{ 
                        if($bill_info['to_company_id'] != 58){                       
                            $goods_item = array(
                                'warehouse_id'=>$bill_info['to_house_id'],
                                'warehouse'=>$to_house_name, 
                            ); 
                        }                       
                        $log_remark = "维修调拨审核通过，单据编号：".$vo['bill_no'];
                    }
                    if(!empty($goods_item)){
                        $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                        if(!$res){
                            throw new Exception("货号{$goods_itemid}变更货品失败！");
                        }
                        $log_data = array(
                            'goods_itemid'=>$goods_itemid,
                            'log_remark'=>$log_remark,
                            'log_user_id'=>$_SESSION['seller_id'],
                            'log_user_name'=>$_SESSION['seller_name'],
                            'log_type' =>$goods_log_model::LOG_TYPE_BILL_M,
                            'bill_no'=>$vo['bill_no'],
                        );
                        $res = $goods_log_model->addGoodsItemLog($log_data);
                        if(!$res){
                            throw new Exception("新增货品日志失败！");
                        }
                    }
                    
                }
            }
            
            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    
    /**
     * M 盘点单 审核
     * @param unknown $bill_id
     * @return multitype:number string
     */
    private function checkBillW($bill_id,$check_status,$bill_info=array()){
        $result = array('success'=>0,'msg'=>'');
        try{
            $store_id = $_SESSION['store_id'];
            if(empty($bill_info)){
                $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id));
            }            
            //更改单据状态
            if($check_status==2){
                //更改单据状态：已取消3
                $bill_data = array('bill_status'=>3,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                if($bill_info['item_status']==0){
                    $sql = "update erp_warehouse w inner join erp_bill_w bw on w.house_id=bw.warehouse_id set w.`lock`=0 where bw.bill_id={$bill_id}";
                    $res = DB::query($sql);
                    if(!$res){
                        throw new Exception("仓库解锁失败！");
                    }
                }
            }else{
                if($bill_info['item_status']!=1){
                    throw new Exception("盘点未结束，不能审核通过！");
                }
                //更改单据状态：已审核2
                $bill_data = array('bill_status'=>2,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
            }
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($bill_data);
            if($res===false){
                throw new Exception("更新单据状态出现异常！");
            }                       
            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    /**
     * 退货返厂单 审核
     * @param unknown $bill_id
     * @param unknown $check_status
     * @param unknown $bill_info
     * @throws Exception
     * @return multitype:number string |multitype:number string NULL
     */
    private function checkBillB($bill_id,$check_status,$bill_info=array()){
        $result = array('success'=>0,'msg'=>'');
        try{
            $store_id = $_SESSION['store_id'];
            if(empty($bill_info)){
                $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id,'store_id'=>$store_id));
            }
            //更改单据状态
            if($check_status==2){
                //更改单据状态：已取消3
                $bill_data = array('bill_status'=>3,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
            }else{
                //更改单据状态：已审核2
                $bill_data = array('bill_status'=>2,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
            }
            $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($bill_data);
            if($res===false){
                throw new Exception("更新单据状态出现异常！");
            }
            $bill_goods = $this->table("erp_bill_goods")->field("goods_itemid,bill_no")->page(10000)->where(array('bill_id'=>$bill_id))->select();
            $goods_log_model = new goods_items_logModel();
            //取消单据逻辑
            if($check_status == 2){
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("调拨单货号为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];
                    $goods_item = array(
                        'is_on_sale' =>2 
                    );                    
                    $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                    if(!$res){
                        throw new Exception("货号{$goods_itemid}还原货品状态失败！");
                    }
                    $log_remark = "取消退货返厂，货品状态变更为 库存，单据编号：".$vo['bill_no'];
                    $log_data = array(
                        'goods_itemid'=>$goods_itemid,
                        'log_remark'=>$log_remark,
                        'log_user_id'=>$_SESSION['seller_id'],
                        'log_user_name'=>$_SESSION['seller_name'],
                        'log_type' =>$goods_log_model::LOG_TYPE_BILL_B,
                        'bill_no'=>$vo['bill_no'],
                    );
                    $res = $goods_log_model->addGoodsItemLog($log_data);
                    if(!$res){
                        throw new Exception("新增货品日志失败！");
                    }
                }
            }else{                
                //批量更新库存表
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("货号不能为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];                                
                    $goods_item = array(
                        'is_on_sale'=>9,//已返厂
                    );   
                    $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                    if(!$res){
                        throw new Exception("货号{$goods_itemid}库存状态更新失败！");
                    }
                    
                    $log_remark = "完成退货返厂，货品状态变更为 已返厂，单据编号：".$vo['bill_no'];
                    $log_data = array(
                        'goods_itemid'=>$goods_itemid,
                        'log_remark'=>$log_remark,
                        'log_user_id'=>$_SESSION['seller_id'],
                        'log_user_name'=>$_SESSION['seller_name'],
                        'log_type' =>$goods_log_model::LOG_TYPE_BILL_B,
                        'bill_no'=>$vo['bill_no'],
                    );
                    $res = $goods_log_model->addGoodsItemLog($log_data);
                    if(!$res){
                        throw new Exception("新增货品日志失败！");
                    }
        
                }
            }
            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    /**
     * 其他出库单C 审核
     * @param unknown $bill_id
     * @return multitype:number string
     */
    private function checkBillC($bill_id,$check_status,$bill_info=array()){
        $result = array('success'=>0,'msg'=>'');
        try{
            $store_id = $_SESSION['store_id'];
            if(empty($bill_info)){
                $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id,'store_id'=>$store_id));
            }
            
            $goods_log_model = new goods_items_logModel();
            $bill_goods = $this->table("erp_bill_goods")->field("goods_itemid,bill_no")->page(10000)->where(array('bill_id'=>$bill_id))->select();
            
            //审核取消
            if($check_status==2){
                $updata = array('bill_status'=>3,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($updata);
                if($res===false){
                    throw new Exception("取消单据失败！");
                }
                //单据商品+库存商品 连表查询
                $bill_goods = $this->table("erp_bill_goods,goods_items")->join("left")->on("erp_bill_goods.goods_itemid=goods_items.goods_id")->field("bill_no,goods_count,goods_itemid")->page(9999)->where(array('bill_id'=>$bill_id))->select();
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("出库单货号不能为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];//货号
                    $goods_item = array("is_on_sale"=>2);//单据取消货品更新为库存                    
                    $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                    if(!$res){
                        throw new Exception("货号{$goods_itemid}还原货品状态失败！");
                    }
                    
                    $log_remark = "取消其它退货单，货品状态变更为库存，单据编号：".$vo['bill_no'];
                    $log_data = array(
                        'goods_itemid'=>$goods_itemid,
                        'log_remark'=>$log_remark,
                        'log_user_id'=>$_SESSION['seller_id'],
                        'log_user_name'=>$_SESSION['seller_name'],
                        'log_type' =>$goods_log_model::LOG_TYPE_BILL_C,
                        'bill_no'=>$vo['bill_no'],
                    );
                    $res = $goods_log_model->addGoodsItemLog($log_data);
                    if(!$res){
                        throw new Exception("新增货品日志失败！");
                    }
                }

            }else{
                //审核通过
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("货号不能为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];
                    $goods_item = array(
                        'is_on_sale'=>3,//已销售
                    );
                    $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                    if(!$res){
                        throw new Exception("货号{$goods_itemid}库存状态更新失败！");
                    }
                
                    $log_remark = "完成其他出库，货品状态变更为 已销售，单据编号：".$vo['bill_no'];
                    $log_data = array(
                        'goods_itemid'=>$goods_itemid,
                        'log_remark'=>$log_remark,
                        'log_user_id'=>$_SESSION['seller_id'],
                        'log_user_name'=>$_SESSION['seller_name'],
                        'log_type' =>$goods_log_model::LOG_TYPE_BILL_B,
                        'bill_no'=>$vo['bill_no'],
                    );
                    $res = $goods_log_model->addGoodsItemLog($log_data);
                    if(!$res){
                        throw new Exception("新增货品日志失败！");
                    }
                
                }
                
            }    
            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    /**
     * 销售出库单S OR 其他出库单C 审核
     * @param unknown $bill_id
     * @return multitype:number string
     */
    private function checkBillS($bill_id,$check_status,$bill_info=array()){
        $result = array('success'=>0,'msg'=>'');
        try{
            $goods_log_model= new goods_items_logModel();
            $store_id = $_SESSION['store_id'];
            if(empty($bill_info)){
                $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id,'store_id'=>$store_id));
            }
            $bill_type = $bill_info['bill_type'];
            $item_type = $bill_info['item_type'];
            $pifa_type = $bill_info['pifa_type'];
            $company_id = $bill_info['to_company_id'];
            $put_in_type = $bill_info['in_warehouse_type'];
            $goods_items_model = new goods_itemsModel();
            $company_list=$goods_items_model->getCompanyList("id,company_name");
            $company_list = array_column($company_list,'company_name','id');
            $company_name = isset($company_list[$company_id])?$company_list[$company_id]:"";
            $from_company_id = $bill_info['from_company_id'];
            $from_company_name = isset($company_list[$from_company_id])?$company_list[$from_company_id]:"";;
           
            if($check_status==2){
                $updata = array('bill_status'=>3,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($updata);
                if($res===false){
                    throw new Exception("取消单据失败！");
                }
                if($bill_type == "S"){
                    //单据商品+库存商品 连表查询
                    $bill_goods = $this->table("erp_bill_goods,goods_items")->join("left")->on("erp_bill_goods.goods_itemid=goods_items.goods_id")->field("bill_no,goods_count,goods_itemid")->page(9999)->where(array('bill_id'=>$bill_id))->select();
                    foreach ($bill_goods as $key=>$vo){
                        if(empty($vo['goods_itemid'])){
                            throw new Exception("出库单货号不能为空！");
                        }
                        $goods_itemid = $vo['goods_itemid'];//货号
                        //更新货品状态 所在公司
                        if($item_type == "PF"){
                            $goods_item = array("is_on_sale"=>2);//单据取消货品更新为库存
                            $log_remark = "取消销售出库单[批发]，货品状态变还原为 库存，单据编号：".$vo['bill_no'];
                        }else{
                            $goods_item = array("weixiu_status"=>3);
                            $log_remark = "取消维修出库单，货品维修状态还原为维修受理，单据编号：".$vo['bill_no'];
                        }
                        $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                        if(!$res){
                            throw new Exception("货号{$goods_itemid}更新商品信息失败！");
                        }
                        $log_data = array(
                            'goods_itemid'=>$goods_itemid,
                            'log_remark'=>$log_remark,
                            'log_user_id'=>$_SESSION['seller_id'],
                            'log_user_name'=>$_SESSION['seller_name'],
                            'log_type' =>$goods_log_model::LOG_TYPE_BILL_S,
                            'bill_no'=>$vo['bill_no'],
                        );
                        $res = $goods_log_model->addGoodsItemLog($log_data);
                        if(!$res){
                            throw new Exception("新增货品日志失败！");
                        }
                        //$this->addGoodsItemLog($goods_itemid,$bill_type,$vo["bill_no"]);
                    }
                }
            }else{
                //更改单据为已审核状态 更新单据信息
                $updata = array('bill_status'=>2,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                if($bill_type == "S" && $item_type == "PF"){
                    //单据审核时：出库类型是【购买】，货品的门店结算方式默认是【已结算】，结算操作时间为单据的审核时间，结算人默认审核人；出库类型是【借货】，门店结算方式是【未结算】出库类型是【借货】，参照【批发销售门店结算】
                    //paramsHelper::echoOptionText();
                    if($put_in_type == 1){//购买
                        $is_settled = 1;//已结算
                        $settle_time = date('Y-m-d H:i:s',TIMESTAMP);
                        $settle_user = $_SESSION['seller_name'];
                    }elseif($put_in_type == 2){//借货
                        $is_settled = 0;//未结算
                        $settle_time = "0000-00-00 00:00:00";
                        $settle_user = "";
                    }else{
                        $is_settled = 0;
                        $settle_time = "0000-00-00 00:00:00";
                        $settle_user = "";
                    }
                    //$updata['is_settled'] = $is_settle;
                    //$updata['settle_time'] = $settle_time;
                    //$updata['settle_user'] = $settle_user;
                }
                $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($updata);
                if($res===false){
                    throw new Exception("更改已审核状态失败！");
                }                
                //单据商品+库存商品 连表查询
                $bill_goods = $this->table("erp_bill_goods,goods_items")->join("left")->on("erp_bill_goods.goods_itemid=goods_items.goods_id")->field("bill_no,goods_count,goods_itemid, sale_price,erp_bill_goods.management_fee")->page(9999)->where(array('bill_id'=>$bill_id))->select();
                $goods_storage_array = array();
                $erp_bill_goodsModel = new erp_bill_goodsModel();
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("出库单货号不能为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];//货号
                    $bill_no = $vo['bill_no'];
                    if($item_type == "PF"){
                        $res = $erp_bill_goodsModel->editErpBillGoods(array('bill_no'=>$bill_no,'goods_itemid'=>$goods_itemid),array('is_settled'=>$is_settled, 'settle_user'=>$settle_user, 'settle_time'=>$settle_time));
                        if(!$res){
                            throw new Exception("货号{$goods_itemid}更新结算信息失败！");
                        }
                        //更新货品状态 所在公司
                        //if($bill_type == "S" && $item_type == "PF" && !empty($company_id)){//"is_on_sale"=>3, //批发销售单审核货品状态不变更
                            //$bill_item = array('is_settled'=>$is_settle, 'settle_time'=>$settle_time, 'settle_user'=>$settle_user);
                            $management_fee = $vo['management_fee'];
                            $jijiachengben = $vo['sale_price'];
                            $yuanshichengben = $jijiachengben - $management_fee;
                            $goods_item = array('is_on_sale'=>1,'put_in_type'=>$put_in_type,'prc_id'=>$from_company_id,'prc_name'=>$from_company_name, 'company_id'=>$company_id, 'company'=>$company_name, 'yuanshichengbenjia'=>$yuanshichengben, 'mingyichengben'=>$yuanshichengben, 'jijiachengben'=>$jijiachengben, 'management_fee' => $management_fee);
                        //}else{
                            //$goods_item = array("is_on_sale"=>3, 'yuanshichengbenjia'=>$vo['sale_price'], 'mingyichengben'=>$vo['sale_price'], 'jijiachengben'=>$vo['sale_price']);
                        //}
                        $log_remark = "销售出库单审核, 单据编号:".$bill_no;
                    }else{
                        $goods_item = array('weixiu_status'=>4);//维修完成
                        $log_remark = "完成维修出库单, 单据编号:".$bill_no;
                    }
                    $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                    if(!$res){
                        throw new Exception("货号{$goods_itemid}更新商品信息失败！");
                    }
                                        
                    $log_data = array(
                        'goods_itemid'=>$goods_itemid,
                        'log_remark'=>$log_remark,
                        'log_user_id'=>$_SESSION['seller_id'],
                        'log_user_name'=>$_SESSION['seller_name'],
                        'log_type' =>$goods_log_model::LOG_TYPE_BILL_S,
                        'bill_no'=>$bill_no,
                    );
                    $res = $goods_log_model->addGoodsItemLog($log_data);
                    if(!$res){
                        throw new Exception("新增货品日志失败！");
                    }
                }
            }

            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    /*
    private function addGoodsItemLog($goods_itemid,$bill_type,$bill_no){
        $goods_log_model= new goods_items_logModel();//Model("goods_items_log");
        $log_remark = "其它出库，单据编号：".$bill_no;
        $log_type=$goods_log_model::LOG_TYPE_BIll;
       if($bill_type=="S")  {
           $log_remark = "销售出库，单据编号：".$bill_no;
           $log_type=$goods_log_model::LOG_TYPE_ORDER_XD;
       }
        $log_data = array(
            'goods_itemid'=>$goods_itemid,
            'log_remark'=>$log_remark,
            'log_user_id'=>$_SESSION['seller_id'],
            'log_user_name'=>$_SESSION['seller_name'],
            'log_type' =>$log_type,
            'bill_no'=>$bill_no,
        );
        $res = $goods_log_model->addGoodsItemLog($log_data);
        if(!$res){
            throw new Exception("新增货品日志失败！");
        }
    }*/

    /**
     * 销售退货单D 审核
     * @param unknown $bill_id
     * @return multitype:number string
     */
    private function checkBillD($bill_id,$check_status,$bill_info=array()){
        $result = array('success'=>0,'msg'=>'');
        try{
            $store_id = $_SESSION['store_id'];
            if(empty($bill_info)){
                $bill_info = $this->getErpBillInfo(array('bill_id'=>$bill_id,'store_id'=>$store_id));
            }
            $bill_type = $bill_info['bill_type'];
            $item_type = $bill_info['item_type'];
            $goods_total = $this->table("erp_bill_goods")->where(array('bill_id'=>$bill_id))->count();
            if($goods_total==0){
                throw new Exception("单据还没有添加明细，不能审核通过！");
            }else if($goods_total>9999){
                throw new Exception("单据商品明细数量不能大于9999条记录！请将单据取消，重新制单！");
            }
            //单据商品+库存商品 连表查询
            $bill_goods = $this->table("erp_bill_goods,goods_items")->join("left")->on("erp_bill_goods.goods_itemid=goods_items.goods_id")->field("goods_itemid")->page($goods_total)->where(array('bill_id'=>$bill_id))->select();
            if($check_status==2){
                $updata = array('bill_status'=>3,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($updata);
                if($res===false){
                    throw new Exception("取消单据失败！");
                }
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("退货单货号不能为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];
                    if($item_type == "PF"){
                        $goods_item = array("is_on_sale"=>2);//批发取消货品改为库存
                    }else{
                        $goods_item = array("weixiu_status"=>1);//维修退货取消维修状态改维修取消
                    }
                    $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                    if(!$res){
                        throw new Exception("货号{$goods_itemid}更新商品信息失败！");
                    }
                }
            }else{                
                //更改单据为已审核状态
                $billEditData = array('bill_status'=>2,'check_user'=>$_SESSION['seller_name'],'check_time'=>date('Y-m-d H:i:s',TIMESTAMP));
                if($item_type == "PF"){
                    //批发退货单单 上级审核
                    if(empty($bill_info['item_status']) && $bill_info['from_company_id'] == $_SESSION['store_company_id']){
                        $billEditData['bill_status'] = 1;//待审核
                        $billEditData['item_status'] = 1;//门店-确认申请退货
                        $result['tip'] = 1;//用于下属公司审核成功后提示
                    }else if($bill_info['item_status']==1 && $bill_info['to_company_id'] == $_SESSION['store_company_id']){
                        $billEditData['bill_status'] = 2;//已审核
                    }
                }
                $res = $this->table("erp_bill")->where(array('bill_id'=>$bill_id))->update($billEditData);
                if($res===false){
                    throw new Exception("更改已审核状态失败！");
                }
                $warehouse_model = new erp_warehouseModel();
                foreach ($bill_goods as $key=>$vo){
                    if(empty($vo['goods_itemid'])){
                        throw new Exception("退货单货号不能为空！");
                    }
                    $goods_itemid = $vo['goods_itemid'];
                    if($item_type == "PF"){
                        //取货号最后一个p单的成本价
                       /* $pinfo = $this->getBillGooodsInfoP($goods_itemid);
                        if(empty($pinfo)){
                            throw new Exception("{$goods_itemid}货号没有批发销售单！");
                        }
                        $chengbenjia = $pinfo['yuanshichengben'];//原始成本价
                        $put_in_type = $pinfo['goods_data']['put_in_type'];
                        $prc_id = $pinfo['goods_data']['prc_id'];
                        $prc_name = $pinfo['goods_data']['prc_name'];
                        $goods_item = array("prc_id"=>$prc_id,"prc_name"=>$prc_name,"put_in_type"=>$put_in_type,"yuanshichengbenjia"=>$chengbenjia, "mingyichengben"=>$chengbenjia, "jijiachengben"=>$chengbenjia);//更新成本价
                       */
                       //批发退货返厂审核不更改 货品信息，批发商签收后修改
                    }else{                        
                        $house_name =$warehouse_model->getWareHouseInfo(array('house_id'=>$bill_info['to_house_id']));
                        $goods_item = array("weixiu_status"=>3, 'weixiu_company_id'=>$bill_info['to_company_id'], 'weixiu_warehouse_id'=>$bill_info['to_house_id'],'weixiu_warehouse_name'=>$house_name['name']);
                    }
                    if(!empty($goods_item)){
                        $res = $this->table("goods_items")->where(array("goods_id"=>$goods_itemid))->update($goods_item);
                        if(!$res){
                            throw new Exception("货号{$goods_itemid}更新商品信息失败！");
                        }
                    }

                }                
            }

            $result['success'] = 1;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }
    /**
     * erp_bill 表列表
     * @param unknown $condition
     * @param string $fields
     * @param string $pagesize
     * @param string $order
     * @param string $limit
     */
    public function getErpBillList($condition = array(), $fields = "*", $pagesize = null, $order = '', $limit = null){
        return $this->table('erp_bill')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
    /**
     * 单据搜索
     * @param unknown $where
     * @param string $fields
     * @param string $pagesize
     * @param string $order
     * @param string $limit
     */
    public function searchErpBillList($condition, $fields = "*", $pagesize = null, $order = '', $limit = null) {

        /*$db=new ModelDb();
        $str_where=$db->parseWhere($condition);
        print_r($str_where);
        exit();*/
        return $this->table('erp_bill')->where($condition)->field($fields)->order($order)->limit($limit)->page($pagesize)->select();
    }
    /**
     * 格式化 单据查询列表 
     * @param unknown $data  数据
     */
    public function formatErpBillList($data,$type="list"){
        if(empty($data)){
            return $data;
        }
        //$supplier_model = Model('store_supplier');
        $jxc_wholesale_model=Model('jxc_wholesale');
        $box_model =Model('erp_box');
        $warehouse_model = Model("erp_warehouse");
        $company_model = Model("company");
        $store_model = Model("store");
        
        //$box_list = $box_model->getBoxList(array('erp_warehouse.store_id'=>$_SESSION['store_id']));
        //$box_list = array_column($box_list,'box_name','box_id');

        $goods_items_model = new goods_itemsModel();//Model('goods_items');
        $warehouse_list = $warehouse_model->getWareHouseList(array(),'house_id,name', 10000);
        $warehouse_list =  array_column($warehouse_list,'name','house_id');
        $company_list = $goods_items_model->getCompanyList("id,company_name");
        $company_list = array_column($company_list,'company_name','id');
        $store_list = $store_model->getStoreOnlineList(array(),0,'',"store_id,store_name");
        $store_list = array_column($store_list,'store_name','store_id');
        $data = $type=="list"?$data:array($data);
        foreach($data as &$vo){            
            $vo['to_house_name'] = '';         
            //$vo['bill_status_name'] = self::getBillStatus($vo['bill_status'],$vo);
            //$vo['bill_type_name'] = self::getBillType($vo['bill_type']);
            if(!empty($vo['supplier_id']) && isset($company_list[$vo['supplier_id']])){
                $vo['supplier_name'] = $company_list[$vo['supplier_id']];
            }
            if(!empty($vo['wholesale_id'])){
                $jxc_wholesale_info = $jxc_wholesale_model->getJxcWholesaleInfo(array('wholesale_id'=>$vo['wholesale_id']),'wholesale_name');
                $vo['wholesale_name'] = $jxc_wholesale_info['wholesale_name'];
            }
            //出库公司
            if(!empty($vo['from_company_id']) && isset($company_list[$vo['from_company_id']])){
                $vo['from_company_name'] = $company_list[$vo['from_company_id']];
            }

            //出库门店
            if(!empty($vo['from_store_id']) && isset($store_list[$vo['from_store_id']])){
                $vo['from_store_name'] = $store_list[$vo['from_store_id']];
            }
            //出库仓
            if(!empty($vo['from_house_id']) && isset($warehouse_list[$vo['from_house_id']])){
                $vo['from_house_name'] = $warehouse_list[$vo['from_house_id']];                
            }else if($vo['bill_type']=="W" && empty($vo['from_house_id'])){
                $vo['from_house_name'] = "所有仓库";
            }
            //入库 储位
            //if(!empty($vo['from_box_id']) && isset($box_list[$vo['from_box_id']])){
                $vo['from_box_name'] = $vo['from_box_id'];
            //}
            //入库公司
            if(!empty($vo['to_company_id']) && isset($company_list[$vo['to_company_id']])){
                $vo['to_company_name'] = $company_list[$vo['to_company_id']];
            }
            //入库门店
            if(!empty($vo['to_store_id']) && isset($store_list[$vo['to_store_id']])){
                $vo['to_store_name'] = $store_list[$vo['to_store_id']];
            }
            //入库仓
            if(!empty($vo['to_house_id']) && isset($warehouse_list[$vo['to_house_id']])){
                $vo['to_house_name'] = $warehouse_list[$vo['to_house_id']];
            }
            //入库 储位
            //if(!empty($vo['to_box_id']) && isset($box_list[$vo['to_box_id']])){
                $vo['to_box_name'] = $vo['to_box_id'];
            //}
            //入库店铺
            if(!empty($vo['to_chain_id']) && isset($chain_list[$vo['to_chain_id']])){
                $vo['to_chain_name'] = $chain_list[$vo['to_house_id']];
            }
            
            if($vo['bill_type']=="W"){
                $pandianTotal = $this->getPandianGoodsTotal($vo['bill_id']);
                $vo['all_pandian_total'] = $pandianTotal['all_pandian_total']/1;
                $vo['my_pandian_total'] = $pandianTotal['my_pandian_total']/1;
            }
        }
        return $type=="list"?$data:$data[0];
    }
    
    /**
     * 获取单据信息
     * @param $condition
     * @param string $field
     * @return mixed
     */
    public function getErpBillInfo($condition,$field="*"){
       return $this->table('erp_bill')->field($field)->where($condition)->find();
    }

    /**
     * 编辑单据信息
     * @param array $condition
     * @param array $update
     * @return boolean
     */
    public function editErpBill($condition, $update) {
        return $this->where($condition)->update($update);
    }

    /**
     * 查询批发价
     * @param array $condition
     * @param array $update
     * @return boolean
     */
    public function getBillGooodsInfoP($goods_id)
    {
        $bill_goods = $this->table("erp_bill,erp_bill_goods")->join("left")->on("erp_bill.bill_id = erp_bill_goods.bill_id")->field("erp_bill.bill_no,erp_bill.wholesale_id,erp_bill_goods.yuanshichengben,erp_bill_goods.mingyichengben,erp_bill_goods.jijiachengben,erp_bill_goods.sale_price,erp_bill.out_warehouse_type,erp_bill_goods.goods_data")->where(array('erp_bill_goods.goods_itemid'=>$goods_id,'erp_bill.bill_type'=>'S','erp_bill.item_type'=>'PF','erp_bill.bill_status'=>array('in', array(2,4))))->order("erp_bill.bill_id desc")->find();
        if(!empty($bill_goods)) {
            $bill_goods['goods_data'] = json_decode($bill_goods['goods_data'],true);
        }else{
            $bill_goods = array();
        }
        return $bill_goods;
    }



    /**
     * 查询批发价
     * @param array $condition
     * @param array $update
     * @return boolean
     */
    public function getBillGooodsInfoPWhereGoodsId($goods_id)
    {
        $bill_goods = $this->table("erp_bill,erp_bill_goods")->join("left")->on("erp_bill.bill_id = erp_bill_goods.bill_id")->field("erp_bill.bill_no,erp_bill.wholesale_id,erp_bill.bill_status,erp_bill.to_company_id,erp_bill_goods.yuanshichengben,erp_bill_goods.mingyichengben,erp_bill_goods.jijiachengben,erp_bill_goods.sale_price,erp_bill.out_warehouse_type,erp_bill_goods.goods_data")->where(array('erp_bill_goods.goods_itemid'=>$goods_id,'erp_bill.bill_type'=>'S','erp_bill.item_type'=>'PF'))->order("erp_bill.bill_id desc")->find();
        if(!empty($bill_goods)) {
            $bill_goods['goods_data'] = json_decode($bill_goods['goods_data'],true);
        }else{
            $bill_goods = array();
        }
        return $bill_goods;
    }

    /**
     * 查询批发价
     * @param array $condition
     * @param array $update
     * @return boolean
     */
    public function getBillGooodsInfoPWhereCompany($goods_id)
    {
        $bill_goods = $this->table("erp_bill,erp_bill_goods")->join("left")->on("erp_bill.bill_id = erp_bill_goods.bill_id")->field("erp_bill.bill_no,erp_bill.wholesale_id,erp_bill_goods.yuanshichengben,erp_bill_goods.mingyichengben,erp_bill_goods.jijiachengben,erp_bill_goods.sale_price,erp_bill.out_warehouse_type,erp_bill_goods.goods_data")->where(array('erp_bill_goods.goods_itemid'=>$goods_id,'erp_bill.bill_type'=>'S','erp_bill.item_type'=>'PF','erp_bill.bill_status'=>array('in', array(2,4)),'erp_bill.to_company_id'=>$_SESSION['store_company_id']))->order("erp_bill.bill_id desc")->find();
        if(!empty($bill_goods)) {
            $bill_goods['goods_data'] = json_decode($bill_goods['goods_data'],true);
        }else{
            $bill_goods = array();
        }
        return $bill_goods;
    }





    public function getBillsToSync($limit) {
        return $this->table('erp_bill,erp_bill_sync,company')
            ->join('left,inner')->on('erp_bill.bill_id = erp_bill_sync.bill_id,erp_bill.to_company_id=company.id')->field('erp_bill.bill_id,erp_bill.bill_no')
            ->where("(erp_bill.to_company_id = 58 or company.company_type = 4)and ((erp_bill.bill_type ='D' AND erp_bill.item_type = 'PF' and erp_bill.bill_status=1 and erp_bill.item_status=1) OR (erp_bill.bill_type ='M' AND erp_bill.item_type ='WX' and erp_bill.bill_status=2 )) AND erp_bill_sync.latest_push_time is null")
            ->order("erp_bill.bill_id DESC")->limit($limit)
            ->select();
    }
    
    /**
     * 已盘点商品数量统计
     * @param unknown $bill_id
     * @return Ambigous <bool/null/array, multitype:, unknown>
     */
    public function getPandianGoodsTotal($bill_id){
        $sql = "select count(1) as all_pandian_total,sum(if(pandian_user='{$_SESSION['seller_name']}',1,0)) as my_pandian_total from erp_bill_goods where bill_id={$bill_id} and bill_type='W' and pandian_time is not null";
        return DB::getRow2($sql);
    }
    
    public function getErpBillWList($condition){
        return $this->table('erp_bill_w')->where($condition)->page(9999)->select();
    }
    public function getErpBillWInfo($condition,$field="*"){
        return $this->table('erp_bill_w')->field($field)->where($condition)->find();
    }
    
    /**
     * 盘点商品矫正
     * @param unknown $bill_id
     */
    public function pandianAdjust($bill_id,$transMode = true){
       
        try{
            $bill_goods_list = $this->table('erp_bill_goods')->where(array('bill_id'=>$bill_id,'bill_type'=>'W','pandian_status'=>array('in',array(1,2))))->page(999999)->select();
            if(empty($bill_goods_list)){
                return  callback(true);
            }            
            foreach ($bill_goods_list as $bill_goods){                
                $pandian_status = $bill_goods['pandian_status'];
                $goods_id = $bill_goods['goods_itemid'];
                if(in_array($pandian_status,array(1,2))){
                    $goods_info = $this->table('goods_items')->where(array('goods_id'=>$goods_id))->find();
                    if(empty($goods_info)){
                        throw new Exception("盘点货号找不到库存记录");
                    }
                    $is_on_sale = $goods_info['is_on_sale'];                    
                    //pandian_status:盘点状态 1盘亏 2盘盈 3正常 
                    //adjust_status:盘点调整状态 0无  1在途 2已销售
                    //is_on_sale:库存状态 3已销售 //1收货中、5调拨中、7报损中,8返厂中、10销售中、11退货中
                    $billGoodsData = array();
                    if($pandian_status==1 && $is_on_sale==3){
                        $billGoodsData = array('pandian_status'=>3,'pandian_adjust'=>2);
                    }else if($pandian_status==2 && in_array($is_on_sale,array(1,5,7,8,10,11))){                        
                        $billGoodsData = array('pandian_status'=>3,'pandian_adjust'=>1);
                    }
                    if(!empty($billGoodsData)){
                       $res = $this->table('erp_bill_goods')->where(array('id'=>$bill_goods['id']))->update($billGoodsData);
                       if($res === false){
                           throw new Exception("更新盘点明细失败");
                       }
                    }
                }
            }            
            if($transMode){
                $this->commit();
            }
            $result =  callback(true);
        }catch (Exception $e){
            if($transMode){
                $this->rollback();
            }
            $result = callback(false,$e->getMessage());
        }
        return $result;
    }
    /**
     * 盘点单统计
     * @param unknown $bill_id
     * @return Ambigous <bool/null/array, multitype:, unknown>
     */
    public function getPandianGoodsTj($bill_id){
        $sql = "select sum(jijiachengben) as chengben_total,count(1) as goods_num,
        sum(if(pandian_status=1,jijiachengben,0)) as chengben_total1,sum(if(pandian_status=1,1,0)) as goods_num1,
        sum(if(pandian_status=2,jijiachengben,0)) as chengben_total2,sum(if(pandian_status=2,1,0)) as goods_num2, 
        sum(if(pandian_status=3,jijiachengben,0)) as chengben_total3,sum(if(pandian_status=3,1,0)) as goods_num3  
        from erp_bill_goods where bill_id={$bill_id} and bill_type='W'";
        return DB::getRow2($sql);
   }
   /**
    * 查询单据锁定仓库列表
    * @param unknown $bill_id
    * @return Ambigous <bool/null/array, multitype:, unknown, NULL, multitype:multitype: , multitype:unknown >
    */
   public function getBillLockWarhouseList($bill_id){
       $sql = "select DISTINCT if(bg.from_house_id=w.house_id,w.name,'') as from_house_name,if(bg.to_house_id=w.house_id,w.name,'') as to_house_name from erp_bill_goods bg INNER JOIN erp_bill b on bg.bill_id=b.bill_id
INNER JOIN erp_warehouse w on bg.from_house_id=w.house_id or bg.to_house_id=w.house_id 
where b.bill_id={$bill_id} and w.`lock`>0";
       return DB::getAll($sql);
   }
}    
?>