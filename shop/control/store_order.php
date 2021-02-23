<?php
/**
 * 卖家实物订单管理
 *
 *
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class store_orderControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }

    /**
     * 订单列表
     *
     */
    public function indexOp() {
        $model_order = Model('order');
        if (!$_GET['state_type']) {
            $_GET['state_type'] = 'store_order';
        }
        $params=array();
        $params["is_xianhuo"]=$_REQUEST["is_xianhuo"];
        $params["is_zp"]=$_REQUEST["is_zp"];
        $params["seller_name"]=$_REQUEST["seller_name"];
        $params["pay_status"]=$_REQUEST["pay_status"];
        $params["buyer_phone"]=$_REQUEST["buyer_phone"];
        $params["goods_id"]=$_REQUEST["goods_id"];
        $params["pay_start_date"] = $_REQUEST["pay_start_date"];
        $params["pay_end_date"] = $_REQUEST["pay_end_date"];
        $params["finnshed_start_date"] = $_REQUEST["finnshed_start_date"];
        $params["finnshed_end_date"] = $_REQUEST["finnshed_end_date"];
        $order_list = $model_order->getStoreOrderList($_SESSION['store_id'], $_GET['order_sn'], $_GET['buyer_name'], $_GET['state_type'], $_GET['query_start_date'], $_GET['query_end_date'], $_GET['skip_off'], '*', array('order_goods','order_common','member'),$params);

        Tpl::output('order_list',$order_list);
        Tpl::output('show_page',$model_order->showpage());
        self::profile_menu('list',$_GET['state_type']);

        Tpl::showpage('store_order.index');
    }

    /**
     * 卖家订单详情
     *
     */
    public function show_orderOp() {
        Language::read('member_member_index');
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) {
            showMessage(Language::get('wrong_argument'),'','html','error');
        }
        $model_order = new orderModel();//Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $store_id = $_SESSION['store_id'];
        //$condition['store_id'] = $store_id;
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods','member'));
        
        if (empty($order_info)) {
            showMessage(Language::get('store_order_none_exist'),'','html','error');
        }
        //取得订单其它扩展信息
        $model_order->getOrderExtendInfo($order_info);

        $model_refund_return = Model('refund_return');
        $order_list = array();
        $order_list[$order_id] = $order_info;
        $order_list = $model_refund_return->getGoodsRefundList($order_list,1);//订单商品的退款退货显示
        $order_info = $order_list[$order_id];
        $refund_all = $order_info['refund_list'][0];
        if (!empty($refund_all) && $refund_all['seller_state'] < 3) {//订单全部退款商家审核状态:1为待审核,2为同意,3为不同意
            Tpl::output('refund_all',$refund_all);
        }

        //显示锁定中
        $order_info['if_lock'] = $model_order->getOrderOperateState('lock',$order_info, $store_id);

        //显示调整费用
        $order_info['if_modify_price'] = $model_order->getOrderOperateState('modify_price',$order_info, $store_id);

        //显示取消订单
        $order_info['if_store_cancel'] = $model_order->getOrderOperateState('store_cancel',$order_info, $store_id);

        //显示发货
        $order_info['if_store_send'] = $model_order->getOrderOperateState('store_send',$order_info, $store_id);

        //显示物流跟踪
        $order_info['if_deliver'] = $model_order->getOrderOperateState('deliver',$order_info, $store_id);
        
        //显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_TIME * 3600;
        }

        //显示快递信息
        if ($order_info['shipping_code'] != '') {
            $express = rkcache('express',true);
            $order_info['express_info']['e_code'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
            $order_info['express_info']['e_name'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
            $order_info['express_info']['e_url'] = $express[$order_info['extend_order_common']['shipping_express_id']]['e_url'];
        }

        //显示系统自动收获时间
        if ($order_info['order_state'] == ORDER_STATE_SEND) {
            $order_info['order_confirm_day'] = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 24 * 3600;
        }
        //取得订单操作日志
        $order_log_list = $model_order->getOrderLogList(array('order_id'=>$order_info['order_id']),'log_id desc',"*",null,100);
        Tpl::output('order_log_list',$order_log_list);

        $api = data_gateway('isales');
        $product_log_list = $api->get_Bclog_list(100,1,array('order_sn' =>$order_info["order_sn"]));
        //$data['product_log_page_count']=empty($product_log_list['return_msg']['pageCount'])?0:$product_log_list['return_msg']['pageCount'];
        $product_log_list = is_array($product_log_list['return_msg']['data'])?$product_log_list['return_msg']['data']:array();

        Tpl::output('product_log_list',$product_log_list);

        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $last_log = end($order_log_list);
            if ($last_log['log_orderstate'] == ORDER_STATE_CANCEL) {
                $order_info['close_info'] = $last_log;
            }
        }
        //查询消费者保障服务
        if (C('contract_allow') == 1) {
            $contract_item = Model('contract')->getContractItemByCache();
        }
        foreach ($order_info['extend_order_goods'] as $value) {
            if (empty($value['goods_image'])) {
                $value['goods_image'] = C('order_detail_def_page');
                $value['image_60_url'] = '';
                $value['image_240_url'] = '';
                $value['goods_type_cn'] = '';
                
            } else {
                $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
                $value['image_240_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
                $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
            }
            
            $value['goods_url'] = '#';//urlShop('goods','index',array('goods_id'=>$value['goods_id']));
            
            //处理消费者保障服务
            if (trim($value['goods_contractid']) && $contract_item) {
                $goods_contractid_arr = explode(',',$value['goods_contractid']);
                foreach ((array)$goods_contractid_arr as $gcti_v) {
                    $value['contractlist'][] = $contract_item[$gcti_v];
                }
            }            
            $order_info['goods_list'][] = $value;

        }
        /* 
        if (empty($order_info['zengpin_list'])) {
            $order_info['goods_count'] = count($order_info['goods_list']);
        } else {
            $order_info['goods_count'] = count($order_info['goods_list']) + 1;
        }*/
        $order_info['goods_count'] = count($order_info['goods_list']);
        $order_info['source_name'] = "";
        if(!empty($order_info['customer_source_id'])){
            $management_api = data_gateway('imanagement');
            $res = $management_api->get_customer_sources_list(array('ids'=>$order_info['customer_source_id']));
            if(!empty($res['return_msg'])){
                $customer_source_info  =  $res['return_msg'][0];
                $order_info['source_name'] = $customer_source_info['source_name'];
            }
            /*
            $sourcelist = isset($res['return_msg'])?$res['return_msg']:array();
            $info = array();
            if(!empty($sourcelist)){
                foreach ($sourcelist as $key => $value) {
                    $info[$value['id']] = $value['source_name'];
                }
            }
            $order_info['source_name'] = isset($info[$order_info['customer_source_id']])?$info[$order_info['customer_source_id']]:'';
            */
        }
        $api = data_gateway('isales');
        $product_log_list = $api->get_Bclog_list(array('order_sn' =>$order_info["order_sn"]),"");
        $product_log_list=is_array($product_log_list['return_msg'])?$product_log_list['return_msg']:array();
        $order_info['product_log_list']=$product_log_list;
        Tpl::output('order_info',$order_info);
        //发货信息
        if (!empty($order_info['extend_order_common']['daddress_id'])) {
            $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
            Tpl::output('daddress_info',$daddress_info);
        }
        Tpl::setLayout('null_layout');

        // 控制订单信息编辑按钮
        $editable = true;
        if ($order_info['order_state'] == ORDER_STATE_CANCEL || $order_info['order_state'] >= ORDER_STATE_SEND) {
            $editable = false;
        } else {
            // 当前查看人是否是在管理组            
            $editable = $_SESSION['seller_id'] == $order_info['seller_id'] || $this->check_seller_limit('limit_change_order_all');
        }
        
        Tpl::output('order.editable', $editable);
        Tpl::showpage('store_order.show2');
    }

    /**
     * 卖家订单状态操作
     *
     */
    public function change_stateOp() {
        $state_type = $_GET['state_type'];
        $order_id   = intval($_GET['order_id']);

        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        if(!in_array("limit_store_order_all",$_SESSION['seller_limits'])){
            $condition['seller_id'] = $_SESSION['seller_id'];
        }
        $order_info = $model_order->getOrderInfo($condition);

        //取得其它订单类型的信息
        $model_order->getOrderExtendInfo($order_info);

        if ($_GET['state_type'] == 'order_cancel') {
            $result = $this->_order_cancel($order_info,$_POST);
        } elseif ($_GET['state_type'] == 'modify_price') {
            $result = $this->_order_ship_price($order_info,$_POST);
        } elseif ($_GET['state_type'] == 'spay_price') {
			$result = $this->_order_spay_price($order_info,$_POST);
    	}else if ($_GET['state_type'] == 'order_pay'){
            $result = $this->_order_pay($order_info,$_POST);
        }else if ($_GET['state_type'] == 'order_bc'){
            $result = $this->_order_bc($order_id,$_POST);
        }else if ($_GET['state_type'] == 'order_audit'){
            $result = $this->_order_audit($order_info,$_POST);
        }else if ($_GET['state_type'] == 'view_log'){
            $result = $this->_order_log($order_info,$_POST);
        }
	
        if (!$result['state']) {
            showDialog($result['msg'],'','error',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();',5);
        } else {
            showDialog($result['msg'],'reload','succ',empty($_GET['inajax']) ?'':'CUR_DIALOG.close();');
        }
    }

    /**
     * 取消订单
     * @param unknown $order_info
     */
    private function _order_cancel($order_info, $post) {
        $model_order = Model('order');
        $logic_order = Logic('order');
        $store_id = $_SESSION['store_id'];
        if(!chksubmit()) {
            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.cancel','null_layout');
            exit();
         } else {
             $if_allow = $model_order->getOrderOperateState('store_cancel',$order_info, $store_id);
             if (!$if_allow) {
                 return callback(false,'无权操作');
             }
             if (TIMESTAMP - 86400 < $order_info['api_pay_time']) {
                 $_hour = ceil(($order_info['api_pay_time']+86400-TIMESTAMP)/3600);
                 return callback(false,'该订单曾尝试使用第三方支付平台支付，须在'.$_hour.'小时以后才可取消');

             }
             $msg = $post['state_info1'] != '' ? $post['state_info1'] : $post['state_info'];
             if ($order_info['order_type'] == 2) {
                 //预定订单
                 return Logic('order_book')->changeOrderStateCancel($order_info,'seller',$_SESSION['seller_name'], $msg);
             } else {
                 $cancel_condition = array();
                 /*
                 if ($order_info['payment_code'] != 'offline') {
                     $cancel_condition['order_state'] = ORDER_STATE_NEW;
                 }*/
                 return $logic_order->changeOrderStateCancel($order_info,'seller',$_SESSION['seller_name'], $msg,true,$cancel_condition);
             }
         }
    }

    /**
     * 修改运费
     * @param unknown $order_info
     */
    private function _order_ship_price($order_info, $post) {
        $model_order = Model('order');
        $logic_order = Logic('order');
        $store_id = $_SESSION['store_id'];
        if(!chksubmit()) {
            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.edit_price','null_layout');
            exit();
        } else {
            $if_allow = $model_order->getOrderOperateState('modify_price',$order_info,$store_id);
            if (!$if_allow) {
                return callback(false,'无权操作');
            }
            return $logic_order->changeOrderShipPrice($order_info,'seller',$_SESSION['seller_name'],$post['shipping_fee']);
        }

    }
	/**
	 * 修改商品价格
	 * @param unknown $order_info
	 */
	private function _order_spay_price($order_info, $post) {
        $model_order = Model('order');
	    $logic_order = Logic('order');
        $store_id = $_SESSION['store_id'];
	    if(!chksubmit()) {
	        Tpl::output('order_info',$order_info);
	        Tpl::output('order_id',$order_info['order_id']);
            Tpl::showpage('store_order.edit_spay_price','null_layout');
            exit();
        } else {
            $if_allow = $model_order->getOrderOperateState('spay_price',$order_info, $store_id);
            if (!$if_allow) {
                return callback(false,'无权操作');
            }
            return $logic_order->changeOrderSpayPrice($order_info,'seller',$_SESSION['member_name'],$post['goods_amount']); 
	    }
	}

    /**
     * 打印发货单
     */
    public function order_printOp() {
        Language::read('member_printorder');

        $order_id   = intval($_GET['order_id']);
        if ($order_id <= 0){
            showMessage(Language::get('wrong_argument'),'','html','error');
        }
        $order_model = Model('order');
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_info = $order_model->getOrderInfo($condition,array('order_common','order_goods'));
        if (empty($order_info)){
            showMessage(Language::get('member_printorder_ordererror'),'','html','error');
        }
        Tpl::output('order_info',$order_info);

        //卖家信息
        $model_store    = Model('store');
        $store_info     = $model_store->getStoreInfoByID($order_info['store_id']);
        if (!empty($store_info['store_label'])){
            if (file_exists(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_label'])){
                $store_info['store_label'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$store_info['store_label'];
            }else {
                $store_info['store_label'] = '';
            }
        }
        if (!empty($store_info['store_stamp'])){
            if (file_exists(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_stamp'])){
                $store_info['store_stamp'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$store_info['store_stamp'];
            }else {
                $store_info['store_stamp'] = '';
            }
        }
        Tpl::output('store_info',$store_info);

        //订单商品
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $goods_new_list = array();
        $goods_all_num = 0;
        $goods_total_price = 0;
        if (!empty($order_info['extend_order_goods'])){
            $goods_count = count($order_goods_list);
            $i = 1;
            foreach ($order_info['extend_order_goods'] as $k => $v){
                $v['goods_name'] = str_cut($v['goods_name'],100);
                $goods_all_num += $v['goods_num'];
                $v['goods_all_price'] = ncPriceFormat($v['goods_num'] * $v['goods_price']);
                $goods_total_price += $v['goods_all_price'];
                $goods_new_list[ceil($i/4)][$i] = $v;
                $i++;
            }
        }
        //优惠金额
        $promotion_amount = $goods_total_price - $order_info['goods_amount'];
        //运费
        $order_info['shipping_fee'] = $order_info['shipping_fee'];
        Tpl::output('promotion_amount',$promotion_amount);
        Tpl::output('goods_all_num',$goods_all_num);
        Tpl::output('goods_total_price',ncPriceFormat($goods_total_price));
        Tpl::output('goods_list',$goods_new_list);
        Tpl::showpage('store_order.print',"null_layout");
    }

    /**
     * 打印发货单
     */
    public function print_orderOp() {
        Language::read('member_printorder');

        $order_id   = intval($_GET['order_id']);
        $print_type = intval($_GET['print_type']);
        if ($order_id <= 0){
            showMessage(Language::get('wrong_argument'),'','html','error');
        }
        $order_model = Model('order');
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_info = $order_model->getOrderInfo($condition,array('order_common','order_goods'));
        if (empty($order_info)){
            showMessage(Language::get('member_printorder_ordererror'),'','html','error');
        }


        //卖家信息
        $model_store    = Model('store');
        $store_info     = $model_store->getStoreInfoByID($order_info['store_id']);
        if (!empty($store_info['store_label'])){
            if (file_exists(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_label'])){
                $store_info['store_label'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$store_info['store_label'];
            }else {
                $store_info['store_label'] = '';
            }
        }
        if (!empty($store_info['store_stamp'])){
            if (file_exists(BASE_UPLOAD_PATH.DS.ATTACH_STORE.DS.$store_info['store_stamp'])){
                $store_info['store_stamp'] = UPLOAD_SITE_URL.DS.ATTACH_STORE.DS.$store_info['store_stamp'];
            }else {
                $store_info['store_stamp'] = '';
            }
        }
        Tpl::output('store_info',$store_info);

        //订单商品
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $goods_new_list = array();
        $goods_all_num = 0;
        $goods_total_price = 0;
        $giftstr = "";
        if (!empty($order_info['extend_order_goods'])){
            $goods_count = count($order_goods_list);
            $i = 1;
            foreach ($order_info['extend_order_goods'] as $k => $v){

                if($v['goods_type'] == 5){
                    $giftstr.=$v['goods_name']."X".$v['goods_num'];
                   //continue;
                }

                $v['goods_name'] = str_cut($v['goods_name'],100);
                $goods_all_num += $v['goods_num'];
                $v['goods_all_price'] = ncPriceFormat($v['goods_num'] * $v['goods_pay_price']);
                $goods_total_price += $v['goods_all_price'];
                $goods_new_list[ceil($i/4)][$i] = $v;
                $i++;
            }
        }
        $order_info['money_unpaid'] = bcsub(bcsub($goods_total_price, $order_info['rcb_amount'], 2),$order_info['refund_amount'],2);
        $order_info['daying_time'] = date('Y-m-d H:i:s');
        $order_info['giftstr'] = $giftstr;
        if($print_type == 1){
            $order_info['title'] = "珂兰货品销售单";
        }else{
            $order_info['title'] = "珂兰货品定制单";
        }
        
        //var_dump($order_info);die;
        Tpl::output('order_info',$order_info);
        //优惠金额
        $promotion_amount = $goods_total_price - $order_info['goods_amount'];
        //运费
        $order_info['shipping_fee'] = $order_info['shipping_fee'];
        Tpl::output('promotion_amount',$promotion_amount);
        Tpl::output('goods_all_num',$goods_all_num);
        Tpl::output('goods_total_price',ncPriceFormat($goods_total_price));
        Tpl::output('goods_list',$goods_new_list);
        Tpl::showpage('store_sale_order.print',"null_layout");
    }

    public function edit_orderOp(){
        $order_id = intval($_REQUEST['order_id']);
        if ($order_id <= 0) return false;
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_info = $model_order->getOrderInfo($condition);
        if (!$order_info) return false;
        $management_api = data_gateway('imanagement');
        $store_company_id = $this->store_info['store_company_id'];
        //$res = $management_api->get_sources_list(array($order_info['store_id']));
        //if(!empty($res['return_msg'])){
            //$res = $management_api->get_sources_list(array($order_info['store_id'],17));
        //}else{
            $res = $management_api->get_customer_sources_list(array('company_ids'=>array($store_company_id)));
        //}
        $sourcelist = isset($res['return_msg'])?$res['return_msg']:array();
        $info = array();
        $sourcelist_html = "";
        if(!empty($sourcelist)){
            foreach ($sourcelist as $key => $value) {
                $selected = "";
                if($value['id'] == $order_info['customer_source_id']){
                    $selected ="selected";
                }
                $info[$value['id']] = $value['source_name'];
                $sourcelist_html .= "<option ".$selected." value=".$value['id'].">".$value['source_name']."</option>";
            }
        }
        $order_info['sourcelist'] = $sourcelist_html;
        Tpl::output('order_info',$order_info);
        Tpl::showpage('store_order.order.edit','null_layout');
    }

    public function save_orderOp(){
        $order_id = intval($_REQUEST['order_id']);
        if ($order_id <= 0) return false;
        $model_order = Model('order');
        $model_order->beginTransaction();
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_info = $model_order->getOrderInfo($condition);
        if (!$order_info) return false;
        $management_api = data_gateway('imanagement');
                
        $old_source_name = "";
        if(!empty($order_info['customer_source_id']) && $_POST['customer_source_id']!=$order_info['customer_source_id']){
            $res = $management_api->get_customer_sources_list(array('ids'=>$order_info['customer_source_id']));
            if(!empty($res['return_msg'])){
                $old_source_name = $res['return_msg'][0]['source_name'];
            }
        }
        
        $data = array();
        $data['buyer_name'] = $_POST['buyer_name'];
        $data['buyer_phone'] = $_POST['buyer_phone'];
        $data['seller_name'] = $_POST['seller_name'];
        $data['customer_source_id'] = $_POST['customer_source_id'];
        $old_name = isset($info[$order_info['customer_source_id']])?$info[$order_info['customer_source_id']]:"";
        $customer_source_name = $_POST['customer_source_name'];
        $remark = "编辑订单：";
        if($data['buyer_name'] != $order_info['buyer_name']){
            $remark.=$order_info['buyer_name']."->".$data['buyer_name'].";";
        }
        if($data['buyer_phone'] != $order_info['buyer_phone']){
            $remark.=$order_info['buyer_phone']."->".$data['buyer_phone'].";";
        }
        if($data['seller_name'] != $order_info['seller_name']){
            $remark.=$order_info['seller_name']."->".$data['seller_name'].";";
        }
        if($data['customer_source_id'] != $order_info['customer_source_id']){
            $remark.=$old_source_name."->".$customer_source_name.";";
        }
        $condition = array();
        $condition['order_id'] = intval($_POST['order_id']);
        $condition['store_id'] = $_SESSION['store_id'];
        $result = $model_order->editOrder($data, $condition);
        if($result) {
            $data = array();
            $data['log_msg']= $remark;
            $data['order_id'] = $order_id;
            $data['log_role'] = 'seller';
            $data['log_user'] = $_SESSION['seller_name'];
            $data['log_orderstate'] = $order_info['order_state'];
            $res = $model_order->addOrderLog($data);
            if($res){
                $model_order->commit();

                //同步到后端 
                $latest_order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
                if ($latest_order_info['order_state'] >= ORDER_STATE_TO_CONFIRM) {
                    EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $latest_order_info));
                }

                return callback(true);
            }else{
                $model_order->rollback();
                 return callback(false);
            }
        } else {
            $model_order->rollback();
            return callback(false);
        }
    }


    public function edit_invoiceOp(){
        $order_id = intval($_REQUEST['order_id']);
        if ($order_id <= 0) return false;
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_common_info = $model_order->getOrderCommonInfo($condition);
        if (!$order_common_info) return false;
        $order_common_info['invoice_info'] = @unserialize($order_common_info['invoice_info']);
        Tpl::output('common_info',$order_common_info);
        Tpl::showpage('store_order.invoice.edit','null_layout');
    }

    public function save_invoiceOp(){
        $order_id = intval($_REQUEST['order_id']);
        if ($order_id <= 0) return false;
        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $_SESSION['store_id'];
        $order_common_info = $model_order->getOrderCommonInfo($condition);
        if (!$order_common_info) return false;
        $data = array();
        $invoice_info = array(
            '类型' => $_POST['invoice_type'],
            '抬头' => $_POST['header'],
            '内容' => $_POST['content']
        );
        $data['invoice_info'] = serialize($invoice_info);
        $condition = array();
        $condition['order_id'] = intval($_POST['order_id']);
        $condition['store_id'] = $_SESSION['store_id'];
        $result = $model_order->editOrderCommon($data, $condition);
        if($result) {
            return callback(true);
        } else {
            return callback(false);
        }
    }
    /**
     * 订单审核
     * @param unknown $order_info
     */
    private function _order_audit($order_info, $post) {
        $model_order = Model('order');
        if(!chksubmit()) {
            $order_info=$model_order->getOrderInfo(["order_id"=>$order_info['order_id']]);
            if($order_info["order_state"] != 5){
                showMessage("当前订单已操作!");
                exit();
            }
            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::output('order_sn',$order_info['order_sn']);
            Tpl::output('state_type',$order_info['state_type']);
            Tpl::showpage('store_order.audit','null_layout');
            exit();
        } else {
            $state_type=$post["state_type"];
            $order_id=$post["order_id"];
            $order_info=$model_order->getOrderInfo(["order_id"=>$order_id]);
            if(empty($order_info))  showMessage("无效订单!");
            
            if ($state_type == 'order_cancel_audit') {               
            
                try{
                    $model_order->beginTransaction();
                    //记录订单日志
                    $data = array();
                    $data['log_msg']= '取消订单:'.$post["remark"];
                    $data['order_id'] = $order_id;
                    $data['log_role'] = 'seller';
                    $data['log_user'] = $_SESSION['seller_name'];
                    //更新订单状态和审核信息
                    $array= array();
                    $array["audit_by"]=$_SESSION["seller_name"];
                    $array["audit_time"]=date('Y-m-d H:i:s');
                    
                    $array["order_state"]=ORDER_STATE_CANCEL;
                    $data['log_orderstate'] = ORDER_STATE_CANCEL;
                    
                    $model_order->editOrder($array,["order_id"=>$order_id]);
                    $model_order->addOrderLog($data);
                    $model_order->commit();                           
                        
                    if ($order_info['order_state'] >= ORDER_STATE_TO_CONFIRM) {
                        $latest_order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
                        EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $latest_order_info));
                    }
                    return callback(true,'操作成功');
                }catch (Exception $ex){
                    $model_order->rollback();
                    return callback(false, $ex->getMessage());
                }
            } else {
                $logic = Logic('order');
                return $logic->changeOrderStateConfirm($order_info, $_SESSION["seller_id"], $_SESSION["seller_name"]);
            }
        }
    }


    /**
     * 订单支付
     * @param unknown $order_info
     */
    private function _order_pay($order_info, $post) {
        $model_payment = Model('payment');
        if(!chksubmit()) {
            $payment_list=$model_payment->getPaymentList(['payment_state'=>1]);
            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::output('payment_list',$payment_list);
            Tpl::showpage('store_order.order_pay','null_layout');
            exit();
        } else {
            //记录支付信息
            $model_order_pay_action= Model('order_pay_action');
            $model_order = Model('order');
            $model_order->beginTransaction();
            try{

                if($post["money"]<0){
                    throw new Exception("付款金额不能小于0");
                }
                //var_dump($order_info["order_amount"],$order_info['rcb_amount'],$order_info['refund_amount'],$post["money"]);die;
                //剩余金额
                $balance = $order_info["order_amount"]- ($order_info['rcb_amount']- $order_info['refund_amount']) + $order_info['breach_amount'] -$post["money"];
                //var_dump($balance);die;
                if($balance < 0){
                    throw new Exception("付款金额不能大于剩余金额");
                }


                $order_id=$post["order_id"];
                $order_info=$model_order->getOrderInfo(["order_id"=>$order_id]);
                $array["order_id"]=$order_id;
                $array["order_sn"]=$post["order_sn"];
                $array["order_amount"]=$order_info["order_amount"];
                $array["deposit"]=$post["money"];
                $array["balance"]=$balance;
                $array["pay_code"]=$post["payment_code"];
                $array["pay_type"]=$post["payment_name"];
                $array["pay_account"]=$post["pay_account"];
                $array["pay_sn"]=$post["pay_sn"];
                $array["remark"]=$post["remark"];
                $array["status"]=1;

                if(isset($post["pay_date"]) && !empty($post["pay_date"])){
                    $array["pay_date"]=$post["pay_date"];
                }else{
                    $array["pay_date"]=date('Y-m-d H:i:s');
                }
                $array["create_date"]=date('Y-m-d H:i:s');
                $array["created_name"]=$_SESSION["seller_name"];
                $model_order_pay_action->addOrderPayAction($array);

                //记录订单日志
                $data = array();
                $data['order_id'] = $order_id;
                $data['log_role'] = 'seller';
                $data['log_user'] = $_SESSION['seller_name'];
                $data['log_msg'] = '支付金额'.': '.$post["money"].'元，付款方式：'.$post["payment_name"];
                $data['log_orderstate'] = ORDER_STATE_NEW;
                $payedMoney=$post["money"]+$order_info["rcb_amount"]-$order_info['refund_amount'];


                /*
                $model_order->editOrder(["rcb_amount"=>$payedMoney,"order_state"=>ORDER_STATE_NEW,"payment_time"=>time(),"pay_status"=>ORDER_PAY_PART,"payment_code"=>$post["payment_code"]],["order_id"=>$order_id]);
                //如果已付全款，则更新订单状态为待发货
                if($payedMoney>=$order_info["order_amount"]){
                    $order_state=ORDER_STATE_TOSEND;

                    if($order_info["is_xianhuo"]!=1){
                        // TODO: 如果订单期货商品货号已回填： 当货品状态都是库存，则状态调整为待发货; 当货品状态非库存时，订单状态为待签收
                        // TODO: 如果订单期货没有回填，则订单调整为生产中
                        $bc_goods_list=$model_order->getOrderGoodsBcStatus($order_id);
                        $all_goods_filled = true;
                        $all_goods_is_kucun = true;

                        foreach ($bc_goods_list as $item) {
                            if (empty($item['goods_itemid'])) { 
                                $all_goods_filled = false; 
                            }

                            if ($item['is_on_sale'] != 2) {
                                $all_goods_is_kucun = false;
                            }
                        }

                        if ($all_goods_is_kucun) $order_state = ORDER_STATE_TOSEND; //所有货品是库存，订单调整为待发货
                        else if ($all_goods_filled) $order_state = ORDER_STATE_TO_SIGN; //所有货号已回调，订单调整为待签收
                        else $order_state = ORDER_STATE_MAKING; //否则调整为生产中
                    }
                    $model_order->editOrder(["order_state"=>$order_state,"pay_status"=>ORDER_PAY_FULL],["order_id"=>$order_id]);
                    $data['log_orderstate'] = ORDER_STATE_TOSEND;
                }
                */
                //订单支付后根据条件判断订单状态2018-06-14 预留
                $order_state = ORDER_STATE_NEW;
                $pay_status = ORDER_PAY_PART;
                if($payedMoney>=$order_info["order_amount"]) {
                    $pay_status = ORDER_PAY_FULL;
                }
                if($order_info["is_xianhuo"]!=1){
                    $bc_goods_list=$model_order->getOrderGoodsBcStatus($order_id);


                    $all_goods_filled = true;
                    $all_goods_is_kucun = true;
                    $all_goods_is_bc = true;

                    foreach ($bc_goods_list as $item) {
                        if (empty($item['goods_itemid']) || ($item['goods_itemid'] == $item['goods_id'] && $item['from_type'] == 6)) {
                            $all_goods_filled = false;
                        }
                        if(empty($item['bc_id']) && $item['is_xianhuo'] == 0){
                            $all_goods_is_bc = false;
                        }

                        if ($item['is_on_sale'] != 2) {
                            $all_goods_is_kucun = false;
                        }
                    }

                    if(!$all_goods_is_bc && $order_info['order_state'] != ORDER_STATE_MAKING){
                        $order_state = ORDER_STATE_TO_BC;
                    }else {
                        $order_state = ORDER_STATE_MAKING;
                    }
                    if($payedMoney>=$order_info["order_amount"]) {
                            if ($all_goods_is_kucun) $order_state = ORDER_STATE_TOSEND; //所有货品是库存，订单调整为待发货
                            else if ($all_goods_filled) $order_state = ORDER_STATE_TO_SIGN; //所有货号已回调，订单调整为待签收

                    }


                }else{
                    if($payedMoney>=$order_info["order_amount"]) {
                        $order_state = ORDER_STATE_TOSEND;
                    }
                }

                $order_data = array(
                    "rcb_amount"=>$payedMoney,
                    "order_state"=>$order_state,
                    "pay_status"=>$pay_status,
                    "payment_code"=>$post["payment_code"]
                );
                //第一次付款时间
                if(empty($order_info['payment_time'])){
                    $order_data['payment_time'] = strtotime($array["pay_date"]);
                }

                $model_order->editOrder($order_data,["order_id"=>$order_id]);
                $data['log_orderstate'] = $order_state;

                $model_order->addOrderLog($data);
                $model_order->commit();

                //获取订单信息，发送到后端系统
                $latest_order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
                EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $latest_order_info));

            } catch (Exception $e){
                $model_order->rollback();
                return callback(false, $e->getMessage());
            }
            return callback(true,'操作成功');
        }
    }

    private function _order_bc($order_id,$post){
        $model_order = Model('order');
        if(!chksubmit()) {
            $order_info=$model_order->getOrderInfo(["order_id"=>$order_id]);
            if ($order_info["order_state"] != ORDER_STATE_TO_BC) {
                showMessage("当前订单不符合允许布产条件");
                exit();
            }

            $result = $model_order->getOrderGoodsList(["order_id"=>$order_id,'is_return'=>0,'is_xianhuo'=>0,'bc_status'=>array('elt',1)]);
            if(empty($result)){
                showMessage("当前订单不符合允许布产条件");
                exit();
            }

            Tpl::output('order_info',$order_info);
            Tpl::output('order_id',$order_info['order_id']);
            Tpl::output('order_sn',$order_info['order_sn']);
            Tpl::showpage('store_order.buchan','null_layout');
            exit();
        }
        $state_type = $post['state_type'];
        if($state_type == 1){
            $model_order->beginTransaction();
            try {
                $model_order->editOrder(["order_state" => ORDER_STATE_MAKING], ["order_id" => $order_id]);
                //记录订单日志
                $data = array();
                $data['order_id'] = $order_id;
                $data['log_role'] = 'seller';
                $data['log_user'] = $_SESSION['seller_name'];
                $data['log_msg'] = '订单布产信息已确认，待生产系统接单';
                $data['log_orderstate'] = ORDER_STATE_MAKING;
                $model_order->addOrderLog($data);
                $model_order->commit();
                
                // 提前触发
                $order_to_sync = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
                //获取订单信息，发送到后端系统
                EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $order_to_sync));
                
            }catch (Exception $e){
                $model_order->rollback();
                return callback(false, $e->getMessage());
            }
            return callback(true,'操作成功');
        }else{
            return callback(false, "取消操作");
        }

    }

    private function _order_log($order_info, $post){
        $model_order = new orderModel();//Model('order');
        $order_log_list = $model_order->getOrderLogList(array('order_id'=>$order_info['order_id']),'log_id desc',"*",null,10);
        Tpl::output('order_log_list',$order_log_list);
        Tpl::output('order_sn',$_GET["order_sn"]);
        Tpl::output('show_page',$model_order->showpage());
        Tpl::showpage('store_order.view_logs','simple_layout');
        exit();
    }

    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_type='',$menu_key='') {
        Language::read('member_layout');
        switch ($menu_type) {
            case 'list':
            $menu_array = array(
            array('menu_key'=>'store_order',        'menu_name'=>Language::get('nc_member_path_all_order'), 'menu_url'=>'index.php?act=store_order'),
            array('menu_key'=>'state_toconfirm',    'menu_name'=>Language::get('nc_member_path_wait_confirm'),  'menu_url'=>'index.php?act=store_order&op=index&state_type=state_toconfirm'),
            array('menu_key'=>'state_topay',        'menu_name'=>Language::get('nc_member_path_wait_pay'),  'menu_url'=>'index.php?act=store_order&op=index&state_type=state_topay'),
            array('menu_key'=>'state_tobc',        'menu_name'=>Language::get('nc_member_path_wait_bc'),  'menu_url'=>'index.php?act=store_order&op=index&state_type=state_tobc'),
            array('menu_key'=>'state_making',       'menu_name'=>Language::get('nc_member_path_making'),  'menu_url'=>'index.php?act=store_order&op=index&state_type=state_making'),           
            array('menu_key'=>'state_tosign',       'menu_name'=>Language::get('nc_member_path_wait_sign'),  'menu_url'=>'index.php?act=store_order&op=index&state_type=state_tosign'),
            array('menu_key'=>'state_tosend',       'menu_name'=>Language::get('nc_member_path_wait_send'), 'menu_url'=>'index.php?act=store_order&op=index&state_type=state_tosend'),
            array('menu_key'=>'state_success',      'menu_name'=>Language::get('nc_member_path_finished'),  'menu_url'=>'index.php?act=store_order&op=index&state_type=state_success'),
            //array('menu_key'=>'state_pending_receive','menu_name'=>'待自提','menu_url'=>'index.php?act=store_order&op=index&state_type=state_pending_receive'),
            array('menu_key'=>'state_cancel',       'menu_name'=>Language::get('nc_member_path_canceled'),  'menu_url'=>'index.php?act=store_order&op=index&state_type=state_cancel'),
            );
            break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
