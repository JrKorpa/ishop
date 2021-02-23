<?php
/**
 * 卖家退款
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class store_refundControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        $model_refund = Model('refund_return');
        $model_refund->getRefundStateArray();
        Language::read('member_store_index');
    }
    /**
     * 退款记录列表页
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
        $_GET['lock'] = 1;
        //$condition['order_lock'] = $order_lock;
        $condition['order_lock'] = array('in', array(1,2));

        $return_check = $this->check_seller_limit('limit_return_check');
        Tpl::output('return_check', $return_check);
        $refund_list = $model_refund->getRefundList($condition,10);
        Tpl::output('refund_list',$refund_list);
        Tpl::output('show_page',$model_refund->showpage());
        //self::profile_menu('refund',$order_lock);
        self::profile_menu('refund',1);
        Tpl::showpage('store_refund');
    }
    /**
     * 退款审核页
     *
     */
    public function editOp() {
        $model_refund = Model('refund_return');
        $model_order = Model('order');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_id'] = intval($_GET['refund_id']);
        $refund_list = $model_refund->getRefundList($condition);
        $refund = $refund_list[0];
        if (chksubmit()) {
            $reload = 'index.php?act=store_refund&lock=1';
            if ($refund['order_lock'] == '2') {
                $reload = 'index.php?act=store_refund&lock=2';
            }
            if ($refund['seller_state'] != '1') {//检查状态,防止页面刷新不及时造成数据错误
                showDialog(Language::get('wrong_argument'),$reload,'error');
            }
            try {
                $model_order->beginTransaction();
                $order_id = $refund['order_id'];
                $refund_amount = $refund['refund_amount'];
                $refund_array = array();
                $refund_array['seller_time'] = time();
                $refund_array['seller_state'] = $_POST['seller_state'];//卖家处理状态:1为待审核,2为同意,3为不同意
                $refund_array['seller_message'] = $_POST['seller_message'];
                if ($refund_array['seller_state'] == '3') {
                    $refund_array['refund_state'] = '3';//状态:1为处理中,2为待管理员处理,3为已完成
                } else {
                    $refund_array['seller_state'] = '2';
                    $refund_array['refund_state'] = '2';
                }
                $state = $model_refund->editRefundReturn($condition, $refund_array);
                if($state){
                    //$refund = $model_refund->getRefundReturnInfo($condition);
                    $order = $model_order->getOrderInfo(array('order_id'=> $order_id),array());
                    if ($order['payment_time'] > 0) {
                        $order['pay_amount'] = $order['order_amount']-$order['rcb_amount']-$order['pd_amount'];//在线支付金额=订单总价格-充值卡支付金额-预存款支付金额
                    }

                    $condition = array();
                    $condition['refund_id'] = intval($_GET['refund_id']);
                    $detail_array = $model_refund->getDetailInfo($condition);
                    if(empty($detail_array)) {
                        $model_refund->addDetail($refund,$order);
                        $detail_array = $model_refund->getDetailInfo($condition);
                    }
                    if ($detail_array['pay_time'] > 0) {
                        $refund['pay_amount'] = $detail_array['pay_amount'];//已完成在线退款金额
                    }
                    //$state = $model_refund->editOrderRefund($refund,$this->admin_info['name']);
                    if($refund_array['seller_state'] == 2)
                    {
                        //更新订单商品信息
                        $updata = array();
                        if ($refund['refund_type'] == 2) {
                            $updata['is_return'] = 1;
                        } else {
                            $updata['is_return'] = $refund['return_type'] == 2 ? 1 : 0;
                        }
                        $is_return = $updata['is_return'];
                        $updata['refund_amount'] = $refund_amount;
                        $condition = array();
                        if(!empty($refund['order_goods_id'])){
                            $condition['rec_id'] = $refund['order_goods_id'];
                            //订单明细违约金累计
                            $order_goods = Model('order')->getOrderGoodsInfo(array('rec_id'=>$refund['order_goods_id']));
                            $updata['breach_amount'] = bcadd($order_goods['breach_amount'], $refund['breach_amount'], 2);
                        }else{
                            $condition['order_id'] = $order_id;//全部退款
                        }
                        
                        $res = $model_order->editOrderGoods($updata, $condition); 
                        if(!$res){
                            throw new Exception('更新订单商品信息失败');
                        }
                        $updata = array();

                        $goods_list = $model_order->getOrderGoodsInfo(array('order_id'=>$order_id, 'is_return' => 0));
                        if(!empty($goods_list)){
                            $refund_state = 1;
                        }else{
                            //$updata['order_state'] = 0;
                            $refund_state = 2;
                        }
                        
                        //更新订单信息
                        $updata['refund_amount'] = bcadd($order['refund_amount'],$refund_amount, 2);
                        if($updata['refund_amount'] > $order['rcb_amount']){
                            throw new Exception('退款总金额不能大于支付金额');
                        }
                        //查询订单商品是否全部退货 退款总金额=订单金额
                        //if($updata['refund_amount'] == $order['rcb_amount']){
                        //    $refund_state = 2;
                        //}else{
                        //    $refund_state = 1;
                        //}
                        $updata['refund_state'] = $refund_state;
                        $goodslist = $model_order->getOrderGoodsInfo(array('rec_id'=>$refund['order_goods_id']));
                        if($is_return == 1){
                            $updata['order_amount'] = bcsub($order['order_amount'], $goodslist['goods_pay_price'], 2);
                        }

                        //订单主表违约金累计
                        $updata['breach_amount'] = bcadd($order['breach_amount'] ,$refund['breach_amount'],2);

                        //判断订单是否支付全款
                        $order_amount = isset($updata['order_amount'])?$updata['order_amount']:$order['order_amount'];
                        $rcb_amount = bcsub($order['rcb_amount'], $updata['refund_amount'], 2);
                        if($order_amount <= $rcb_amount){
                            //1\已付全款 支付状态 已付款
                            $updata['pay_status'] = 3;
                        }
                        //$updata['order_amount'] = bcsub($order['goods_amount'], $updata['refund_amount'], 2);

                        //$updata['update_time'] = date("Y-m-d H:i:s");
                        $condition = array();
                        $condition['order_id'] = $order_id;
                        $res = $model_order->editOrder($updata, $condition);
                        if(!$res){
                            throw new Exception('更新订单状态失败');
                        }
                    }
                    $this->recordSellerLog('退款处理，退款编号：'.$refund['refund_sn']);

                    // 发送买家消息
                    //$param = array();
                    //$param['code'] = 'refund_return_notice';
                    //$param['member_id'] = $refund['buyer_id'];
                    //$param['param'] = array(
                    //    'refund_url'=> urlShop('member_refund', 'view', array('refund_id' => $refund['refund_id'])),
                    //    'refund_sn' => $refund['refund_sn']
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
                    $data['log_msg'] = $_log." 退款/退货申请单：".$refund['refund_sn'];
                    $data['log_user'] = $order_info['seller_name'];
                    $data['log_orderstate'] = $order_info['order_state'];
                    $res = $model_order->addOrderLog($data);
                    if(!$res){
                        throw new Exception('添加订单日志失败');
                    }
                    $model_order->commit();

                    //同步到后端 
                    $latest_order_info = $model_order->getOrderInfo(array('order_id'=>$order_id),array("order_common","order_goods","store","order_pay_action"));
                    EventEmitter::dispatch("erp", array('event' => 'sync_order', 'data' => $latest_order_info));

                    showDialog(Language::get('nc_common_save_succ'),$reload,'succ');
                }else{
                    throw new Exception('更新退款单状态失败');
                }
            } catch (Exception $e) {
                $model_order->rollback();
                showDialog("提交失败！".$e->getMessage(),$reload,'error');
            }
        }
        Tpl::output('refund',$refund);
        $info['buyer'] = array();
        if(!empty($refund['pic_info'])) {
            $info = unserialize($refund['pic_info']);
        }
        $breach_amount = $refund['breach_amount'];
        Tpl::output('breach_amount',$breach_amount);
        Tpl::output('pic_list',$info['buyer']);
        $model_member = Model('member');
        $member = $model_member->getMemberInfoByID($refund['buyer_id']);
        Tpl::output('member',$member);
        $condition = array();
        $condition['order_id'] = $refund['order_id'];
        $model_refund->getRightOrderList($condition, $refund['order_goods_id']);
        Tpl::showpage('store_refund_edit');
    }
    /**
     * 退款记录查看页
     *
     */
    public function viewOp() {
        $model_refund = Model('refund_return');
        $condition = array();
        $condition['store_id'] = $_SESSION['store_id'];
        $condition['refund_id'] = intval($_GET['refund_id']);
        $refund_list = $model_refund->getRefundList($condition);
        $refund = $refund_list[0];
        Tpl::output('refund',$refund);
        $info['buyer'] = array();
        if(!empty($refund['pic_info'])) {
            $info = unserialize($refund['pic_info']);
        }
        $breach_amount = $refund['breach_amount'];
        Tpl::output('breach_amount',$breach_amount);
        Tpl::output('pic_list',$info['buyer']);
        $model_member = Model('member');
        $member = $model_member->getMemberInfoByID($refund['buyer_id']);
        Tpl::output('member',$member);
        $condition = array();
        $condition['order_id'] = $refund['order_id'];
        $model_refund->getRightOrderList($condition, $refund['order_goods_id']);
        Tpl::showpage('store_refund_view');
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
            case 'refund':
                $menu_array = array(
                    //array('menu_key'=>'2','menu_name'=>'售前退款',  'menu_url'=>'index.php?act=store_refund&lock=2'),
                    //array('menu_key'=>'1','menu_name'=>'售后退款','menu_url'=>'index.php?act=store_refund&lock=1')
                    array('menu_key'=>'1','menu_name'=>'退款列表','menu_url'=>'index.php?act=store_refund')
                );
                break;
        }
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
