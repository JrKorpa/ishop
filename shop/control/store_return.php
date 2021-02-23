<?php
/**
 * 卖家退货
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class store_returnControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        $model_refund = Model('refund_return');
        $model_refund->getRefundStateArray();
        Language::read('member_store_index');
    }
    /**
     * 退货记录列表页
     *
     */
    public function indexOp() {
        $model_refund = Model('refund_return');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];

        $keyword_type = array('order_sn','refund_sn','buyer_name');
        if (trim($_GET['key']) != '' && in_array($_GET['type'],$keyword_type)) {
            $type = $_GET['type'];
            $condition[$type] = array('like','%'.$_GET['key'].'%');
        }
        if (trim($_GET['add_time_from']) != '' || trim($_GET['add_time_to']) != '') {
            $add_time_from = strtotime(trim($_GET['add_time_from']));
            $add_time_to = strtotime(trim($_GET['add_time_to']));
            if ($add_time_from !== false || $add_time_to !== false) {
                $condition['add_time'] = array('time',array($add_time_from,$add_time_to));
            }
        }
        $seller_state = intval($_GET['state']);
        if ($seller_state > 0) {
            $condition['seller_state'] = $seller_state;
        }
        //$order_lock = intval($_GET['lock']);
        //if ($order_lock != 1) {
        //    $order_lock = 2;
        //}
        //$_GET['lock'] = $order_lock;
        //$condition['order_lock'] = $order_lock;
        $_GET['lock'] = 1;
        $condition['order_lock'] = array('in', array(1,2));

        $return_check = $this->check_seller_limit('limit_return_check');
        Tpl::output('return_check', $return_check);
        $return_list = $model_refund->getReturnList($condition,10);
        Tpl::output('return_list',$return_list);
        Tpl::output('show_page',$model_refund->showpage());
        //self::profile_menu('return',$order_lock);
        self::profile_menu('return',1);
        Tpl::showpage('store_return');
    }
    /**
     * 退货审核页
     *
     */
    public function editOp() {
        $model_refund = new refund_returnModel();
        $model_order  = new orderModel();
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_id'] = intval($_GET['return_id']);
        $return_list = $model_refund->getReturnList($condition);
        $return = isset($return_list[0])?$return_list[0]:array();
        if (chksubmit()) {
            $reload = 'index.php?act=store_return&lock=1';
            if ($return['order_lock'] == '2') {
                $reload = 'index.php?act=store_return&lock=2';
            }
            if ($return['seller_state'] != '1') {//检查状态,防止页面刷新不及时造成数据错误
                showDialog(Language::get('wrong_argument'),$reload,'error');
            }
            try {
                $model_order->beginTransaction();
                $order_id = $return['order_id'];
                $order_sn = $return['order_sn'];
                $order_goods_id = $return['order_goods_id'];
                $refund_sn = $return['refund_sn'];
                $refund_array = array();
                $refund_array['seller_time'] = time();
                $refund_array['seller_state'] = $_POST['seller_state'];//卖家处理状态:1为待审核,2为同意,3为不同意
                $refund_array['seller_message'] = $_POST['seller_message'];
                $to_house_id = $_POST['warehouse_id'];
                if ($refund_array['seller_state'] == '2' && empty($_POST['return_type'])) {
                    $refund_array['return_type'] = '2';//退货类型:1为不用退货,2为需要退货
                } elseif ($refund_array['seller_state'] == '3') {
                    $refund_array['refund_state'] = '3';//状态:1为处理中,2为待管理员处理,3为已完成
                } else {
                    $refund_array['seller_state'] = '2';
                    $refund_array['refund_state'] = '2';
                    $refund_array['return_type'] = '1';//选择弃货
                }
                $warehouse = Model("erp_warehouse")->getWareHouseInfo(array('house_id'=>$to_house_id));
                if(isset($warehouse) && $warehouse['lock']>0){
                    throw new Exception("【{$warehouse['name']}】退货仓库正在盘点中！");
                }
                
                $state = $model_refund->editRefundReturn($condition, $refund_array);
                
                if ($state) {
                    if(empty($return['order_sn']) && empty($return['order_goods_id'])){
                        if($refund_array['seller_state'] == 2){
                            $result = $this->checkBillHa($return);
                            if($result['error'] == 0){
                                $bill_no = $result['return_msg'];
                                $model_order->commit();
                                showDialog(Language::get('nc_common_save_succ')."单据编号".$bill_no, $reload, 'succ');
                            }
                        }else{
                            $model_order->commit();
                            showDialog(Language::get('nc_common_save_succ'), $reload, 'succ');
                        }
                    }
                    if ($refund_array['seller_state'] == '3' && $return['order_lock'] == '2') {
                        $model_refund->editOrderUnlock($order_id);//订单解锁
                    }

                    $order = $model_order->getOrderInfo(array('order_id'=> $order_id),array());
                    if ($order['payment_time'] > 0) {
                        $order['pay_amount'] = $order['order_amount']-$order['rcb_amount']-$order['pd_amount'];//在线支付金额=订单总价格-充值卡支付金额-预存款支付金额
                    }

                    $condition = array();
                    $condition['refund_id'] = intval($_GET['refund_id']);
                    $detail_array = $model_refund->getDetailInfo($condition);
                    if(empty($detail_array)) {
                        $model_refund->addDetail($return,$order);
                        $detail_array = $model_refund->getDetailInfo($condition);
                    }
                    if ($detail_array['pay_time'] > 0) {
                        $return['pay_amount'] = $detail_array['pay_amount'];//已完成在线退款金额
                    }
                    $bill_no = "";
                    //$state = $model_refund->editOrderRefund($return,$this->admin_info['name']);
                    if($refund_array['seller_state'] == 2){//同意
                        
                        $order_goods = Model('order')->getOrderGoodsInfo(array('rec_id'=>$order_goods_id));
                        if(empty($order_goods)) throw new Exception('商品明细为空！');
                        $goods_itemid = $order_goods['goods_itemid'];
                        //更新订单商品信息
                        $updata = array();
                        $updata['is_return'] = 1;
                        $updata['refund_amount'] = bcadd($order_goods['refund_amount'], $return['refund_amount'], 2);
                        $updata['breach_amount'] = bcadd($order_goods['breach_amount'], $return['breach_amount'], 2);
                        $res = $model_order->editOrderGoods($updata, array('rec_id'=>$order_goods_id)); 
                        if(!$res){
                            throw new Exception('更新订单商品信息失败');
                        }
                        //更新订单信息
                        $updata = array();
                        $goods_list = $model_order->getOrderGoodsInfo(array('order_id'=>$order_id, 'is_return' => 0));
                        if(!empty($goods_list)){
                            $refund_state = 1;
                        }else{
                            //$updata['order_state'] = 0;
                            $refund_state = 2;
                        }
                        $updata['refund_state'] = $refund_state;
                        $updata['refund_amount'] = bcadd($order['refund_amount'], $return['refund_amount'], 2);
                        //订单主表违约金累计
                        $updata['breach_amount'] = bcadd($order['breach_amount'] ,$return['breach_amount'],2);
                        //查询订单商品是否全部退货 退款总金额=订单金额
                        //if($updata['refund_amount'] == $order['rcb_amount']){
                        //    $refund_state = 2;
                        //}else{
                        //    $refund_state = 1;
                        //}
                        $goodslist = $model_order->getOrderGoodsInfo(array('rec_id'=>$order_goods_id));
                        $updata['order_amount'] = bcsub($order['order_amount'], $goodslist['goods_pay_price'], 2);
                        //$updata['update_time'] = date("Y-m-d H:i:s");
                        $res = $model_order->editOrder($updata, array('order_id'=>$order_id));
                        if(!$res){
                            throw new Exception('更新订单信息失败');
                        }
                        
                        $erp_bill_model = Model('erp_bill');
                        $goods_model = Model('erp_goods');
                        $bill_goods_list = array();
                        $from_company_id = 0;
                        $to_company_id = $_SESSION['store_company_id'];
                        $erp_warehouse_model = Model('erp_warehouse');
                        if(empty($to_house_id)){
                            $erp_info = $erp_warehouse_model->getWareHouseInfo(array('company_id'=>$to_company_id, 'type'=>9));
                            if(!isset($erp_info['house_id'])){
                                throw new Exception("未设置退货仓，请配置退货仓库");
                            }
                            $to_house_id = $erp_info['house_id'];
                            $to_house_name = $erp_info['name'];
                        }else{
                            $erp_info = $erp_warehouse_model->getWareHouseInfo(array('house_id'=>$to_house_id));
                            $to_house_name = $erp_info['name'];
                        }
                        $return_goods_list = $model_refund->getReturnGoodsByDetailId($order_goods_id);                        
                        if(empty($return_goods_list)){
                            throw new Exception("未查到此货品".$goods_itemid);
                        }
                        $return_goods_ids = array_column($return_goods_list, "goods_id");
                        //防止成品定制  漏货
                        if(!in_array($goods_itemid, $return_goods_ids)){
                            throw new Exception("未查到货品".$goods_itemid);
                        }
                        $sale_price_total = $chengben_total = $guanlifei_total = 0;
                        foreach ($return_goods_list as $k =>$g){
                            if($g['is_on_sale'] != 3){
                                 throw new Exception("货品不是已销售状态不能退货");
                            }
                            $bill_goods = array();
                            $bill_goods['goods_itemid'] = $g['goods_id'];
                            $bill_goods['goods_sn'] = $g['goods_sn'];
                            $bill_goods['goods_name'] = $g['goods_name'];
                            $bill_goods['goods_count'] = 1;
                            //$goods_info['remark'] = $goods_list['goods_id'];
                            $bill_goods['from_company_id'] = $from_company_id;
                            //$goods_info['from_house_id'] = $goods_list['goods_id'];
                            $bill_goods['to_company_id'] = $to_company_id;
                            $bill_goods['to_house_id'] = $to_house_id;
                            $bill_goods['in_warehouse_type'] = $g['put_in_type'];
                            //$goods_info['is_settled'] = $goods_list['goods_id'];
                            //$goods_info['settle_user'] = $goods_list['goods_id'];
                            $bill_goods['yuanshichengben'] = $g['sale_price'];
                            $bill_goods['mingyichengben'] = $g['sale_price'];
                            $bill_goods['jijiachengben'] = $g['sale_price'];    
                            $bill_goods['sale_price'] = $g['sale_price'];//退货金额
                            $bill_goods['management_fee'] = 0;
                            $bill_goods_list[] = $bill_goods;
                            $sale_price_total = bcadd($sale_price_total, $g['sale_price'], 2);
                            //$guanlifei_total = bcadd($guanlifei_total, $g['management_fee'], 2);
                            $chengben_total = bcadd($chengben_total, $g['jijiachengben'], 2);
                        }
                        //$sale_price_total 表示 商品实际支付金额
                        //if($sale_price_total < $return['refund_amount']){
                         //   throw new Exception("退款金额不能大于订单成交金额{$pifajia_total}");
                        //}
                        //$goods_arr = array($goods_itemid);
                        /*foreach ($goods_arr as $key => $goods_id) {
                            $goods_list = $goods_model->getErpBillList(array('goods_id'=>$goods_id));
                            $goods_list = isset($goods_list[0])?$goods_list[0]:array();
                            if(empty($goods_list)){
                                throw new Exception("未查到此货品".$goods_list['goods_id']);
                            }
                            if($goods_list['is_on_sale'] != 3){
                                throw new Exception("货品不是已销售状态不能退货");
                            }
                                
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
                            //$goods_info['is_settled'] = $goods_list['goods_id'];
                            //$goods_info['settle_user'] = $goods_list['goods_id'];
                            $goods_info['sale_price'] = $return['refund_amount'];
                            $goods_info['management_fee'] = 0;
                            $bill_goods_list[] = $goods_info;
                            $pifajia_total = bcadd($pifajia_total, $goods_info['sale_price'], 2);
                            $guanlifei_total = bcadd($guanlifei_total, $goods_info['management_fee'], 2);
                        }*/

                        $bill_type = "D";
                        $remark = "退货处理，退货编号：".$refund_sn;
                        $bill_info = array(
                            'bill_no'=>uniqid(),
                            'bill_type'=>$bill_type,
                            'item_type'=>'LS',
                            'bill_status'=>2,
                            'from_company_id'=>$from_company_id,
                            'wholesale_id'=>"",
                            'express_id'=>"",
                            'from_store_id'=>$_SESSION['store_id'],
                            'create_user'=>$_SESSION['seller_name'],
                            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
                            'remark'=>$remark,
                            'order_sn'=>$order_sn,
                            'to_company_id'=>$to_company_id,
                            'to_house_id'=>$to_house_id,
                            'warehouse_name'=>$to_house_name,
                            'chengben_total'=>$chengben_total,//最大可退金额
                            'goods_total'=>$sale_price_total,//实际退款金额
                            'check_user'=>$_SESSION['seller_name'],
                            'check_time'=>date("Y-m-d H:i:s",TIMESTAMP)
                        );
                        $res = $erp_bill_model->createBill($bill_info,$bill_goods_list,$bill_type,false);
                        if($res['success'] != 1){
                            throw new Exception('生成销售退货单失败！'.$res['msg']);
                        }
                        $bill_no = $res['data']['bill_no'];
                    }
                    $this->recordSellerLog('退货处理，退货编号：'.$refund_sn);
                    // 发送买家消息
                    //$param = array();
                    //$param['code'] = 'refund_return_notice';
                    //$param['member_id'] = $return['buyer_id'];
                    //$param['param'] = array(
                    //    'refund_url' => urlShop('member_return', 'view', array('return_id' => $return['refund_id'])),
                    //    'refund_sn' => $refund_sn
                    //);
                    //QueueClient::push('sendMemberMsg', $param);
                    $condition = array();
                    $condition['order_id'] = $order_id;
                    $order_info = $model_order->getOrderInfo($condition);
                    $_log = $refund_array['seller_state'] == 2 ?"同意":"不同意";
                    //添加订单日志
                    $data = array();
                    $data['order_id'] = $order_id;
                    $data['log_role'] = "seller";
                    $data['log_msg'] = $refund_sn."退款申请".$_log.$bill_no;
                    $data['log_user'] = $order_info['seller_name'];
                    $data['log_orderstate'] = $order_info['order_state'];
                    $res = $model_order->addOrderLog($data);
                    if(!$res){
                        throw new Exception('添加订单日志失败');
                    }
                    $model_order->commit();
                    showDialog(Language::get('nc_common_save_succ')."单据编号".$bill_no, $reload, 'succ');
                } else {
                    throw new Exception('更新退款单状态失败');
                }
            } catch (Exception $e) {
                $model_order->rollback();
                showDialog("提交失败！".$e->getMessage(),"",'error');
            }
        }
        if(empty($return['order_sn']) && empty($return['order_goods_id'])){
            $billha_info = unserialize($return['billha_info']);
            $return['to_house_id'] = $billha_info['to_house_id'];
            $return['sales_price'] = ncPriceFormat($billha_info['sales_price']);
        }
        Tpl::output('return',$return);
        $info['buyer'] = array();
        if(!empty($return['pic_info'])) {
            $info = unserialize($return['pic_info']);
        }
        $erp_warehouse_model =Model('erp_warehouse');
        //$erp_box_model =Model('erp_box');
        $house_list = $erp_warehouse_model->getWareHouseList(array('store_id' =>$_SESSION['store_id']), 'house_id,name', 5000);
        Tpl::output('house_list', $house_list);
        Tpl::output('pic_list',$info['buyer']);
        $model_member = Model('member');
        $member = $model_member->getMemberInfoByID($return['buyer_id']);
        $order_goods_id = $return['order_goods_id'];
        $breach_amount = $return['breach_amount'];
        //$breach_amount = '0.00';
        //if($order_goods_id){
        //    $model = new orderModel();
        //    $goods_info = $model->getOrderGoodsInfo(array('rec_id'=>$order_goods_id));
        //    $breach_amount = bcsub($goods_info['goods_pay_price'], $return['refund_amount'], 2);
        //}
        Tpl::output('breach_amount',$breach_amount);
        Tpl::output('member',$member);
        $condition = array();
        $condition['order_id'] = $return['order_id'];
        $model_refund->getRightOrderList($condition, $return['order_goods_id']);
        Tpl::showpage('store_return_edit');
    }

    /**
     * 无订单退货单审核
     *
     */
    public function checkBillHa($return=array())
    {
        if(empty($return)){
             throw new Exception("退款记录不存在");
        }
        $result = array('error'=>0,'error_msg'=>'','return_msg'=>"");
        $to_house_id = isset($_POST['warehouse_id'])?$_POST['warehouse_id']:'0';
        $goods_itemid = $return['goods_id'];
        $erp_bill_model = Model('erp_bill');
        $erp_warehouse_model =Model('erp_warehouse');
        $model_goodsitems = Model('goods_items');
        $itemsinfo = $model_goodsitems->getGoodsItemInfo(array('goods_id'=>$goods_itemid));
        
        if(empty($itemsinfo)){
             throw new Exception("货品不存在");
        }

        $billha_info = unserialize($return['billha_info']);
        //$return['to_house_id'] = $billha_info['to_house_id'];
        $sales_price = ncPriceFormat($billha_info['sales_price']);//销售价

        //if($itemsinfo['is_on_sale'] != 3){
        //     throw new Exception("货品不是已销售状态不能退货");
        //}
        $to_company_id = $_SESSION['store_company_id'];
        $from_company_id = $chengben_total = $sale_price_total = 0;
        $bill_goods = array();
        $bill_goods['goods_itemid'] = $itemsinfo['goods_id'];
        $bill_goods['goods_sn'] = $itemsinfo['goods_sn'];
        $bill_goods['goods_name'] = $itemsinfo['goods_name'];
        $bill_goods['goods_count'] = 1;
        $bill_goods['from_company_id'] = $from_company_id;
        $bill_goods['to_company_id'] = $to_company_id;
        $bill_goods['to_house_id'] = $to_house_id;
        $bill_goods['in_warehouse_type'] = $itemsinfo['put_in_type'];
        $bill_goods['yuanshichengben'] = $itemsinfo['yuanshichengbenjia'];
        $bill_goods['mingyichengben'] = $itemsinfo['mingyichengben'];
        $bill_goods['jijiachengben'] = $itemsinfo['jijiachengben'];    
        $bill_goods['sale_price'] = $sales_price;//退货金额
        $bill_goods['management_fee'] = 0;
        $sale_price_total = bcadd($sale_price_total, $sales_price, 2);
        $chengben_total = bcadd($chengben_total, $itemsinfo['jijiachengben'], 2);

        $warehouse = $erp_warehouse_model->getWareHouseInfo(array('house_id'=>$to_house_id));
        $to_house_name = isset($warehouse['name'])?$warehouse['name']:"";

        $bill_type = "D";
        $remark = "退货处理，退货编号：".$return['refund_sn'].$return['buyer_message'];
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'item_type'=>'LS',
            'bill_status'=>2,
            'from_company_id'=>$from_company_id,
            'wholesale_id'=>"",
            'express_id'=>"",
            'from_store_id'=>$_SESSION['store_id'],
            'create_user'=>$_SESSION['seller_name'],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>$remark,
            'order_sn'=>"0",
            'to_company_id'=>$to_company_id,
            'to_house_id'=>$to_house_id,
            'warehouse_name'=>$to_house_name,
            'chengben_total'=>$chengben_total,//计价成本价
            'goods_total'=>$sale_price_total,//实际退款金额（销售价）
            'check_user'=>$_SESSION['seller_name'],
            'check_time'=>date("Y-m-d H:i:s",TIMESTAMP)
        );
        $res = $erp_bill_model->createBill($bill_info, array($bill_goods), $bill_type, false);
        if($res['success'] != 1){
            throw new Exception('生成销售退货单失败！'.$res['msg']);
        }
        $result['return_msg'] = $res['data']['bill_no'];
        return $result;
    }

    /**
     * 收货
     *
     */
    public function receiveOp() {
        $model_refund = Model('refund_return');
        $model_trade = Model('trade');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_id'] = intval($_GET['return_id']);
        $return_list = $model_refund->getReturnList($condition);
        $return = $return_list[0];
        Tpl::output('return',$return);
        $return_delay = $model_trade->getMaxDay('return_delay');//发货默认5天后才能选择没收到
        $delay_time = time()-$return['delay_time']-60*60*24*$return_delay;
        Tpl::output('return_delay',$return_delay);
        Tpl::output('return_confirm',$model_trade->getMaxDay('return_confirm'));//卖家不处理收货时按同意并弃货处理
        Tpl::output('delay_time',$delay_time);
        if (chksubmit()) {
            if ($return['seller_state'] != '2' || $return['goods_state'] != '2') {//检查状态,防止页面刷新不及时造成数据错误
                showDialog(Language::get('wrong_argument'),'reload','error','CUR_DIALOG.close();');
            }
            $refund_array = array();
            if ($_POST['return_type'] == '3' && $delay_time > 0) {
                $refund_array['goods_state'] = '3';
            } else {
                $refund_array['receive_time'] = time();
                $refund_array['receive_message'] = '确认收货完成';
                $refund_array['refund_state'] = '2';//状态:1为处理中,2为待管理员处理,3为已完成
                $refund_array['goods_state'] = '4';
            }
            $state = $model_refund->editRefundReturn($condition, $refund_array);
            if ($state) {
                $this->recordSellerLog('退货确认收货，退货编号：'.$return['refund_sn']);

                // 发送买家消息
                $param = array();
                $param['code'] = 'refund_return_notice';
                $param['member_id'] = $return['buyer_id'];
                $param['param'] = array(
                    'refund_url' => urlShop('member_return', 'view', array('return_id' => $return['refund_id'])),
                    'refund_sn' => $return['refund_sn']
                );
                QueueClient::push('sendMemberMsg', $param);

                showDialog(Language::get('nc_common_save_succ'),'reload','succ','CUR_DIALOG.close();');
            } else {
                showDialog(Language::get('nc_common_save_fail'),'reload','error','CUR_DIALOG.close();');
            }
        }
        $express_list  = rkcache('express',true);
        if ($return['express_id'] > 0 && !empty($return['invoice_no'])) {
            Tpl::output('e_name',$express_list[$return['express_id']]['e_name']);
            Tpl::output('e_code',$express_list[$return['express_id']]['e_code']);
        }
        Tpl::showpage('store_return_receive','null_layout');
    }
    /**
     * 退货记录查看页
     *
     */
    public function viewOp() {
        $model_refund = Model('refund_return');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_id'] = intval($_GET['return_id']);
        $return_list = $model_refund->getReturnList($condition);
        $return = $return_list[0];
        if(empty($return['order_sn']) && empty($return['order_goods_id'])){
            $billha_info = unserialize($return['billha_info']);
            $return['to_house_id'] = $billha_info['to_house_id'];
            $return['sales_price'] = ncPriceFormat($billha_info['sales_price']);
        }
        Tpl::output('return',$return);
        $express_list  = rkcache('express',true);
        if ($return['express_id'] > 0 && !empty($return['invoice_no'])) {
            Tpl::output('e_name',$express_list[$return['express_id']]['e_name']);
            Tpl::output('e_code',$express_list[$return['express_id']]['e_code']);
        }
        $info['buyer'] = array();
        if(!empty($return['pic_info'])) {
            $info = unserialize($return['pic_info']);
        }
        $breach_amount = $return['breach_amount'];
        //$order_goods_id = $return['order_goods_id'];
        //$breach_amount = '0.00';
        //if($order_goods_id){
        //    $model = new orderModel();
        //    $goods_info = $model->getOrderGoodsInfo(array('rec_id'=>$order_goods_id));
        //    $breach_amount = bcsub($goods_info['goods_pay_price'], $return['refund_amount'], 2);
        //}
        Tpl::output('breach_amount',$breach_amount);
        Tpl::output('pic_list',$info['buyer']);
        $model_member = Model('member');
        $member = $model_member->getMemberInfoByID($return['buyer_id']);
        Tpl::output('member',$member);
        $condition = array();
        $condition['order_id'] = $return['order_id'];
        $model_refund->getRightOrderList($condition, $return['order_goods_id']);
        Tpl::showpage('store_return_view');
    }
    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type,$menu_key='') {
        $menu_array = array();
        switch ($menu_type) {
            case 'return':
                $menu_array = array(
                    //array('menu_key'=>'2','menu_name'=>'售前退货',  'menu_url'=>'index.php?act=store_return&lock=2'),
                    //array('menu_key'=>'1','menu_name'=>'售后退货','menu_url'=>'index.php?act=store_return&lock=1')
                    array('menu_key'=>'1','menu_name'=>'退货列表','menu_url'=>'index.php?act=store_return')
                );
                break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
