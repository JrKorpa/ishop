<?php
/**
 * 实物订单行为
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class orderLogic {

    /**
     * 取消订单
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param string $msg 操作备注
     * @param boolean $if_update_account 是否变更账户金额
     * @param array $cancel_condition 订单更新条件,目前只传入订单状态，防止并发下状态已经改变
     * @return array
     */
    public function changeOrderStateCancel($order_info, $role, $user = '', $msg = '', $if_update_account = true, $cancel_condition = array()) {
        try {
            $model_order = Model('order');
            $model_order->beginTransaction();
            $order_id = $order_info['order_id'];

            //库存销量变更
            $goods_list = $model_order->getOrderGoodsList(array('order_id'=>$order_id));
            $data = array();
            foreach ($goods_list as $goods) {
                $data[$goods['goods_id']] = $goods['goods_num'];
            }
            $result = Logic('queue')->cancelOrderUpdateStorage($data);
            if (!$result['state']) {
                throw new Exception('解除绑定失败');
            }
            //if ($order_info['chain_id']) {
            //    $result = Logic(queue)->cancelOrderUpdateChainStorage($data,$order_info['chain_id']);
            //    if (!$result['state']) {
            //        throw new Exception('还原门店库存失败');
            //    }
            //}

            //if ($if_update_account) {
            //    $model_pd = Model('predeposit');
            //    //解冻充值卡
            //    $rcb_amount = floatval($order_info['rcb_amount']);
            //    if ($rcb_amount > 0) {
            //        $data_pd = array();
            //        $data_pd['member_id'] = $order_info['buyer_id'];
            //        $data_pd['member_name'] = $order_info['buyer_name'];
            //        $data_pd['amount'] = $rcb_amount;
            //        $data_pd['order_sn'] = $order_info['order_sn'];
            //        $model_pd->changeRcb('order_cancel',$data_pd);
            //    }
            //
            //    //解冻预存款
            //    $pd_amount = floatval($order_info['pd_amount']);
            //    if ($pd_amount > 0) {
            //        $data_pd = array();
            //        $data_pd['member_id'] = $order_info['buyer_id'];
            //        $data_pd['member_name'] = $order_info['buyer_name'];
            //        $data_pd['amount'] = $pd_amount;
            //        $data_pd['order_sn'] = $order_info['order_sn'];
            //        $model_pd->changePd('order_cancel',$data_pd);
            //    }
            //}

            //更新订单信息
            $update_order = array('order_state'=>ORDER_STATE_CANCEL);
            $cancel_condition['order_id'] = $order_id;
            $update = $model_order->editOrder($update_order,$cancel_condition);
            if (!$update) {
                throw new Exception('保存失败');
            }

            //添加订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_msg'] = '取消了订单';
            $data['log_user'] = $user;
            if ($msg) {
                $data['log_msg'] .= ' ( '.$msg.' )';
            }
            $data['log_orderstate'] = ORDER_STATE_CANCEL;
            $res = $model_order->addOrderLog($data);
            if (!$res) {
                throw new Exception('保存订单日志失败');
            }
            $model_order->commit();

            Model('voucher')->returnVoucher($order_info['order_id']);
            
            //同步到后端 
            $latest_order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
            EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $latest_order_info));
            
            return callback(true,'操作成功');

        } catch (Exception $e) {
            $model_order->rollback();
            return callback(false,'操作失败');
        }
    }

    /**
     * 收货
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system,chain 分别代表买家、商家、管理员、系统、门店
     * @param string $user 操作人
     * @param string $msg 操作备注
     * @return array
     */
    public function changeOrderStateReceive($order_info, $role, $user = '', $msg = '') {
        try {

            $order_id = $order_info['order_id'];
            $model_order = Model('order');

            //更新订单状态
            $update_order = array();
            $update_order['finnshed_time'] = TIMESTAMP;
            $update_order['order_state'] = ORDER_STATE_SUCCESS;
            $update = $model_order->editOrder($update_order,array('order_id'=>$order_id));
            if (!$update) {
                throw new Exception('保存失败');
            }

            //添加订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_msg'] = $msg;
            $data['log_user'] = $user;
            $data['log_orderstate'] = ORDER_STATE_SUCCESS;
            $model_order->addOrderLog($data);

            if ($order_info['buyer_id'] > 0 && $order_info['order_amount'] > 0) {
                //添加会员积分
                if (C('points_isuse') == 1){
                    Model('points')->savePointsLog('order',array('pl_memberid'=>$order_info['buyer_id'],'pl_membername'=>$order_info['buyer_name'],'orderprice'=>$order_info['order_amount'],'order_sn'=>$order_info['order_sn'],'order_id'=>$order_info['order_id']),true);
                }
                //添加会员经验值
                Model('exppoints')->saveExppointsLog('order',array('exp_memberid'=>$order_info['buyer_id'],'exp_membername'=>$order_info['buyer_name'],'orderprice'=>$order_info['order_amount'],'order_sn'=>$order_info['order_sn'],'order_id'=>$order_info['order_id']),true);
		//邀请人获得返利积分 by 33ha o .com
			$model_member = Model('member');
			$inviter_id = $model_member->table('member')->getfby_member_id($member_id,'inviter_id');
			$inviter_name = $model_member->table('member')->getfby_member_id($inviter_id,'member_name');
			$rebate_amount = ceil(0.01 * $order_info['order_amount'] * $GLOBALS['setting_config']['points_rebate']);
			// 33  hao. com  v5
			$desc = '被邀请人['.$order_info['buyer_name'].']消费';
			Model('points')->savePointsLog('rebate',array('pl_memberid'=>$inviter_id,'pl_membername'=>$inviter_name,'rebate_amount'=>$rebate_amount,'pl_desc'=>$desc),true);

            }

            return callback(true,'操作成功');
        } catch (Exception $e) {
            return callback(false,'操作失败');
        }
    }

    /**
     * 更改运费
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param float $price 运费
     * @return array
     */
    public function changeOrderShipPrice($order_info, $role, $user = '', $price) {
        try {

            $order_id = $order_info['order_id'];
            $model_order = Model('order');

            $data = array();
            $data['shipping_fee'] = abs(floatval($price));
            $data['order_amount'] = array('exp','goods_amount+'.$data['shipping_fee']);
            $update = $model_order->editOrder($data,array('order_id'=>$order_id));
            if (!$update) {
                throw new Exception('保存失败');
            }
            //记录订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_user'] = $user;
            $data['log_msg'] = '修改了运费'.'( '.$price.' )';;
            $data['log_orderstate'] = $order_info['payment_code'] == 'offline' ? ORDER_STATE_PAY : ORDER_STATE_NEW;
            $model_order->addOrderLog($data);
            return callback(true,'操作成功');
        } catch (Exception $e) {
            return callback(false,'操作失败');
        }
    }
    /**
     * 更改价格
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param float $price 价格
     * @return array
     */
    public function changeOrderSpayPrice($order_info, $role, $user = '', $price) {
        try {

            $order_id = $order_info['order_id'];
            $model_order = Model('order');

            $data = array();
            $data['goods_amount'] = abs(floatval($price));
            $data['order_amount'] = array('exp','IFNULL(shipping_fee,0)+'.$data['goods_amount']);
            $update = $model_order->editOrder($data,array('order_id'=>$order_id));
            if (!$update) {
                throw new Exception('保存失败');
            }
            //记录订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_user'] = $user;
            $data['log_msg'] = '修改了价格'.'( '.$price.'元 )';;
            $data['log_orderstate'] = $order_info['payment_code'] == 'offline' ? ORDER_STATE_PAY : ORDER_STATE_NEW;
            $model_order->addOrderLog($data);
            return callback(true,'操作成功');
        } catch (Exception $e) {
            return callback(false,'操作失败');
        }
    }
    /**
     * 回收站操作（放入回收站、还原、永久删除）
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $state_type 操作类型
     * @return array
     */
    public function changeOrderStateRecycle($order_info, $role, $state_type) {
        $order_id = $order_info['order_id'];
        $model_order = Model('order');
        //更新订单删除状态
        $state = str_replace(array('delete','drop','restore'), array(ORDER_DEL_STATE_DELETE,ORDER_DEL_STATE_DROP,ORDER_DEL_STATE_DEFAULT), $state_type);
        $update = $model_order->editOrder(array('delete_state'=>$state),array('order_id'=>$order_id));
        if (!$update) {
            return callback(false,'操作失败');
        } else {
            return callback(true,'操作成功');
        }
    }

    /**
     * 发货
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @return array
     */
    public function changeOrderSend($order_info, $role, $user = '', $post = array()) {
        $order_id = $order_info['order_id'];
        $model_order = new orderModel();
        try {
            $order_amount = $order_info['order_amount'];
            $model_order->beginTransaction();
            //检查货品库存
            //$order_goods = $order_info["extend_order_goods"];
            $order_goods = $model_order->buildCpdzGoodsList($order_info["extend_order_goods"]);
            $company_id = $order_info["store_id"];
            $goods_item_model = new goods_itemsModel();
            $has_finance_zp = 0;//是否有需要销账的赠品
            //warehoue_goods_list 库存商品列表，成品定制现货+裸石现货信息
            $warehouse_goods_list = array();            
            $cpdz_goods_list = array();
            foreach ($order_goods as $key=>$vo){
                $goods_id = $vo['goods_itemid'];//
                $style_sn = $vo["style_sn"];//款号
                $order_detail_id =  $vo["rec_id"]; //订单明细ID
                $is_cpdz = $vo['is_cpdz'];//是否成品定制 1是 0否
                $is_finance = $vo['is_finance'];//是否销账 1是 0否
                $is_zp = $vo['goods_type']==5?1:0;//是否赠品1是，0否             
                if($is_finance ==1){
                    if($is_zp == 1){
                        $has_finance_zp = 1;
                    }
                    if(empty($goods_id)) {
                        throw new Exception("亲，款{$style_sn}没有真实货号哦，你需要进行换货后才能销账发货！");
                    }
                    if(empty($post["goods_item_id"][$order_detail_id])) {
                        throw new Exception("亲，请填写发货货号，请核对款{$style_sn}！");
                    }else{
                        $post_goods_id = $post["goods_item_id"][$order_detail_id];
                    }
                    
                    if(!empty($goods_id) && $goods_id != $post_goods_id) {
                        throw new Exception("亲，您填写的发货货号与实际货号不相符，请核对款{$style_sn}！");
                    }
                    $goods_item_info = $goods_item_model->getGoodsItemInfo(array("goods_id"=>$post_goods_id,"company_id"=>$_SESSION["store_company_id"]));
                    if(empty($goods_item_info)) {
                        throw new Exception("亲，您输入的货号{$post_goods_id}不存在，请核对！");
                    }else if($goods_item_info["is_on_sale"]!=2) {
                        throw new Exception("亲，您输入的货号{$post_goods_id}不是库存状态，请核对！");
                    }else if($order_detail_id != $goods_item_info['order_detail_id']){
                        throw new Exception("亲，您输入的货号{$post_goods_id}绑定订单不对！");
                    }else{
                        $goods_item_info['sale_price'] = $vo['goods_pay_price']; 
                        $goods_item_info['jijiachengben_total'] = $goods_item_info['jijiachengben'];                        
                    }
                    
                    //成品定制裸石处理
                    if($is_cpdz == 1){                        
                        $cpdz_goods_list[$goods_id] = $goods_item_info;
                        $cpdz_goods_list[$goods_id]['jijiachengben_total'] = $goods_item_info['jijiachengben'];
                        //echo "chengpin:{$goods_item_info['jijiachengben']}\r\n";
                        $stone_goods_ids = $post["stone_goods_id"][$order_detail_id];
                        if(!empty($vo['stone_list']) && is_array($vo['stone_list'])){
                            foreach ($vo['stone_list'] as $s_k=>$stoneInfo){
                                if(is_array($stoneInfo)){
                                    if(empty($stone_goods_ids[$s_k])){
                                        throw new Exception("亲，请填写裸石销账货号! ");
                                    }else if($stone_goods_ids[$s_k]!=$stoneInfo['goods_id']){                                        
                                        throw new Exception("亲，裸石真实货号{$stoneInfo['goods_id']}与销账发货货号不一致，请核实! ");
                                    }
                                    $stoneInfo['order_detail_id'] = $order_detail_id;
                                    $cpdz_goods_list[$goods_id]['jijiachengben_total'] += $stoneInfo['jijiachengben'];
                                    $goods_item_info['jijiachengben_total']+= $stoneInfo['jijiachengben'];
                                    $cpdz_goods_list[$goods_id]['stone_list'][] = $stoneInfo;
                                    //$warehouse_goods_list[] = $stoneInfo;                                    
                                }else{
                                    //$error = $stoneInfo;
                                    throw new Exception($stoneInfo);
                                }
                            }
                        }else{
                            //$error ="亲，成品定制货号【{$goods_id}】品牌字段没有填写证书号，请联系入库人员核实! ";
                            //throw new Exception($error);
                        }
                    }
                    $warehouse_goods_list[] = $goods_item_info;
                }
            }
            if(!empty($warehouse_goods_list)){
                //销售价格拆分
                $_warehouse_goods_list = array();//临时变量
                $all_jijiachengben_total = array_sum(array_column($warehouse_goods_list,"jijiachengben_total"));                
                //$order_pay_price_total = array_sum(array_column($order_goods,"order_pay_price"));
                foreach ($warehouse_goods_list as $k=>$v){
                    $goods_id = $v['goods_id'];
                    $jijiachengben_total = $v['jijiachengben_total'];
                    //如果包含销账的赠品
                    if($has_finance_zp ==1){
                        $sale_price = round($jijiachengben_total/$all_jijiachengben_total*$order_amount,2);
                    }else{
                        $sale_price = $v['sale_price'];//等同 order_goods.order_pay_price
                    }                    
                    //成品定制价格均分
                    if(!empty($cpdz_goods_list[$goods_id])){ 
                        $v['sale_price'] = round($v['jijiachengben']/$jijiachengben_total*$sale_price,2);
                        //echo "chengpin:{$v['goods_id']}:{$v['sale_price']}={$v['jijiachengben']}/{$jijiachengben_total}*{$sale_price}\r\n";
                        $_warehouse_goods_list[] = $v;
                        
                        if(!empty($cpdz_goods_list[$goods_id]['stone_list'])){                            
                            $stone_list = $cpdz_goods_list[$goods_id]['stone_list'];
                            foreach ($stone_list as $k2=>$g){
                                $g['sale_price'] = $g['jijiachengben']/$jijiachengben_total * $sale_price;
                                $g['sale_price'] = round($g['sale_price'],2);   
                                //echo "stone:{$g['goods_id']}:{$g['sale_price']}={$g['jijiachengben']}/{$jijiachengben_total}*{$sale_price}\r\n";
                                $_warehouse_goods_list[] = $g;    
                            }
                        }
                    }else{
                        $v['sale_price'] = $sale_price;
                        //echo "kongtuo:{$v['sale_price']}\r\n";
                        $_warehouse_goods_list[] = $v;
                    }
                } 
          
                $warehouse_goods_list = $_warehouse_goods_list;
                unset($_warehouse_goods_list);
                //创建S单
                $order_info['warehouse_goods_list'] = $warehouse_goods_list;
                $result = $this->_createBillInfoS($order_info);
                if($result["success"] !=1 ){
                    throw new Exception('创建S单失败！'.$result['msg']);
                }
            }
            $data = array();
            if (!empty($post['reciver_name'])) {
                $data['reciver_name'] = $post['reciver_name'];
            }
            if (!empty($post['reciver_info'])) {
                $data['reciver_info'] = $post['reciver_info'];
            }
            $data['deliver_explain'] = $post['deliver_explain'];
            $data['daddress_id'] = intval($post['daddress_id']);
            $data['shipping_express_id'] = intval($post['shipping_express_id']);
            $data['shipping_time'] = time();

            $condition = array();
            $condition['order_id'] = $order_id;
            $condition['store_id'] = $order_info['store_id'];
            $update = $model_order->editOrderCommon($data,$condition);
            if (!$update) {
                throw new Exception('操作失败');
            }

            $data = array();
            $data['shipping_code']  = $post['shipping_code'];
            $data['order_state'] = ORDER_STATE_SUCCESS;
            $data['delay_time'] = time();
            $update = $model_order->editOrder($data,$condition);
            if (!$update) {
                throw new Exception('操作失败');
            }
            //throw new Exception('success');
            $model_order->commit();

            //同步到后端 
            $latest_order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
            EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $latest_order_info));

        } catch (Exception $e) {
            $model_order->rollback();
            return callback(false,$e->getMessage());
        }

        //更新表发货信息
        if ($post['shipping_express_id'] && $order_info['extend_order_common']['reciver_info']['dlyp']) {
            $data = array();
            $data['shipping_code'] = $post['shipping_code'];
            $data['order_sn'] = $order_info['order_sn'];
            $express_info = Model('express')->getExpressInfo(intval($post['shipping_express_id']));
            $data['express_code'] = $express_info['e_code'];
            $data['express_name'] = $express_info['e_name'];
            Model('delivery_order')->editDeliveryOrder($data,array('order_id' => $order_info['order_id']));
        }

        //添加订单日志
        $data = array();
        $data['order_id'] = $order_id;
        $data['log_role'] = 'seller';
        $data['log_user'] = $user;
        $data['log_msg'] = '发货成功';
        $data['log_orderstate'] = ORDER_STATE_SEND;
        $model_order->addOrderLog($data);

        // 发送买家消息
        $param = array();
        $param['code'] = 'order_deliver_success';
        $param['member_id'] = $order_info['buyer_id'];
        $param['param'] = array(
            'order_sn' => $order_info['order_sn'],
            'order_url' => urlShop('member_order', 'show_order', array('order_id' => $order_id))
        );
        QueueClient::push('sendMemberMsg', $param);

        return callback(true,'操作成功');
    }
    /**
     * 生成销售出库单
     * @param unknown $order_info
     * @throws Exception
     * @return multitype:number string multitype: NULL multitype:unknown string
     */
    private function _createBillInfoS($order_info){
        $erp_bill_model = new erp_billModel();//Model('erp_bill');
        $order_model = new orderModel();
        $order_id = $order_info['order_id'];
        $order_sn = $order_info['order_sn'];
        if(!empty($order_info['warehouse_goods_list'])){
             $warehouse_goods_list = $order_info['warehouse_goods_list'];
        }else{
            throw new Exception('商品明细为空！');
        }
        $pifajia_total = 0;
        $guanlifei_total = 0;
        $from_company_id = $_SESSION["store_company_id"];
        foreach ($warehouse_goods_list as $key => $warehouse_goods) {
            $goods_id = $warehouse_goods['goods_id'];      
            
            $bill_goods = array();
            $bill_goods['goods_itemid'] = $warehouse_goods['goods_id'];
            $bill_goods['goods_sn'] = $warehouse_goods['goods_sn'];
            $bill_goods['goods_name'] = $warehouse_goods['goods_name'];
            $bill_goods['goods_count'] = 1;
            $bill_goods['from_company_id'] = $from_company_id;
            $bill_goods['to_company_id'] = "";
            $bill_goods['to_house_id'] = "";
            $bill_goods['in_warehouse_type'] = $warehouse_goods['put_in_type'];
            $bill_goods['yuanshichengben'] = $warehouse_goods['yuanshichengbenjia'];
            $bill_goods['mingyichengben'] = $warehouse_goods['mingyichengben'];
            $bill_goods['jijiachengben'] = $warehouse_goods['jijiachengben'];        
            $bill_goods['sale_price'] = $warehouse_goods['sale_price'];
            $bill_goods['management_fee'] = 0;
            $bill_goods['order_detail_id'] = $warehouse_goods['order_detail_id'];
            $bill_goods_list[] = $bill_goods;
            $pifajia_total = bcadd($pifajia_total, $bill_goods['sale_price'], 2);
            $guanlifei_total = bcadd($guanlifei_total, $bill_goods['management_fee'], 2);
        }
        $bill_type = "S";
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'item_type'=>"LS",
            'bill_status'=>2,
            'from_company_id'=>$from_company_id,
            'wholesale_id'=>"",
            'express_id'=>"",
            'pifa_type'=>"",
            'from_store_id'=>$order_info['store_id'],
            'create_user'=>$_SESSION["seller_name"],
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>"",
            'to_company_id'=>"",
            'to_house_id'=>"",
            'order_sn'=>$order_sn,
            'chengben_total'=>$pifajia_total,
            'goods_total'=>$pifajia_total,
            'check_user'=>$_SESSION["seller_name"],
            'check_time'=>date("Y-m-d H:i:s",TIMESTAMP),
        );
        $res = $erp_bill_model->createBill($bill_info,$bill_goods_list,$bill_type,false);        
        return $res;
    }
    //创建销售单
    /*public function createBillInfoS($order_info)
    {
        $erp_bill_model = Model('erp_bill');
        $goods_model = Model('erp_goods');
        $order_model = new orderModel();
        $order_id = $order_info['order_id'];
        $order_sn = $order_info['order_sn'];
        if(!empty($order_info['extend_order_goods'])){
             $order_goods = $order_info['extend_order_goods'];
        }else{
             $order_goods = $order_model->getOrderGoodsList(array('order_id'=>$order_id));
        }
        if(empty($order_goods)){
            throw new Exception('商品明细为空！');
        }

        $pifajia_total = 0;
        $guanlifei_total = 0;
        $from_company_id = $_SESSION["store_company_id"];
        foreach ($order_goods as $key => $vo) {
            $goods_id = $vo['goods_id'];
            $goods_item = $goods_model->getErpBillList(array('goods_id'=>$goods_id));
            
            
            $goods_list = isset($goods_list[0])?$goods_list[0]:array();
            $goods_info = array();
            $goods_info['goods_itemid'] = $goodslist['goods_itemid'];
            $goods_info['goods_sn'] = $goods_list['goods_sn'];
            $goods_info['goods_name'] = $goods_list['goods_name'];
            $goods_info['goods_count'] = 1;
            //$goods_info['remark'] = $goods_list['goods_id'];
            $goods_info['from_company_id'] = $from_company_id;
            //$goods_info['from_house_id'] = $goods_list['goods_id'];
            $goods_info['to_company_id'] = "";
            $goods_info['to_house_id'] = "";
            $goods_info['in_warehouse_type'] = $goods_list['put_in_type'];
            $goods_info['yuanshichengben'] = $goods_list['yuanshichengbenjia'];
            $goods_info['mingyichengben'] = $goods_list['mingyichengben'];
            $goods_info['jijiachengben'] = $goods_list['jijiachengben'];
            
            $goods_info['sale_price'] = $goodslist['goods_pay_price'];
            $goods_info['management_fee'] = 0;
            $bill_goods_list[] = $goods_info;
            $pifajia_total = bcadd($pifajia_total, $goods_info['sale_price'], 2);
            $guanlifei_total = bcadd($guanlifei_total, $goods_info['management_fee'], 2);
        }
        $bill_type = "S";
        $bill_info = array(
            'bill_no'=>uniqid(),
            'bill_type'=>$bill_type,
            'item_type'=>"LS",
            'bill_status'=>2,
            'from_company_id'=>$from_company_id,
            'wholesale_id'=>"",
            'express_id'=>"",
            'pifa_type'=>"",
            'from_store_id'=>$order_info['store_id'],
            'create_user'=>"system",
            'create_time'=>date("Y-m-d H:i:s",TIMESTAMP),
            'remark'=>"",
            'to_company_id'=>"",
            'to_house_id'=>"",
            'order_sn'=>$order_sn,
            'chengben_total'=>$pifajia_total,
            'goods_total'=>$pifajia_total
        );
        $res = $erp_bill_model->createBill($bill_info,$bill_goods_list,$bill_type);
        return $res;
    }*/

    /**
     * 收到货款
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @return array
     */
    public function changeOrderReceivePay($order_list, $role, $user = '', $post = array()) {
        $model_order = Model('order');

        try {
            $model_order->beginTransaction();
            $pay_info = $model_order->getOrderPayInfo(array('pay_sn'=>$order_list[0]['pay_sn']));
            if ($pay_info) {
                if ($pay_info['api_pay_state'] == 1) {
                    return callback(true,'操作成功');
                }
                $pay_info = $model_order->getOrderPayInfo(array('pay_id'=>$pay_info['pay_id']), true,true);
                if ($pay_info['api_pay_state'] == 1) {
                    return callback(true,'操作成功');
                }
            }
            $model_pd = Model('predeposit');
            foreach($order_list as $order_info) {
                $order_id = $order_info['order_id'];
                if (!in_array($order_info['order_state'],array(ORDER_STATE_NEW))) continue;
                //下单，支付被冻结的充值卡
                $rcb_amount = floatval($order_info['rcb_amount']);
                if ($rcb_amount > 0) {
                    $data_pd = array();
                    $data_pd['member_id'] = $order_info['buyer_id'];
                    $data_pd['member_name'] = $order_info['buyer_name'];
                    $data_pd['amount'] = $rcb_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $model_pd->changeRcb('order_comb_pay',$data_pd);
                }

                //下单，支付被冻结的预存款
                $pd_amount = floatval($order_info['pd_amount']);
                if ($pd_amount > 0) {
                    $data_pd = array();
                    $data_pd['member_id'] = $order_info['buyer_id'];
                    $data_pd['member_name'] = $order_info['buyer_name'];
                    $data_pd['amount'] = $pd_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $model_pd->changePd('order_comb_pay',$data_pd);
                }

                //更新订单相关扩展信息
                $result = $this->_changeOrderReceivePayExtend($order_info,$post);
                if (!$result['state']) {
                    throw new Exception($result['msg']);
                }

                //添加订单日志
                $data = array();
                $data['order_id'] = $order_id;
                $data['log_role'] = $role;
                $data['log_user'] = $user;
                $data['log_msg'] = '收到货款(外部交易号:'.$post['trade_no'].')';
                $data['log_orderstate'] = ORDER_STATE_PAY;
                $insert = $model_order->addOrderLog($data);
                if (!$insert) {
                    throw new Exception('操作失败');
                }

                //更新订单状态
                $update_order = array();
                $update_order['order_state'] = ORDER_STATE_PAY;
                $update_order['payment_time'] = ($post['payment_time'] ? strtotime($post['payment_time']) : TIMESTAMP);
                $update_order['payment_code'] = $post['payment_code'];
                if ($post['trade_no'] != '') {
                    $update_order['trade_no'] = $post['trade_no'];
                }
                $condition = array();
                $condition['order_id'] = $order_info['order_id'];
                $condition['order_state'] = ORDER_STATE_NEW;
                $update = $model_order->editOrder($update_order,$condition);
                if (!$update) {
                    throw new Exception('操作失败');
                }
            }

            //更新支付单状态
            $data = array();
            $data['api_pay_state'] = 1;
            $update = $model_order->editOrderPay($data,array('pay_sn'=>$order_info['pay_sn']));
            if (!$update) {
                throw new Exception('更新支付单状态失败');
            }

            $model_order->commit();
        } catch (Exception $e) {
            $model_order->rollback();
            return callback(false,$e->getMessage());
        }

        foreach($order_list as $order_info) {

            $order_id = $order_info['order_id'];
            //支付成功发送买家消息
            $param = array();
            $param['code'] = 'order_payment_success';
            $param['member_id'] = $order_info['buyer_id'];
            $param['param'] = array(
                    'order_sn' => $order_info['order_sn'],
                    'order_url' => urlShop('member_order', 'show_order', array('order_id' => $order_info['order_id']))
            );
            QueueClient::push('sendMemberMsg', $param);

            //非预定订单下单或预定订单全部付款完成
            if ($order_info['order_type'] != 2 || $order_info['if_send_store_msg_pay_success']) {
                //支付成功发送店铺消息
                $param = array();
                $param['code'] = 'new_order';
                $param['store_id'] = $order_info['store_id'];
                $param['param'] = array(
                        'order_sn' => $order_info['order_sn']
                );
                QueueClient::push('sendStoreMsg', $param);
                //门店自提发送提货码
                if ($order_info['order_type'] == 3) {
                    $_code = rand(100000,999999);
                    $result = $model_order->editOrder(array('chain_code'=>$_code),array('order_id'=>$order_info['order_id']));
                    if (!$result) {
                        throw new Exception('订单更新失败');
                    }
                    $param = array();
                    $param['chain_code'] = $_code;
                    $param['order_sn'] = $order_info['order_sn'];
                    $param['buyer_phone'] = $order_info['buyer_phone'];
                    QueueClient::push('sendChainCode', $param);
                }
            }
        }

        return callback(true,'操作成功');
    }

    /**
     * 更新订单相关扩展信息
     * @param unknown $order_info
     * @return unknown
     */
    private function _changeOrderReceivePayExtend($order_info, $post) {
        //预定订单收款
        if ($order_info['order_type'] == 2) {
            $result = Logic('order_book')->changeBookOrderReceivePay($order_info, $post);
        }
        return callback(true);
    }

    /**
     * 买家订单详细
     */
    public function getMemberOrderInfo($order_id,$member_id,$store_id) {
        $order_id = intval($order_id);
        $member_id = intval($member_id);
        $store_id = intval($store_id);
        if ($order_id <= 0) {
            return callback(false,'订单不存在');
        }

        $model_order = Model('order');
        $condition = array();
        $condition['order_id'] = $order_id;
        //$condition['buyer_id'] = $member_id;
        $order_info = $model_order->getOrderInfo($condition,array('order_goods','order_common','store','order_log','order_pay_action'));
        if (empty($order_info) || $order_info['delete_state'] == ORDER_DEL_STATE_DROP) {
            return callback(false,'订单不存在');
        }

        $model_refund_return = Model('refund_return');
        $order_list = array();
        $order_list[$order_id] = $order_info;
        $order_list = $model_refund_return->getGoodsRefundList($order_list,1);//订单商品的退款退货显示
        $order_info = $order_list[$order_id];
        
        //$refund_all = $order_info['refund_list'][0];
        //if (!empty($refund_all) && $refund_all['seller_state'] < 3) {//订单全部退款商家审核状态:1为待审核,2为同意,3为不同意
        //} else {
            //$refund_all = array();
        //}
        $return_goods_ing = array();
        if(isset($order_info['refund_list']) && !empty($order_info['refund_list'])){
            $returndata = $order_info['refund_list'];
            foreach ($returndata as $return_goods_id => $returnlist) {
                if($returnlist['refund_state'] == 1){
                    $return_goods_ing[] = $return_goods_id;
                }
            }
        }
        $order_pay_action = array();
        //支付记录
        if(isset($order_info['order_pay_action'][0]) && !empty($order_info['order_pay_action'][0])){
            $order_pay_action = $order_info['order_pay_action'][0];
        }


        //是否全部退款
        $is_return_all = $model_refund_return->getRefundReturnInfo(array('order_id'=>$order_id,'order_goods_id'=>0,'seller_state'=>array('in',array(1,2))));
        $order_info['if_return_all'] = isset($is_return_all['seller_state'])?$is_return_all['seller_state']:0;
        //显示锁定中
        $order_info['if_lock'] = $model_order->getOrderOperateState('lock',$order_info,$store_id);

        //显示审核按钮
        $order_info['if_check_order'] = $model_order->getOrderOperateState('check_order',$order_info,$store_id);

        //显示取消订单
        $order_info['if_buyer_cancel'] = $model_order->getOrderOperateState('buyer_cancel',$order_info,$store_id);

        //显示添加赠品&&删除赠品
        $order_info['if_add_zp'] = $model_order->getOrderOperateState('add_zp',$order_info,$store_id);

        //编辑货品
        $order_info['if_edit_goods'] = $model_order->getOrderOperateState('edit_goods',$order_info,$store_id);

        //显示编辑地址
        $order_info['if_edit_addr'] = $model_order->getOrderOperateState('edit_addr',$order_info,$store_id);

        //显示退款取消订单
        $order_info['if_refund_cancel'] = $model_order->getOrderOperateState('refund_cancel',$order_info,$store_id);

        //显示投诉
        $order_info['if_complain'] = $model_order->getOrderOperateState('complain',$order_info,$store_id);

        //显示收货
        $order_info['if_receive'] = $model_order->getOrderOperateState('receive',$order_info,$store_id);

        //显示物流跟踪
        $order_info['if_deliver'] = $model_order->getOrderOperateState('deliver',$order_info,$store_id);

        //显示评价
        $order_info['if_evaluation'] = $model_order->getOrderOperateState('evaluation',$order_info,$store_id);

        //显示分享
        $order_info['if_share'] = $model_order->getOrderOperateState('share',$order_info,$store_id);

        //退款
        $order_info['if_return_price'] = $model_order->getOrderOperateState('return_price',$order_info,$store_id);

        //退货
        $order_info['if_return_goods'] = $model_order->getOrderOperateState('return_goods',$order_info,$store_id);

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

        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $order_info['close_info'] = $model_order->getOrderLogInfo(array('order_id'=>$order_info['order_id']),'log_id desc');
        }
        //查询消费者保障服务
        if (C('contract_allow') == 1) {
            $contract_item = Model('contract')->getContractItemByCache();
        }
        foreach ($order_info['extend_order_goods'] as $value) {
            $value['image_60_url'] = cthumb($value['goods_image'], 60, $value['store_id']);
            $value['image_240_url'] = cthumb($value['goods_image'], 240, $value['store_id']);
            $value['goods_type_cn'] = orderGoodsType($value['goods_type']);
            $value['goods_url'] = urlShop('goods','index',array('goods_id'=>$value['goods_id']));
            $value['refund'] = $value['refund'] ? 1 : 0;
            //处理消费者保障服务
            if (trim($value['goods_contractid']) && $contract_item) {
                $goods_contractid_arr = explode(',',$value['goods_contractid']);
                foreach ((array)$goods_contractid_arr as $gcti_v) {
                    $value['contractlist'][] = $contract_item[$gcti_v];
                }
            }
            if ($value['goods_type'] == 5) {
                $order_info['zengpin_list'][] = $value;
            } else {
                $order_info['goods_list'][] = $value;
            }
        }

        if (empty($order_info['zengpin_list'])) {
            $order_info['zengpin_list'] = array();
            $order_info['goods_count'] = count($order_info['goods_list']);
        } else {
            $order_info['goods_count'] = count($order_info['goods_list']) + 1;
        }

        //取得其它订单类型的信息
        $model_order->getOrderExtendInfo($order_info);

        //卖家发货信息
        if (!empty($order_info['extend_order_common']['daddress_id'])) {
            $daddress_info = Model('daddress')->getAddressInfo(array('address_id'=>$order_info['extend_order_common']['daddress_id']));
        } else {
            $daddress_info = array();
        }
        return callback(true,'',array('order_info'=>$order_info,'refund_all'=>$refund_all,'daddress_info'=>$daddress_info,'return_goods_ing'=>$return_goods_ing,'order_pay_action'=>$order_pay_action));
    }

    /**
     * 订单审核
     * @param unknown $order_info
     * @param unknown $to_state
     * @param unknown $user
     */
    public function changeOrderStateConfirm($order_info, $user_id, $user_name) {
        $model_order = Model('order');
        $order_id = $order_info['order_id'];
        try{
            
            $others = $model_order->preCheckOrder($order_id);
            if (!empty($others)) {
                return callback(false, '对不起，商品货号 '.$others[0]['goods_itemid'].' 已被订单 '.$others[0]['order_sn'].' 抢先下单确认');
            }
            
            $model_order->beginTransaction();
            //记录订单日志
            $data = array();
            $data['log_msg']='订单审核通过';
            $data['order_id'] = $order_id;
            $data['log_role'] = 'seller';
            $data['log_user'] = $user_name;
            $data['log_orderstate'] = ORDER_STATE_NEW;
            
            //更新订单状态和审核信息
            $array= array();
            $array["audit_by"]=$user_name;
            $array["audit_time"]=date('Y-m-d H:i:s');
            $array["order_state"]=ORDER_STATE_NEW;

            $order_amount = $order_info['order_amount'];//订单宗金额
            $rcb_amount = $order_info['rcb_amount'];//已付款金额
            $refund_amount = $order_info['refund_amount'];//退款金额
            $breach_amount = $order_info['breach_amount'];//违约金

            //如果是赠品，价格为0,审核时，支付时间=审核时间
            if($order_info['is_zp'] == 1 && $order_amount ==0){
                $array["payment_time"]=strtotime(date('Y-m-d'));
            }

            if(($order_amount - $rcb_amount + $refund_amount +  $breach_amount) <= 0){
                $array['pay_status'] = ORDER_PAY_FULL;  //付款状态：已付款
            }elseif($rcb_amount > 0){
                $array['pay_status'] = ORDER_PAY_PART;  //付款状态：已付款
            }else{
                $array['pay_status'] = ORDER_PAY_TODO;  //付款状态：已付款
            }

            $model_order->editOrder($array,["order_id"=>$order_id]);
            $model_order->addOrderLog($data);
            $model_order->commit();

            //更新订单状态
            $this->auto_order_wf('', $order_id);


            //同步到后端 
            $latest_order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
            EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $latest_order_info));
            
            return callback(true,'操作成功');
        } catch (Exception $ex){
            $model_order->rollback();
            return callback(false, $ex->getMessage());
        }
    }
    
    public function auto_order_wf($order_sn, $order_id) {
        $order_model = Model('order');
        // 当前订单信息
        if (!empty($order_id)) {
            $order_info = $order_model->getOrderInfo(['order_id' => $order_id],[]);
            $order_sn = $order_info['order_sn'];
        } else if (!empty($order_sn)) {
            $order_info = $order_model->getOrderInfo(['order_sn' => $order_sn],[]);
            $order_id = $order_info['order_id'];
        }
        
        if (in_array($order_info['order_state'], [ORDER_STATE_CANCEL, ORDER_STATE_TO_CONFIRM, ORDER_STATE_SEND, ORDER_STATE_SUCCESS, ORDER_STATE_RETURN])) {
            return true;
        }
        
        $order_goods = $order_model->getOrderGoodsFillStats($order_id);
        try {
            
            $order_next_state = $order_info['order_state'];  
            $order_model->beginTransaction();
        
            if (empty($order_goods)) {
                if ($order_info['order_state'] == ORDER_STATE_MAKING) $order_model->editOrderSync(['order_id' => $order_id], ['pull_stop' => 1]);
               
                // 订单没有全退款退货
                if ($order_info['refund_state'] < 2) {
                    if ($order_info['order_amount'] <= 0) {
                        $order_next_state = ORDER_STATE_TOSEND;
                    } else if ($order_info['pay_status'] <= ORDER_PAY_PART) {
                        // 待支付
                        $order_next_state = ORDER_STATE_NEW;
                    } else if ($order_info['pay_status'] == ORDER_PAY_FULL) {
                        // 待发货
                        $order_next_state = ORDER_STATE_TOSEND;
                    }
                } else {
                    //$order_next_state = ORDER_STATE_RETURN;
                }
                
            } else {
                
                $all_signed = true;
                $all_to_sign = true;
                $goods_making = true;
                $is_xianhuo = true;
                
                foreach ($order_goods as $item) {
                    $state = $item['is_on_sale'];
                        
                    if (!in_array($state,[1,2])) $all_to_sign = false;
                    if ($state != 2) $all_signed = false;
                    if ($item['is_xianhuo'] == 0 && empty($item['bc_id'])) $goods_making = false;
                    if ($item['is_xianhuo'] == 0) $is_xianhuo = false;
                }
                              
                if ($all_signed || $is_xianhuo) {
                    if ($order_info['pay_status'] <= ORDER_PAY_PART) {
                        // 待支付
                        $order_next_state = ORDER_STATE_NEW;
                    } else if ($order_info['pay_status'] == ORDER_PAY_FULL) {
                        // 待发货
                        $order_next_state = ORDER_STATE_TOSEND;
                    }
                } else if ($all_to_sign) {
                    $order_next_state = ORDER_STATE_TO_SIGN;                 
                } else if ($order_info['is_xianhuo'] == 0 && $order_info['pay_status'] > ORDER_PAY_TODO && !$goods_making && $order_info['order_state'] < ORDER_STATE_MAKING) {
                    $order_next_state = ORDER_STATE_TO_BC;
                }                
            }

            if ($order_info['order_state'] != $order_next_state) {
                $order_model->editOrder(['order_state' => $order_next_state], ['order_id' => $order_id]);
                $log_msg = '';
                switch ($order_next_state){
                    case ORDER_STATE_TOSEND:
                        $log_msg = '订单符合发货条件，状态调整为：待发货'; break;
                        
                    case ORDER_STATE_NEW:
                        $log_msg = '订单商品已入库，订单状态调整为：待付尾款'; break;
                        
                    case ORDER_STATE_TO_SIGN:
                        $log_msg = '订单商品已生产出厂，待入库签收'; break;
                        
                    case ORDER_STATE_MAKING:
                        $order_model->editOrderSync(['order_id' => $order_id], ['sync_stop' => 1, 'pull_stop' => 0]);
                        break;
                        
                    case ORDER_STATE_RETURN:
                        $log_msg = '订单商品已退款/退货'; break;
                }
                
                if (!empty($log_msg)) {
                    // 添加订单日志
                    $order_model->addOrderLog(['order_id' => $order_id, 'log_msg' => $log_msg, 'log_role' => 'system', 'log_user' => 'system', 'log_orderstate' => $order_next_state ]);
                }
            } 
            
            $order_model->commit();            
            return true;
        } catch (Exception $ex) {
            $order_model->rollback();
            return $ex;
        }
    }
}