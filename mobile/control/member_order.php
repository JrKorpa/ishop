<?php
/**
 * 我的订单
 *
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class member_orderControl extends mobileMemberControl {

    public function __construct(){
        parent::__construct();
    }

    /**
     * 订单列表
     */
    public function order_listOp() {
        $model_order = Model('order');
        $condition = array();
        $condition['seller_id'] = $this->member_info['member_id'];
        $store_id = $this->member_info['store_id'];
        $condition['store_id'] = $store_id;
        $condition['order_type'] = array('in',array(1,3));

        if ($_POST['state_type'] != '') {
            $condition['order_state'] = str_replace(
                array('state_new','state_send','state_noeval'),
                array(ORDER_STATE_NEW,ORDER_STATE_SEND,ORDER_STATE_SUCCESS), $_POST['state_type']);

            $allow_state_array = array('state_toconfirm', 'state_topay','state_tomaking','state_making','state_tosign', 'state_tosend', 'state_success','state_cancel');
            if (in_array($_POST['state_type'], $allow_state_array)) {
                $condition['order_state'] = str_replace($allow_state_array,
                    array(ORDER_STATE_TO_CONFIRM, ORDER_STATE_NEW, ORDER_STATE_TO_BC, ORDER_STATE_MAKING, ORDER_STATE_TO_SIGN, ORDER_STATE_TOSEND,ORDER_STATE_SUCCESS,ORDER_STATE_CANCEL), $_POST['state_type']);
            }
        }else{
            $condition['order_state'] = array('gt',0);
        }
        if ($_POST['search_val'] !== '') {
            if(preg_match("/^1[34578]{1}\d{9}$/",$_POST['search_val'])){
                $condition['buyer_phone'] = $_POST['search_val'];
            }elseif(preg_match('/[\x{4e00}-\x{9fa5}]/u', $_POST['search_val'])>0){
                $condition['buyer_name'] = $_POST['search_val'];
            }else{
                $condition['order_sn'] = $_POST['search_val'];
            }
            unset($condition['seller_id'],$condition['store_id']);
        }
        if ($_POST['goods_sn'] !== '') {
            $where = array();
            if(preg_match('/[A-Za-z]+/',$_POST['goods_sn'])){
                $where['style_sn'] = $_POST['goods_sn'];
            }else{
                $where['goods_id'] = $_POST['goods_sn'];
            }
            $order_ids = $model_order->getOrderGoodsList($where, "`order_id`");
            $order_ids = array_column($order_ids,'order_id');
            $condition['order_id'] = array('in', $order_ids);
        }
        if ($_POST['starttime'] !== '' && $_POST['endtime'] !== '') {
            $starttime = strtotime($_POST['starttime']." 00:00:00");
            $endtime = strtotime($_POST['endtime']." 23:59:59");
            $condition['add_time'] = array('between', "{$starttime},{$endtime}");
        }else if ($_POST['starttime'] !== '' && $_POST['endtime'] == '') {
            $condition['add_time'] =array("gt", strtotime($_POST['starttime']." 00:00:00"));
        }else if ($_POST['endtime'] !== '' && $_POST['starttime'] == '') {
            $condition['add_time'] =array("lt", strtotime($_POST['endtime']."23:59:59"));
        }
        if ($_POST['is_zp'] !== '') {
            $condition['is_zp'] = $_POST['is_zp'];
        }
        if ($_POST['is_xianhuo'] !== '') {
            $condition['is_xianhuo'] = $_POST['is_xianhuo'];
        }
        /*
        if ($_POST['state_type'] == 'state_new') {
            $condition['chain_code'] = 0;
        }
        if ($_POST['state_type'] == 'state_noeval') {
            $condition['evaluation_state'] = 0;
            $condition['order_state'] = ORDER_STATE_SUCCESS;
        }
        if ($_POST['state_type'] == 'state_notakes') {
            $condition['order_state'] = array('in',array(ORDER_STATE_NEW,ORDER_STATE_PAY));
            $condition['chain_code'] = array('gt',0);
        }
        */
        if (preg_match('/^\d{10,20}$/',$_POST['order_key'])) {
            $condition['order_sn'] = $_POST['order_key'];
        } elseif ($_POST['order_key'] != '') {
            $condition['order_id'] = array('in',$this->_getOrderIdByKeyword($_POST['order_key']));
        }
        $order_list_array = $model_order->getNormalOrderList($condition, $this->page, '*', 'order_id desc','', array('order_goods'));
        $order_num = $model_order->getOrderCount($condition);
        $order_num = !empty($order_num)?$order_num:0;
        $model_refund_return = Model('refund_return');
        if(!empty($order_list_array)){
            $order_list_array = $model_refund_return->getGoodsRefundList($order_list_array,1);//订单商品的退款退货显示
        }

        $ownShopIds = Model('store')->getOwnShopIds();

        $order_group_list = array();
        $order_pay_sn_array = array();
        foreach ($order_list_array as $value) {
            $value['zengpin_list'] = array();
            //显示取消订单
            $value['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel',$value, $store_id);
            //显示审核按钮
            $value['if_check'] = $model_order->getOrderOperateState('check_order',$value, $store_id);
            //显示布产按钮
            $value['if_buchan'] = $model_order->getOrderOperateState('buchan_bar',$value, $store_id);
            //显示退款取消订单
            $value['if_refund_cancel'] = $model_order->getOrderOperateState('refund_cancel',$value, $store_id);
            //显示收货
            //$value['if_receive'] = $model_order->getOrderOperateState('receive',$value, $store_id);
            //显示锁定中
            $value['if_lock'] = $model_order->getOrderOperateState('lock',$value, $store_id);
            //显示物流跟踪
            $value['if_deliver'] = $model_order->getOrderOperateState('deliver',$value, $store_id);
            //显示评价
            //$value['if_evaluation'] = $model_order->getOrderOperateState('evaluation',$value, $store_id);
            //显示追加评价
            //$value['if_evaluation_again'] = $model_order->getOrderOperateState('evaluation_again',$value, $store_id);
            //显示删除订单(放入回收站)
            $value['if_delete'] = $model_order->getOrderOperateState('delete',$value, $store_id);

            $value['ownshop'] = in_array($value['store_id'], $ownShopIds);
            //显示允许布产
            $value['show_bc']=false;
            if($value["order_state"] == ORDER_STATE_TO_BC && $store_id == $value['store_id']){
                $value['show_bc']=true;
            }

            if(!empty($value['extend_order_goods'])){
                //商品图
                foreach ($value['extend_order_goods'] as $k => $goods_info) {
                    if(preg_match("/^http/is",$goods_info['goods_image'])){
                        $value['extend_order_goods'][$k]['goods_image_url'] = $goods_info['goods_image'];
                    }else{
                        $value['extend_order_goods'][$k]['goods_image_url'] = cthumb($goods_info['goods_image'], 240, $value['store_id']);
                    }
                    $value['extend_order_goods'][$k]['refund'] = $value['extend_order_goods'][$k]['refund'] ? true : false;
                    unset($value['extend_order_goods'][$k]['rec_id']);
                    unset($value['extend_order_goods'][$k]['order_id']);
                    //unset($value['extend_order_goods'][$k]['goods_pay_price']);
                    unset($value['extend_order_goods'][$k]['store_id']);
                    unset($value['extend_order_goods'][$k]['buyer_id']);
                    unset($value['extend_order_goods'][$k]['promotions_id']);
                    unset($value['extend_order_goods'][$k]['commis_rate']);
                    unset($value['extend_order_goods'][$k]['gc_id']);
                    unset($value['extend_order_goods'][$k]['goods_contractid']);
                    unset($value['extend_order_goods'][$k]['goods_image']);
                    if ($value['extend_order_goods'][$k]['goods_type'] == 5) {
                        $value['zengpin_list'][] = $value['extend_order_goods'][$k];
                        unset($value['extend_order_goods'][$k]);
                    }
                }  
            }
            
            if ($value['extend_order_goods']) {
                $value['extend_order_goods'] = array_values($value['extend_order_goods']);
            }

            $order_group_list[$value['pay_sn']]['order_list'][] = $value;

            //如果有在线支付且未付款的订单则显示合并付款链接
            if ($value['order_state'] == ORDER_STATE_NEW) {
                $order_group_list[$value['pay_sn']]['pay_amount'] += $value['order_amount'] - $value['rcb_amount'] - $value['pd_amount'];
            }
            $order_group_list[$value['pay_sn']]['add_time'] = $value['add_time'];

            //记录一下pay_sn，后面需要查询支付单表
            $order_pay_sn_array[] = $value['pay_sn'];
        }
        $new_order_group_list = array();
        foreach ($order_group_list as $key => $value) {
            $value['pay_sn'] = strval($key);
            $new_order_group_list[] = $value;
        }
        $page_count = $model_order->gettotalpage();

        output_data(array('order_group_list' => $new_order_group_list, 'order_num'=>$order_num), mobile_page($page_count));
    }

    private function _getOrderIdByKeyword($keyword) {
        $goods_list = Model('order')->getOrderGoodsList(array('goods_name'=>array('like','%'.$keyword.'%')),'order_id',100,null,'', null,'order_id');
        return array_keys($goods_list);
    }

    /**
     * 取消订单
     */
    public function order_cancelOp() {
        $model_order = Model('order');
        $logic_order = Logic('order');
        $order_id = intval($_POST['order_id']);

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_type'] = array('in',array(1,3));
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('buyer_cancel',$order_info, $this->member_info['store_id']);
        if (!$if_allow) {
            output_error('无权操作');
        }
        //if (TIMESTAMP - 86400 < $order_info['api_pay_time']) {
        //    $_hour = ceil(($order_info['api_pay_time']+86400-TIMESTAMP)/3600);
        //    output_error('该订单曾尝试使用第三方支付平台支付，须在'.$_hour.'小时以后才可取消');
        //}
        $result = $logic_order->changeOrderStateCancel($order_info,'buyer', $this->member_info['member_name'], '其它原因');
        if(!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }
    }

    /**
     * 取消订单
     */
    public function order_deleteOp() {
        $model_order = Model('order');
        $logic_order = Logic('order');
        $order_id = intval($_POST['order_id']);
    
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_type'] = array('in',array(1,3));
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('delete',$order_info, $this->member_info['store_id']);
        if (!$if_allow) {
            output_error('无权操作');
        }

        $result = $logic_order->changeOrderStateRecycle($order_info,'buyer','delete');
        if(!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }
    }

    /**
     * 刪除商品
     */
    public function goods_deleteOp() {
        $model_order = Model('order');
        $logic_order = Logic('order');
        $order_id = intval($_POST['order_id']);
        $rec_id = intval($_POST['rec_id']);
        //重新計算訂單金額
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getOrderInfo($condition);
        $goodsinfo = $model_order->getOrderGoodsList($condition);
        if(count($goodsinfo)<=1){
            output_error("订单至少有一件商品");
        }

        $condition = array();
        $condition['rec_id'] = $rec_id;
        $result = $model_order->deleteOrderGoodsInfo($condition);
        if($result !== true){
            output_error("删除失败！");
        }

        //重新計算訂單金額
        $condition = array();
        $condition['order_id'] = $order_id;
        $goodsinfo = $model_order->getOrderGoodsList($condition, "`goods_pay_price`,`goods_price`");

        $new_price = 0;
        $goods_price_all = 0;
        if(!empty($goodsinfo)){
            foreach ($goodsinfo as $key => $value) {
                $goods_price = ncPriceFormat($value['goods_price']);
                $goods_pay_price = ncPriceFormat($value['goods_pay_price']);
                $goods_price_all = ncPriceFormat($goods_price_all+$goods_price);
                $new_price = ncPriceFormat($new_price+$goods_pay_price);
            }
        }

        //跟新訂單縂金額
        $condition = array();
        $condition['order_id'] = $order_id;
        $data = array();
        $data['goods_amount'] = $goods_price_all;
        $data['order_amount'] = $new_price;
        $res = $model_order->editOrder($data, $condition);
        if($res){

            //添加订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = "seller";
            $data['log_msg'] = '删除货品';
            $data['log_user'] = $this->member_info['member_name'];
            $data['log_orderstate'] = $order_info['order_state'];
            $model_order->addOrderLog($data);

            output_data('1');
        }else{
            output_error("删除失败！");
        }
        /*$condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_type'] = array('in',array(1,3));
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('delete',$order_info);
        if (!$if_allow) {
            output_error('无权操作');
        }

        $result = $logic_order->changeOrderStateRecycle($order_info,'buyer','delete');
        if(!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }*/
    }

    /**
     * 订单确认收货
     */
    public function order_receiveOp() {
        $model_order = Model('order');
        $logic_order = Logic('order');
        $order_id = intval($_POST['order_id']);

        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_type'] = 1;
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('receive',$order_info,$this->member_info['store_id']);
        if (!$if_allow) {
            output_error('无权操作');
        }

        $result = $logic_order->changeOrderStateReceive($order_info,'buyer', $this->member_info['member_name'],'签收了货物');
        if(!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }
    }

    /**
     * 物流跟踪
     */
    public function search_deliverOp(){
        $order_id   = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }

        $model_order    = Model('order');
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'],array(ORDER_STATE_SEND,ORDER_STATE_SUCCESS))) {
            output_error('订单不存在');
        }

        $express = rkcache('express',true);
        $e_code = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
        $e_name = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
        $deliver_info = $this->_get_express($e_code, $order_info['shipping_code']);
        output_data(array('express_name' => $e_name, 'shipping_code' => $order_info['shipping_code'], 'deliver_info' => $deliver_info));
    }

    /**
     * 取得当前的物流最新信息
     */
    public function get_current_deliverOp(){
        $order_id   = intval($_POST['order_id']);
        if ($order_id <= 0) {
            output_error('订单不存在');
        }
    
        $model_order    = Model('order');
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
        if (empty($order_info) || !in_array($order_info['order_state'],array(ORDER_STATE_SEND,ORDER_STATE_SUCCESS))) {
            output_error('订单不存在');
        }
    
        $express = rkcache('express',true);
        $e_code = $express[$order_info['extend_order_common']['shipping_express_id']]['e_code'];
        $e_name = $express[$order_info['extend_order_common']['shipping_express_id']]['e_name'];
        $content = Model('express')->get_express($e_code, $order_info['shipping_code']);
        if (empty($content)) {
            output_error('物流信息查询失败');
        } else {
            foreach ($content as $k=>$v) {
                if ($v['time'] == '') continue;
                output_data(array('deliver_info'=>$content[0]));
            }
            output_error('物流信息查询失败');
        }
    }

    /**
     * 从第三方取快递信息
     *
     */
    public function _get_express($e_code, $shipping_code){

        $content = Model('express')->get_express($e_code, $shipping_code);
        if (empty($content)) {
            output_error('物流信息查询失败');
        }
        $output = array();
        foreach ($content as $k=>$v) {
            if ($v['time'] == '') continue;
            $output[]= $v['time'].'&nbsp;&nbsp;'.$v['context'];
        }

        return $output;
    }

    public function order_infoOp() {
        $logic_order = logic('order');
        $result = $logic_order->getMemberOrderInfo($_GET['order_id'],$this->member_info['member_id'],$this->member_info['store_id']);
        if (!$result['state']) {
            output_error($result['msg']);
        }
        //var_dump($result);die;
        $data = array();
        $order_data=$result['data']['order_info'];
        $data['order_id'] = $order_data['order_id'];
        $data['order_sn'] = $order_data['order_sn'];
        //$data['store_id'] = $order_data['store_id'];
        $data['buyer_name'] = $order_data['buyer_name'];
        $data['buyer_phone'] = $order_data['buyer_phone'];
        $data['store_name'] = $order_data['store_name'];
        $data['add_time'] = date('Y-m-d H:i:s',$order_data['add_time']);
        $data['payment_time'] = $order_data['payment_time'] ? date('Y-m-d H:i:s',$order_data['payment_time']) : '';
        $data['shipping_time'] = $order_data['extend_order_common']['shipping_time'] ? date('Y-m-d H:i:s',$order_data['extend_order_common']['shipping_time']) : '';
        $data['finnshed_time'] = $order_data['finnshed_time'] ? date('Y-m-d H:i:s',$order_data['finnshed_time']): '';
        $data['goods_amount'] = ncPriceFormat($order_data['goods_amount']);//商品总金额
        $data['order_amount'] = ncPriceFormat($order_data['order_amount']);//订单总金额
        $data['real_pay_amount'] = ncPriceFormat($order_data['rcb_amount']);//已付金额
        $data['refund_amount'] = ncPriceFormat($order_data['refund_amount']);
        $data['breach_amount'] = ncPriceFormat($order_data['breach_amount']);//违约金
       /*
        $balance = $data['order_amount'];
        if(isset($result['data']['order_pay_action']['balance']) && !empty($result['data']['order_pay_action']['balance'])){
            $balance = $result['data']['order_pay_action']['balance'];
        }
        $data['order_balance'] = ncPriceFormat($balance);//未付金额
       */
        $data['order_balance'] = bcadd(bcsub($data['order_amount'] , $data['real_pay_amount']), bcadd($data['refund_amount'],$data['breach_amount']));
        $data['favorable_price'] = 0;//商品优惠总金额
        $data['shipping_fee'] = ncPriceFormat($order_data['shipping_fee']);
        //$real_pay_amount = bcsub($data['order_amount'], $balance);


        $data['real_amount'] = bcsub($data['real_pay_amount'], $data['refund_amount'], 2);
//         $data['evaluation_state'] = $order_data['evaluation_state'];
//         $data['evaluation_again_state'] = $order_data['evaluation_again_state'];
//         $data['refund_state'] = $order_data['refund_state'];
        $data['state_desc'] = $order_data['state_desc'];
        $data['order_state'] = $order_data['order_state'];
        $data['pay_status'] = $order_data['pay_status'];
        $data['payment_name'] = $order_data['payment_name'];
        $data['order_message'] = $order_data['remark'];
        $data['distribution_type'] = $order_data['extend_order_common']['distribution_type'];
        //$data['reciver_phone'] = $order_data['buyer_phone'];
        //$data['reciver_name'] = $order_data['extend_order_common']['reciver_name'];
        //$data['reciver_addr'] = $order_data['extend_order_common']['reciver_info']['address'];
        $data['reciver_sear'] =$order_data['extend_order_common']['reciver_info']['address'];
        $data['provice_id'] = $order_data['extend_order_common']['reciver_province_id'];
        $data['city_id'] = $order_data['extend_order_common']['reciver_city_id'];
        $data['area_id'] = $result['data']['daddress_info']['area_id'];
        $data['reciver_info'] = $order_data['extend_order_common']['reciver_info'];//收货地址
        $data['reciver_name'] = $data['reciver_info']['true_name'];
        $data['reciver_phone'] = $data['reciver_info']['mob_phone'];
        $data['reciver_addr'] = $data['reciver_info']['address'];
        $data['store_member_id'] = $order_data['extend_store']['member_id'];
        $data['store_phone'] = $order_data['extend_store']['store_phone'];
        $data['order_tips'] = $order_data['order_state'] == ORDER_STATE_NEW ? '请于'.ORDER_AUTO_CANCEL_TIME.'小时内完成付款，逾期未付订单自动关闭' : '';
        $_tmp = $order_data['extend_order_common']['invoice_info'];
        $_invonce = '';
        if (is_array($_tmp) && count($_tmp) > 0) {
            foreach ($_tmp as $_k => $_v) {
                $_invonce .= $_k.'：'.$_v.' ';
            }
        }
        $_tmp = $order_data['extend_order_common']['promotion_info'];
        $data['promotion'] = array();
        if(!empty($_tmp)){
            $pinfo = unserialize($_tmp);
            if (is_array($pinfo) && $pinfo){
                foreach ($pinfo as $pk => $pv){
                    if (!is_array($pv) || !is_string($pv[1]) || is_array($pv[1])) {
                        $pinfo = array();
                        break;
                    }
                    $pinfo[$pk][1] = strip_tags($pv[1]);
                }
                $data['promotion'] = $pinfo;
            }
        }
        //客户来源
        $data['source_name'] = "";
        if(!empty($order_data['customer_source_id'])){
            $management_api = data_gateway('imanagement');
            $customer_source_id = $order_data['customer_source_id'];
            $res = $management_api->get_customer_sources_list(array('ids'=>$customer_source_id));
            if(!empty($res['return_msg'])){
                $customer_source_info  =  $res['return_msg'][0];
                $data['source_name'] = $customer_source_info['source_name'];
            }
        }
        $data['invoice'] = rtrim($_invonce);
        $data['if_deliver'] = $order_data['if_deliver'];
        $data['if_check_order'] = $order_data['if_check_order'];
        $data['if_buyer_cancel'] = $order_data['if_buyer_cancel'];
        $data['if_add_zp'] = $order_data['if_add_zp'];
        $data['if_edit_addr'] = $order_data['if_edit_addr'];
        $data['if_edit_goods'] = $order_data['if_edit_goods'];
        $data['if_refund_cancel'] = $order_data['if_refund_cancel'];
        $data['if_receive'] = $order_data['if_receive'];
        $data['if_evaluation'] = $order_data['if_evaluation'];
        $data['if_lock'] = $order_data['if_lock'];
        $data['if_return_price'] = $order_data['if_return_price'];
        $data['if_return_goods'] = $order_data['if_return_goods'];
        $data['if_return_all'] = $order_data['if_return_all'];
        $data['goods_list'] = array();
        $is_tuikuan_arr = !empty($result['data']['return_goods_ing'])?$result['data']['return_goods_ing']:array();
        if(isset($order_data['goods_list']) && !empty($order_data['goods_list'])){
            foreach ($order_data['goods_list'] as $_k => $_v) {
                $is_tuikuan = true;
                if(in_array($_v['rec_id'], $is_tuikuan_arr)) $is_tuikuan = false;
                $data['goods_list'][$_k]['rec_id'] = $_v['rec_id'];
                $data['goods_list'][$_k]['goods_id'] = $_v['goods_id'];
                $data['goods_list'][$_k]['goods_itemid'] = $_v['goods_itemid'];
                $data['goods_list'][$_k]['style_sn'] = $_v['style_sn'];
                $data['goods_list'][$_k]['goods_name'] = $_v['goods_name'];
                $data['goods_list'][$_k]['goods_price'] = ncPriceFormat($_v['goods_price']);
                $data['goods_list'][$_k]['goods_pay_price'] = ncPriceFormat($_v['goods_pay_price']);
                $data['goods_list'][$_k]['goods_num'] = $_v['goods_num'];
                $data['goods_list'][$_k]['goods_spec'] = $_v['goods_spec'];
                $data['goods_list'][$_k]['image_url'] = empty($_v['goods_image']) ? C('order_detail_def_page') : $_v['goods_image'];
                $data['goods_list'][$_k]['carat'] = $_v['carat'];
                $data['goods_list'][$_k]['clarity'] = $_v['clarity'];
                $data['goods_list'][$_k]['color'] = $_v['color'];
                $data['goods_list'][$_k]['cut'] = $_v['cut'];
                $data['goods_list'][$_k]['caizhi'] = $_v['caizhi'];
                $data['goods_list'][$_k]['jinse'] = $_v['jinse'];
                $data['goods_list'][$_k]['jinzhong'] = $_v['jinzhong'];
                $data['goods_list'][$_k]['zhiquan'] = $_v['zhiquan'];
                $data['goods_list'][$_k]['xiangkou'] = $_v['xiangkou'];
                $data['goods_list'][$_k]['face_work'] = $_v['face_work'];
                $data['goods_list'][$_k]['xiangqian'] = $_v['xiangqian'];
                $data['goods_list'][$_k]['cert_id'] = $_v['cert_id'];
                $data['goods_list'][$_k]['cert_type'] = $_v['cert_type'];
                $data['goods_list'][$_k]['zhushi_num'] = $_v['zhushi_num'];
                $data['goods_list'][$_k]['tuo_type'] = $_v['tuo_type'];
                $data['goods_list'][$_k]['kezi'] = formatKezi($_v['kezi']);
                $data['goods_list'][$_k]['is_return'] = $_v['is_return'];
                $data['goods_list'][$_k]['is_tuikuan'] = $is_tuikuan;
                $data['goods_list'][$_k]['discount_code'] = $_v['discount_code'];
                $data['goods_list'][$_k]['is_cpdz'] = $_v['is_cpdz'];
                $data['goods_list'][$_k]['xianhuo_adds'] = $_v['xianhuo_adds'];
                $data['goods_list'][$_k]['if_edit_goods'] = $_v['if_edit_goods'];
                //商品优惠金额
                $data['favorable_price'] += bcsub($_v['goods_price'], $_v['goods_pay_price'], 2);
            }
        }
        $data['zengpin_list'] = array();
        foreach ($order_data['zengpin_list'] as $_k => $_v) {
            $is_tuikuan = true;
            if(in_array($_v['rec_id'], $is_tuikuan_arr)) $is_tuikuan = false;
            $data['zengpin_list'][$_k]['rec_id'] = $_v['rec_id'];
            $data['zengpin_list'][$_k]['goods_name'] = $_v['goods_name'];
            $data['zengpin_list'][$_k]['style_sn'] = $_v['style_sn'];
            $data['zengpin_list'][$_k]['image_url'] = empty($_v['goods_image']) ? C('order_detail_def_page') : $_v['goods_image'];
            $data['zengpin_list'][$_k]['zhiquan'] = $_v['zhiquan'];
            $data['zengpin_list'][$_k]['goods_price'] = ncPriceFormat($_v['goods_price']);
            $data['zengpin_list'][$_k]['goods_pay_price'] = ncPriceFormat($_v['goods_pay_price']);
            $data['zengpin_list'][$_k]['goods_num'] = $_v['goods_num'];
            $data['zengpin_list'][$_k]['rec_id'] = $_v['rec_id'];
            $data['zengpin_list'][$_k]['is_finance'] = $_v['is_finance'];
            $data['zengpin_list'][$_k]['is_return'] = $_v['is_return'];
            $data['zengpin_list'][$_k]['discount_code'] = $_v['discount_code'];
            $data['zengpin_list'][$_k]['is_tuikuan'] = $is_tuikuan;


            $data['favorable_price'] += bcsub($_v['goods_price'], $_v['goods_pay_price'], 2);
        }
        //<editor-fold desc="订单日志">
        $order_log_list=$order_data['extend_order_log'];
        //首次去前五条
        $data['order_log_list'] = array_slice($order_log_list,0,8);
        $total_count=count($order_log_list);
        $page_count=$total_count%8==0?($total_count/8):ceil($total_count/8);
        $data['order_log_page_count']=$page_count;

        $api = data_gateway('isales');
        $product_log_list = $api->get_Bclog_list(8,1,array('order_sn' =>$data["order_sn"]));
        $data['product_log_page_count']=empty($product_log_list['return_msg']['pageCount'])?0:$product_log_list['return_msg']['pageCount'];
        $product_log_list=is_array($product_log_list['return_msg']['data'])?$product_log_list['return_msg']['data']:array();
        $data['product_log_list']=$product_log_list;
        //</editor-fold>
        $ownShopIds = Model('store')->getOwnShopIds();
        $data['ownshop'] = in_array($data['store_id'], $ownShopIds);
        $model_order = Model('order');
        //显示布产按钮
        $data['if_buchan'] = $model_order->getOrderOperateState('buchan_bar', $order_data, $this->member_info['store_id']);
        output_data(array('order_info'=>$data));
    }

    /**
     * 获取本地订单日志
     */
    public function get_order_log_listOp(){
        $order_id=$_POST["order_id"];
        $page=$_POST["page"];
        $page_size=$_POST["page_size"];
        $model_order = Model('order');
        $data=$model_order->getOrderLogPageList($order_id,$page,$page_size);
        output_data(array('order_log_list'=>$data));
    }

    /**
     * 获取生产系统订单日志
     */
    public function get_order_product_log_listOp(){
        $order_sn=$_POST["order_sn"];
        $page=$_POST["page"];
        $page_size=$_POST["page_size"];

        $api = data_gateway('isales');
        $product_log_list = $api->get_Bclog_list($page_size,$page,array('order_sn' =>$order_sn));
        $product_log_list=is_array($product_log_list['return_msg']['data'])?$product_log_list['return_msg']['data']:array();
        output_data(array('product_log_list'=>$product_log_list));
    }

    //取商品信息
    public function get_order_goods_listOp()
    {
        $model_order = Model('order');
        $goods_list = $where = array();
        $where['rec_id'] = isset($_POST['rec_id'])?$_POST['rec_id']:'';
        $style_sn = isset($_POST['style_sn'])?$_POST['style_sn']:'';
        if(!empty($_POST['rec_id']) && !empty($style_sn)){
            $styleApi = data_gateway('istyle');
            $keys = array("tuo_type","confirm","xiangqian","zhushi_num","cert","color","clarity","cut","facework",'kezi');
                
            $result = $styleApi->get_style_goods_diy_index($keys,$style_sn);
            if($result['error']==1){
                output_error($result['error_msg']);
            }
            $goods_list = $model_order->getOrderGoodsInfo($where);
        }
        $data = array();
        $data['attr_list'] = $result['return_msg'];
        $data['goods_info'] = $goods_list;
        output_data($data);
    }

    /**
     * 用户商品编辑
     */
    public function goods_updateOp() {
        if($_POST['is_dingzhi']==""){
            output_error("请选择是否定制");
        }
        $salesApi = data_gateway('isales');
        $rec_id = $_POST['rec_id'];
        $goods_type = $_POST['goods_type'];
        $channel_id = $this->member_info['store_id'];
        $is_dingzhi = $_POST['is_dingzhi'];
        $is_xianhuo = $is_dingzhi==1?0:1;
        if($goods_type==1){
            //戒托
            $data = array(
                //'tuo_type'=>$_POST['tuo_type'],
                //'is_dingzhi'=>$is_dingzhi,
                'is_xianhuo'=>$is_xianhuo,
                'xiangqian'=>$_POST['xiangqian'],
                //'zhushi_num'=>$_POST['zhushi_num'],
                //'cert'=>$_POST['cert'],
                'cert_id'=>$_POST['cert_id'],
                //'carat' => $_POST['carat'],
                //'color' => $_POST['color'],
                //'cut' => $_POST['cut'],                
                //'clarity' => $_POST['clarity'],
                'face_work' => htmlspecialchars_decode($_POST['facework']),
                'kezi' => htmlspecialchars_decode($_POST['kezi']),
                //'is_cpdz'=>$is_cpdz,
                //'cpdz_code' => $_POST['cpdz_code'],
                //'goods_price' => $goods_price,
                //'goods_pay_price' => $goods_price,
                //'others' => $_POST['others'],
            );
        }else{
            output_error("goods_type 参数错误");
        }
        $this->goods_edit($rec_id, $goods_type, $data);
    }

    /**
     * 订单明细修改商品
     * @param unknown $goods_id
     * @param unknown $goods_type
     * @param unknown $data
     */
    private function goods_edit($rec_id,$goods_type,$data){
        $model_goods = Model("order");
        $member_id = $this->member_info['member_id'];
        $store_id = $this->store_info['store_id'];
        if(empty($rec_id)){
            output_error("rec_id不能为空");
        }
        $where = array();
        $where['rec_id'] = $rec_id;
        $res =false;
        if($goods_type == 1){
            //var_dump($data, $where);die;
            $res = $model_goods->editOrderGoods($data, $where);
        }
        if($res){
            $goods = $model_goods->getOrderGoodsInfo($where, 'order_id');
            $order_info = $model_goods->getOrderInfo(array('order_id'=>$goods['order_id']),array("order_common","order_goods","store","order_pay_action"));
            //获取订单信息，发送到后端系统
            EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $order_info));
            output_data($data);
        }else{
            output_error("保存失败");
        }
    }

    //订单详情保存地址
    public function editaddressOp()
    {
        $model_order = Model('order');
        $order_id = $_POST['order_id'];
        $reciver_info = $_POST;
        $reciver_info['phone'] = $_POST['mob_phone'];
        $reciver_info['street'] = $_POST['address'];
        unset($reciver_info['key'],$reciver_info['order_id']);
        $order_common = array();
        $order_common['reciver_info'] = serialize($reciver_info);
        $where = array('order_id' => $order_id);
        $res = $model_order->editOrderCommon($order_common, $where);
        if($res){
            output_data($reciver_info);
        }else{
            output_error("保存失败");
        }
    }

    /**
     * 赠品添加
     * gaopeng
     */
    public function gift_addOp(){
        $model_order = Model('order');
        if(empty($_POST['style_sn'])){
            output_error("请选择赠品");
        }
        if(empty($_POST['order_id'])){
            output_error("订单ID错误");
        }
        $order_id = $_POST['order_id'];
        $store_id = $this->store_info['store_id'];
        $store_name = $this->store_info['store_name'];
        $goods_id = $_POST['style_sn']."-".(int)$_POST['shoucun'];
        $goods_type = 5;//赠品

        $data = array(
            'order_id'=>$order_id,
            'goods_name'=>$_POST['goods_name'],
            'goods_id' =>$goods_id,
            'goods_num'=>1,
            'goods_type'=>5,
            'is_finance'=>$_POST['is_finance'],
            'style_sn'=>$_POST['style_sn'],            
            'zhiquan' =>$_POST['shoucun'],
            'goods_price' => $_POST['goods_price'],
            'goods_pay_price' => 0,
        );

        $data['store_id'] = $store_id;
        //$data['store_name'] = $store_name;
        $res = $model_order->addOrderGood($data);
        if($res){
            $condition = array();
            $condition['order_id'] = $order_id;
            $order_info = $model_order->getOrderInfo($condition);
            //添加订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = "seller";
            $data['log_msg'] = '添加赠品,'.$_POST['goods_name']."x1";
            $data['log_user'] = $this->member_info['member_name'];
            $data['log_orderstate'] = $order_info['order_state'];
            $model_order->addOrderLog($data);

            output_data($res);
        }else{
            output_error("保存失败");
        }
    }

    //添加订单日志
    public function add_order_logOp()
    {
        $model_order = Model('order');
        $order_id = $_POST['order_id'];
        if(empty($order_id)){
            output_error("订单ID错误");
        }
        $log_text = $_POST['log_text'];
        if(empty($log_text)){
            output_error("请填写订单日志");
        }
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getOrderInfo($condition);
        //添加订单日志
        $data = array();
        $data['order_id'] = $order_id;
        $data['log_role'] = "seller";
        $data['log_msg'] = '<span style="color:red">'.$log_text.'</span>';
        $data['log_user'] = $this->member_info['member_name'];
        $data['log_orderstate'] = $order_info['order_state'];
        $res = $model_order->addOrderLog($data);
        if(!$res){
            output_error("保存失败");
        }else{
            output_data($res);
        }
    }
    
    public function order_checkOp() {
        $model_order = Model('order');
        $logic_order = Logic('order');
        $order_id = intval($_POST['order_id']);
    
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['buyer_id'] = $this->member_info['member_id'];
        $condition['order_type'] = array('in',array(1,3));
        $order_info = $model_order->getOrderInfo($condition);
        $if_allow = $model_order->getOrderOperateState('confirm',$order_info,$this->member_info['store_id']);
        if (!$if_allow) {
            output_error('无权操作');
        }

        $result = $logic_order->changeOrderStateConfirm($order_info, $this->member_info['member_id'], $this->member_info['member_name']);
        if(!$result['state']) {
            output_error($result['msg']);
        } else {
            output_data('1');
        }
    }


    /**
     * 布产
     */
    public function order_bcOp() {
        $model_order = Model('order');
        $order_id = intval($_POST['order_id']);
        try {
            $model_order->beginTransaction();
            $model_order->editOrder(["order_state" => ORDER_STATE_MAKING], ["order_id" => $order_id]);
            //记录订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = 'seller';
            $data['log_user'] = $this->member_info['member_name'];
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
            output_error($e->getMessage());
        }
        output_data('1');
    }

    /**
     * 订单商品优惠打折
     */
    public function edit_goods_discountOp(){

        $rec_id = $_POST['rec_id'];
        $model = Model("order");
        $goods_info = $model->getOrderGoodsInfo(array('rec_id'=>$rec_id));
        $goods_type = $goods_info['goods_type'];
        //$goods_hand_price实际输入金额
        //$goods_hand_price_min 最小可输入金额
        $goods_hand_price = $goods_hand_price_min =  floatval($_POST['goods_hand_price']);
        $discount_code = $_POST['discount_code'];

        if($goods_hand_price < 0 || ($goods_hand_price == 0 && $goods_type != 5)){
            output_error("商品金额不合法");
        }

        if(empty($goods_info)){
            output_error("编辑对象不存在");
        }
        $order_id = $goods_info['order_id'];
        $member_id = $this->member_info['member_id'];       
        $store_id = $this->store_info['store_id'];
        $order_info = $model->getOrderInfo(array('order_id'=>$order_id),array('order_goods'));
        if ($order_info['store_id']!=$store_id) {
            output_error("非店面订单不能修改优惠金额");
        }
        //取出最低优惠金额；
        if($discount_code == ''){
            $discountinfo = $this->getDiscount($member_id, $goods_info);
            if($discountinfo['error'] == 1){
                $error = $discountinfo['error_msg'];
                output_error($error);
            }
            $favorable_money = isset($discountinfo['data']['favorable_money'])?$discountinfo['data']['favorable_money']:0;
            if($favorable_money <= 0 && $goods_type != 5){
                output_error("商品最低优惠金额异常！");
            }
            if($discount_code == '' && bccomp($goods_hand_price, $favorable_money) == -1  && $goods_type != 5){
                output_error("超出最低折扣权限，请输入商品折扣码打折！");
            }
        }
        $goods_price = $goods_info['goods_price'];
        try{ 
            $model->beginTransaction();
            if($discount_code !=''){
                $model_voucher = Model('voucher');
                //获取商品属于那种类型
                $type = $model_voucher->get_diamond_type($member_id, $goods_info);
                $voucher_info = $model_voucher->getVoucherInfo(array('voucher_code'=>$discount_code,'voucher_type'=>1));
                if(empty($voucher_info)){
                    output_error("折扣码不存在！");
                }else if($voucher_info['voucher_start_date']>time() || $voucher_info['voucher_end_date']<time()){
                    output_error("折扣码不在有效期内！");
                }else if($voucher_info['voucher_store_id']!=$store_id){
                    output_error("折扣码不属于当前门店！");
                }else if($goods_info['discount_code']!= $discount_code && $voucher_info['voucher_state']!=1){
                    throw new Exception("折扣码已被使用！");
                }else if($voucher_info['voucher_goods_type'] != $type){
                    output_error("折扣码类型与商品类型不一致！");
                }
                if($goods_hand_price == $goods_price){
                    output_error("亲，使用折扣券后，需要手动修改商品金额，请确认！");
                }

                $favorable_money   = round($goods_price*(1-$voucher_info['voucher_price']/100));
                $goods_hand_price_min = $goods_price - $favorable_money;
                //$str = "{$goods_hand_price}*(1-{$voucher_info['voucher_price']}/100)={$discount_price}";
                $goods_voucher_list = array($store_id=>array(
                    'voucher_id'=>$voucher_info['voucher_id'],                
                    'voucher_order_id'=>$order_id,                
                    'voucher_owner_id'=>$this->member_info['member_id'],
                    'voucher_owner_name'=>$this->member_info['member_name'],
                    'voucher_state'=>2,
                ));
                $result = Logic('queue')->editGoodsVoucherState($goods_voucher_list);
                if (!$result['state']) {
                    throw new Exception("商品折扣券处理失败！");
                }
            }
            if($goods_hand_price < $goods_hand_price_min) { 
                output_error("超出折扣码折扣范围,请确认并修改商品价格！");
            }else{
                $goods_pay_price = $goods_hand_price;
                $discount_price = $goods_price - $goods_pay_price;
            }
            
            $data = array(
                'goods_pay_price'=>$goods_pay_price,
                'discount_code'=>$discount_code,
            );
            $res = $model->editOrderGoods($data, array('rec_id'=>$rec_id));
            $order_info = $model->getOrderInfo(array('order_id'=>$order_id),array('order_goods'));
            if($res === false){
                throw new Exception("订单商品金额修改失败！");
            }
            
            if(empty($order_info) || empty($order_info['extend_order_goods'])){
                throw new Exception("订单查询失败！");
            }else if($order_info['order_state']>=30){
                throw new Exception("订单已发货,不能优惠改价！");
            }
            $shipping_fee = $order_info['shipping_fee'];
            $goods_amount = 0;
            $order_amount = 0;
            foreach ($order_info['extend_order_goods'] as $goods){
                $goods_amount += $goods['goods_pay_price'];
                //$order_amount = bcadd(bcsub($goods['goods_pay_price'],($goods['is_return'] == 1 ? $goods['refund_amount'] : 0), 2), $order_amount, 2);
                $order_amount = bcadd(($goods['is_return'] == 1 ? 0: $goods['goods_pay_price']),$order_amount, 2);
            }
            $order_amount = bcadd($order_amount, $shipping_fee, 2);
            if ($order_info['rcb_amount'] > $goods_amount) {
                throw new Exception("订单已付款金额大于订单金额，请先申请退款！");
            }

            $data = array('goods_amount'=>$goods_amount,'order_amount'=>$order_amount);
            $rcb_amount = $order_info['rcb_amount'];//已付款金额
            $refund_amount = $order_info['refund_amount'];//退款金额
            $breach_amount = $order_info['breach_amount'];//违约金
            if(($order_amount - $rcb_amount + $refund_amount +  $breach_amount) <= 0){
                $data['pay_status'] = ORDER_PAY_FULL;  //付款状态：已付款
            }elseif($rcb_amount > 0){
                $data['pay_status'] = ORDER_PAY_PART;  //付款状态：已付款
            }else{
                $data['pay_status'] = ORDER_PAY_TODO;
            }
            $res = $model->editOrder($data,array('order_id'=>$order_id));
            if($res ===false){
                throw new Exception("订单商品金额修改失败！");
            }
            
            //添加订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = "seller";
            if($discount_code==""){
               $data['log_msg'] = "货号{$goods_info['goods_id']} 手动调价，商品金额:{$goods_pay_price}元";
            }else{
               $data['log_msg'] = "货号{$goods_info['goods_id']} 折扣码优惠，商品金额：{$goods_pay_price}元,折扣码：{$discount_code}";
            }
            $data['log_user'] = $this->member_info['member_name'];
            $data['log_orderstate'] = $order_info['order_state'];
 
            $model->addOrderLog($data);
            
            $model->commit();

            //更新订单状态
            Logic('order')->auto_order_wf('', $order_id);

            output_data('1');
        }catch (Exception $e){
            $model->rollback();
            output_error($e->getMessage());
        }
    }
    
    /**
     * 折扣权限优惠金额\
     * user_id 用户ID
     * goods_info 商品信息
     */
    public function getDiscount($user_id, $goods_info)
    {

        $model_voucher = Model('voucher');
        $type = $model_voucher->get_diamond_type($user_id, $goods_info);
        $favorable_money = 0;
        $goods_price = isset($goods_info['goods_price']) ? $goods_info['goods_price']:'0';
        $result = array('error'=>1, 'error_msg'=>'', 'data'=>array());
       
        $discountmodel = model("base_lz_discount_config");
        $where = array(
            'user_id' => $user_id,
            'type' => $type,
            'enabled' => 1
        );
        $discountlist = $discountmodel->getBaseLzDiscountConfigInfo($where);
        if(empty($discountlist)){
            $result['error_msg'] = "您没有“".paramsHelper::echoOptionText('voucher_goods_type',$type)."”商品的折扣权限，请申请设置并开启！";
            return $result;
        }
        $zhekou = isset($discountlist['zhekou'])?$discountlist['zhekou']:0;
        if($zhekou <=1 && $zhekou>0){
            $favorable_money =bcmul($goods_price,$zhekou,3);
        }
        if($favorable_money){
            $result['error'] = 0;
            $result['data'] = array('favorable_money'=> $favorable_money, 'zhekou'=>$zhekou);;
            return $result;
        }
        $result['error_msg'] = "优惠金额错误！";
        return $result;
    }

    //获取客户来源
    public function get_sources_listOp()
    {
        $store_id = $this->member_info['store_id'];
        $store_company_id = $this->store_info['store_company_id'];
        $management_api = data_gateway('imanagement');
        //$res = $management_api->get_sources_list(array($store_id));
        //if(!empty($res['return_msg'])){
            //$res = $management_api->get_sources_list(array($store_id,17));//增值业务渠道ID=17
        //}else{//渠道没查到就根据公司查询
            $res = $management_api->get_customer_sources_list(array('company_ids'=>array($store_company_id)));
        //}
        $sourcelist = isset($res['return_msg'])?$res['return_msg']:array();
        $info = array();
        if(!empty($sourcelist)){
            foreach ($sourcelist as $key => $value) {
                $info[$value['source_code']] = $value['source_name'];
            }
        }
        $data = array();
        $data['sourcelist'] = $info;
        output_data($data);
    }

    //手机号获取客户信息
    public function get_vip_list_by_mobOp()
    {
        $khphone = !empty($_GET['khphone'])?$_GET['khphone']:"";
        if(empty($khphone)){
            output_error("手机号不能为空！"); 
        }
        $sales_api = data_gateway('isales');
        $res = $sales_api->get_vip_list_by_mob($khphone);
        $data = array();
        if(!empty($res)){
            $memberinfo = json_decode($res);
            $data = array(
                'khname'=>$memberinfo->member_name,
                'source_id'=>$memberinfo->source_code
                );

        }else{
            $data = array(
                'khname'=>'',
                'source_id'=>''
            );
            //output_error("手机号码不存在");
        }

        //如果下单客户手机号查有非本月已审核订单，且本月没有其他订单，则默认二次消费
        $orderModel = new  orderModel();
        $store_id = $this->member_info['store_id'];
        $where = array('audit_time'=>['lt',date('Y-m',time())."-01 00:00:00"],'store_id'=>$store_id,'is_zp'=>0);
        $res1 = $orderModel->getOrderCount($where);
        $where = array('audit_time'=>['egt',date('Y-m',time())."-01 00:00:00"],'store_id'=>$store_id,'is_zp'=>0);
        $res2 = $orderModel->getOrderCount($where);
        if($res1>0 && $res2 == 0){
            $data['source_id'] = "001100150588";
        }
        output_data($data);

    }

    //保存会员信息
    public function save_vip_infoOp()
    {
        $khphone = !empty($_GET['khphone'])?$_GET['khphone']:"";
        $khname  = !empty($_GET['khname'])?$_GET['khname']:"";
        $source_id = !empty($_GET['source_id'])?$_GET['source_id']:"";
        $xuqiu = !empty($_GET['xuqiu'])?$_GET['xuqiu']:"";
        $carat_min = !empty($_GET['carat_min'])?$_GET['carat_min']:"";
        $carat_max = !empty($_GET['carat_max'])?$_GET['carat_max']:"";
        if(empty($khphone)){
            output_error("手机号不能为空！"); 
        }
        if(empty($khname)){
            output_error("顾客姓名不能为空！"); 
        }
        $save_args = array(
            'khphone' =>$khphone,
            'khname' =>$khname,
            'source_id' =>$source_id,
            'xuqiu' =>$xuqiu,
            'carat_min' =>$carat_min,
            'carat_max' =>$carat_max
        );
        //$model = Model("order");
        try {
            //$model->beginTransaction();
            $post_data = array(
                'member_name'=>$khname,
                'cell_phone'=>$khphone,
                'source_channel'=>$this->member_info['store_id'],  //店铺ID
                'belong_channel'=>$this->member_info['store_id'],  //店铺ID
                'creator_id'=>$this->member_info['member_id'],//创建人编号
                'budget'=>$carat_min."-".$carat_max,  //预算
                'prod_demand'=>$xuqiu, //需求编号
                'source_code'=>$source_id,
                //'create_time'=>date('Y-m-d H:i:s')
            );
            $sales_api = data_gateway('isales');
            $rest = $sales_api->get_vip_list_by_mob($khphone);
            $province_id = 0;
            $city_id = 0;
            $region_id = 0;
            $address = "";
            if(!empty($rest)) {
                //unset($post_data['source_code']);
                $adress = json_decode($rest);
                $post_data['source_code'] = !empty($adress->source_code)?$adress->source_code:$source_id;
                $profile = $adress->profile;
                $province_id = $profile->province_id;
                $city_id = $profile->city_id;
                $region_id = $profile->region_id;
                $address = $profile->address;
            }
            $res = $sales_api->save_vip_info($post_data);
            if(in_array($res['code'], array('200', '201'))){
                $memberlist = json_decode($res[0]);
            }else{
                $error = json_decode($res[0]);
                throw new Exception("保存会员信息失败！".$error[0]->message);
            }
            //$model->commit();
            output_data(array('mob_phone'=>$save_args['khphone'],'true_name'=>$save_args['khname'],'source_id'=>$save_args['source_id'], 'province_id'=>$province_id, 'city_id'=>$city_id, 'region_id'=>$region_id, 'address'=>$address));
        } catch (Exception $e) {
            //$model->rollback();
            output_error($e->getMessage());
        }
    }

    //根据手机号获取会员信息
    public function getMemberInfoOp(){
        $khphone = !empty($_GET['khphone'])?$_GET['khphone']:"";
        if(empty($khphone)) return '';
        $sales_api = data_gateway('isales');
        $rest = $sales_api->get_vip_list_by_mob($khphone);
        $province_id = 0;
        $city_id = 0;
        $region_id = 0;
        $address = "";
        $true_name = "";
        $source_id = "";
        if(!empty($rest)) {
            $adress = json_decode($rest);
            $profile = $adress->profile;
            $true_name = $adress->member_name;
            $source_id = $adress->source_code;
            $province_id = $profile->province_id;
            $city_id = $profile->city_id;
            $region_id = $profile->region_id;
            $address = $profile->address;
        }
        $return_data = array(
            'mob_phone'=>$khphone,
            'true_name'=>$true_name,
            'source_id'=>$source_id,
            'province_id'=>$province_id, 
            'city_id'=>$city_id, 
            'region_id'=>$region_id, 
            'address'=>$address
            );
        output_data($return_data);
    }

    /**
     * 折扣商品类型\
     * user_id 用户ID
     * goods_info 商品信息
     */
    /*废弃
    public function get_shop_type($user_id, $goods_info){
        $result = array('error'=>1, 'error_msg'=>'', 'data'=>array());
        if(empty($user_id) || empty($goods_info)){
            $result['error_msg'] = "当前登陆人ID或商品信息不能为空！";
            return $result;
        };
        $diamond_api = data_gateway('idiamond');
        $diainfo = array();
        $type = 0;
        $cert_id = isset($goods_info['cert_id']) ? $goods_info['cert_id']:'';
        $style_sn = isset($goods_info['style_sn']) ? $goods_info['style_sn']:'';
        $cert = isset($goods_info['cert_type']) ? $goods_info['cert_type']:''; 
        $carat = isset($goods_info['carat']) ? $goods_info['carat']:'';
        if($style_sn == 'DIA'){  
            if($cert == 'HRD-S'){
                if($carat<0.5){
                    $type = 5;
                }else if($carat>=0.5 && $carat<1){
                    $type = 6;
                }else if($carat>=1 && $carat<1.5){
                    $type = 7;
                }else{
                    $type = 8;
                }
            }elseif($cert == 'HRD-D'){
                $type = 9;
            }else{
                if($carat<0.5){
                    $type = 1;
                }else if($carat>=0.5 && $carat<1){
                    $type = 2;
                }else if($carat>=1 && $carat<1.5){
                    $type = 3;
                }else{
                    $type = 4;
                }
            }

        }else{
            //普通空拖、成品根据商品信息、款式归属判断；
            if($cert_id){
                $diainfo = $diamond_api->GetDiamondByCert_id($cert_id);
                if($diainfo['error'] != 1){
                    $cert = $diainfo['return_msg']['cert'];                   
                }
            }
            if($cert == 'HRD-D'){
                $type = 10;
            }
            $style_api = data_gateway('istyle');
            //款号判断是否天生一对系列产品
            if($style_sn != '' && $type != 10){
                $styleinfo = $style_api->get_style_info(array('style_sn'=>$style_sn));
                if($styleinfo['error'] != 1){
                    $xilie = $styleinfo['return_msg']['xilie'];
                    if($xilie){
                        $xiliearr = array_filter(explode(',', $xilie));
                        if(!empty($xiliearr)){
                            if(in_array('8', $xiliearr)){
                                $type = 10;
                            }else if(in_array('24', $xiliearr)){
                                $type = 12;
                            }
                        }
                    }
                }
            }
            //普通空拖、成品
            if(!in_array($type,array(10,12))){
                $type = 11;
            }
        }
        return $type;
    }
    */
}
