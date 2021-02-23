<?php
/**
 * 购买
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class member_buyControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 购物车、直接购买第一步:选择收获地址和配置方式
     */
    public function buy_step1Op() {
        $cart_id = array_filter(explode(',', $_POST['cart_id']));

        $logic_buy = logic('buy');

        //得到会员等级
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);

        if ($member_info){
            $member_gradeinfo = $model_member->getOneMemberGrade(intval($member_info['member_exppoints']));
            $member_discount = $member_gradeinfo['orderdiscount'];
            $member_level = $member_gradeinfo['level'];
        } else {
            $member_discount = $member_level = 0;
        }
        
        //得到购买数据
        $result = $logic_buy->buyStep1($cart_id, $_POST['ifcart'], $this->member_info['member_id'], $this->member_info['store_id'],$this->store_info['store_company_id'],null,$member_discount,$member_level);
        if(!$result['state']) {
            output_error($result['msg']);
        } else {
            $result = $result['data'];
        }
        
        //if (intval($_POST['address_id']) > 0) {
            //$result['address_info'] = Model('address')->getDefaultAddressInfo(array('address_id'=>intval($_POST['address_id']),'member_id'=>$this->member_info['member_id']));
        //}
        if (!empty($_POST['address_info']['city_id'])) {
            $data_area = $logic_buy->changeAddr($result['freight_list'], $_POST['address_info']['city_id'], $_POST['address_info']['area_id'], $this->member_info['member_id']);
            if(!empty($data_area) && $data_area['state'] == 'success' ) {
                if (is_array($data_area['content'])) {
                    foreach ($data_area['content'] as $store_id => $value) {
                        $data_area['content'][$store_id] = ncPriceFormat($value);
                    }
                }
            } else {
                //output_error('地区请求失败');
            }
        }else{
            $_POST['address_info'] = array();
        }

        //整理数据
        $store_cart_list = array();
        $store_total_list = $result['store_goods_total_1'];
        foreach ($result['store_cart_list'] as $key => $value) {
            $value = formatGoodsListKezi($value);
            $store_cart_list[$key]['goods_list'] = $value;            
            $store_cart_list[$key]['store_goods_total'] = $result['store_goods_total'][$key];

            $store_cart_list[$key]['store_mansong_rule_list'] = $result['store_mansong_rule_list'][$key];

            if (is_array($result['store_voucher_list'][$key]) && count($result['store_voucher_list'][$key]) > 0) {
                reset($result['store_voucher_list'][$key]);
                $store_cart_list[$key]['store_voucher_info'] = current($result['store_voucher_list'][$key]);
                $store_cart_list[$key]['store_voucher_info']['voucher_price'] = ncPriceFormat($store_cart_list[$key]['store_voucher_info']['voucher_price']);
                $store_total_list[$key] -= $store_cart_list[$key]['store_voucher_info']['voucher_price'];
            } else {
                $store_cart_list[$key]['store_voucher_info'] = array();
            }

            $store_cart_list[$key]['store_voucher_list'] = $result['store_voucher_list'][$key];
            if(!empty($result['cancel_calc_sid_list'][$key])) {
                $store_cart_list[$key]['freight'] = '0';
                $store_cart_list[$key]['freight_message'] = $result['cancel_calc_sid_list'][$key]['desc'];
            } else {
                $store_cart_list[$key]['freight'] = '1';
            }
            $store_cart_list[$key]['store_name'] = $value[0]['store_name'];
        }

        $buy_list = array();
        $buy_list['error'] = false;
        $buy_list['store_cart_list'] = $store_cart_list;
        $buy_list['freight_hash'] = $result['freight_list'];
        $buy_list['address_info'] = $result['address_info'];
        $buy_list['ifshow_offpay'] = $result['ifshow_offpay'];
        $buy_list['vat_hash'] = $result['vat_hash'];
        $buy_list['inv_info'] = $result['inv_info'];
        $buy_list['available_predeposit'] = $result['available_predeposit'];
        $buy_list['available_rc_balance'] = $result['available_rc_balance'];
        if (is_array($result['rpt_list']) && !empty($result['rpt_list'])) {
            foreach ($result['rpt_list'] as $k => $v) {
                unset($result['rpt_list'][$k]['rpacket_id']);
                unset($result['rpt_list'][$k]['rpacket_end_date']);
                unset($result['rpt_list'][$k]['rpacket_owner_id']);
                unset($result['rpt_list'][$k]['rpacket_code']);
            }
        }
        $buy_list['rpt_list'] = $result['rpt_list'] ? $result['rpt_list'] : array();
        $buy_list['zk_list'] = $result['zk_list'];

        if ($data_area['content']) {
            $store_total_list = Logic('buy_1')->reCalcGoodsTotal($store_total_list,$data_area['content'],'freight');
            //返回可用平台红包
            $result['rpt_list'] = Logic('buy_1')->getStoreAvailableRptList($this->member_info['member_id'],array_sum($store_total_list),'rpacket_limit desc');
            reset($result['rpt_list']);
            if (is_array($result['rpt_list']) && count($result['rpt_list']) > 0) {
                $result['rpt_info'] = current($result['rpt_list']);
                unset($result['rpt_info']['rpacket_id']);
                unset($result['rpt_info']['rpacket_end_date']);
                unset($result['rpt_info']['rpacket_owner_id']);
                unset($result['rpt_info']['rpacket_code']);
            }
        }
        $buy_list['order_amount'] = ncPriceFormat(array_sum($store_total_list)-$result['rpt_info']['rpacket_price']);
        $buy_list['rpt_info'] = $result['rpt_info'] ? $result['rpt_info'] : array();
        $buy_list['address_api'] = $data_area ? $data_area : '';

        foreach ($store_total_list as $store_id => $value) {
            $store_total_list[$store_id] = ncPriceFormat($value);
        }
        $buy_list['store_final_total_list'] = $store_total_list;
                
        if(!$this->check_tsyd_goods($store_cart_list)){
            $buy_list['error'] = "天生一对产品不能单独售卖";
        }
        output_data($buy_list);
    }
    /**
     * 天生一对 成品定制 不能单独售卖
     * @param unknown $store_cart_list
     * @return boolean
     * --1、系列是天生一对 款式分类是情侣戒  的款 不管是成品还是空托 都不能单支(作废)
       2、金托类型是成品的 证书类型是HRD-D的所有款 都不能单支
       3、证书类型是HRD-D的裸钻不能单支
     */
    private function check_tsyd_goods($store_cart_list){

        $tsyd_cp_num = 0;
        $dia_hrd_num  =0;
        $tsyd_jt_num = 0;
                
        $styleModel = new style_apiModel();
        foreach ($store_cart_list as $store_id=>$store_goods_list){
            foreach ($store_goods_list['goods_list'] as $goods){
                if(isset($goods['cert']) && $goods['cert'] =='HRD-D'){
                                      
                    if($goods['style_sn']=="DIA" && $goods['cert'] =='HRD-D'){                    
                        $dia_hrd_num ++;//天生一对裸石
                        continue;
                    }else if($goods['tuo_type']==1){

                        $pinpai_arr = explode('/',trim($goods['pinpai']));
                        if(!empty($pinpai_arr)){
                            $tsyd_cp_num += count($pinpai_arr);
                        }else{
                            $zhushi_num = empty($goods['zhushi_num'])?1:$goods['zhushi_num'];
                            $tsyd_cp_num += $zhushi_num;
                        }


                        continue;                 
                    }
                }                
            }
        }
        if($tsyd_cp_num %2 != 0){
            return false;
        }
        if($dia_hrd_num %2 !=0){
            return false;
        }
        /*if($tsyd_jt_num %2 !=0){
            return false;
        }*/
        return true;
    }

    /**
     * 购物车、直接购买第二步:保存订单入库，产生订单号，开始选择支付方式
     *
     */
    public function buy_step2Op() {
        $param = array();
        $param['ifcart'] = $_POST['ifcart'];
        $param['cart_id'] = array_filter(explode(',', $_POST['cart_id']));
        $param['address_id'] = $_POST['address_id'];
        $param['vat_hash'] = $_POST['vat_hash'];
        $param['offpay_hash'] = $_POST['offpay_hash'];
        $param['offpay_hash_batch'] = $_POST['offpay_hash_batch'];
        $param['pay_name'] = $_POST['pay_name'];
        $param['invoice_id'] = $_POST['invoice_id'];
        $param['rpt'] = $_POST['rpt'];

        //处理代金券
        $voucher = array();
        $post_voucher = explode(',', $_POST['voucher']);
        if(!empty($post_voucher)) {
            foreach ($post_voucher as $value) {
                list($voucher_t_id, $store_id, $voucher_price) = explode('|', $value);
                $voucher[$store_id] = $value;
            }
        }
        $param['voucher'] = $voucher;

        $_POST['pay_message'] = trim($_POST['pay_message'],',');
        $_POST['pay_message'] = explode(',',$_POST['pay_message']);
        $param['pay_message'] = array();
        if (is_array($_POST['pay_message']) && $_POST['pay_message']) {
            foreach ($_POST['pay_message'] as $v) {
                if (strpos($v, '|') !== false) {
                    $v = explode('|', $v);
                    $param['pay_message'][$v[0]] = $v[1];
                }
            }
        }
        $param['pd_pay'] = $_POST['pd_pay'];
        $param['rcb_pay'] = $_POST['rcb_pay'];
        $param['password'] = $_POST['password'];
        $param['fcode'] = $_POST['fcode'];
        $param['order_from'] = 2;
        //增加参数  by gaopeng 2018-03-30
        $param['store_id'] = $this->member_info['store_id'];
        $param['company_id'] = $this->store_info['store_company_id'];
        if(empty($_POST['address_info']) || !is_array($_POST['address_info'])){
            output_error("请填写收货地址");
        }else{
            $address = $_POST['address_info'];
            if(empty($address['true_name'])){
                output_error("请填写收货人姓名");
            }else if(empty($address['mob_phone'])){
                output_error("请填写收货人手机号");
            }
            if($address['chain_id']==0){
                 if(empty($address['area_id']) || empty($address['address'])){
                     output_error("请填写收货人地址");
                 }
            }
            $address['provice_id'] = (int)$address['provice_id'];
            $address['city_id'] = (int)$address['city_id'];
            $address['area_id'] = (int)$address['area_id']; 
            $_POST['address_info'] = $address;

            $post_data = array(
                'member_name'=>$address['true_name'],
                'cell_phone'=>$address['mob_phone'],
                'source_channel'=>$this->member_info['store_id'],  //店铺ID
                'belong_channel'=>$this->member_info['store_id'],  //店铺ID
                'creator_id'=>$this->member_info['member_id'],//创建人编号
                'source_code'=>$address['source_id'],
                'create_time'=>date('Y-m-d H:i:s')
            );
            $sales_api = data_gateway('isales');
            $rest = $sales_api->get_vip_list_by_mob($address['mob_phone']);
            $province_id = 0;
            $city_id = 0;
            $region_id = 0;
            $addr_content = "";
            if(!empty($rest)) {
                //unset($post_data['source_code']);
                $rest = json_decode($rest);
                $_POST['address_info']['org_source_id'] = $rest->source_code;//原始客户来源ID
                 /*
                $post_data['source_code'] = !empty($rest->source_code)?$rest->source_code:$address['source_id'];
                $profile = $rest->profile;
                $province_id = $profile->province_id;
                $city_id = $profile->city_id;
                $region_id = $profile->region_id;
                $addr_content = $profile->address;
                 */
            }
            $res = $sales_api->save_vip_info($post_data);
            if(in_array($res['code'], array('200', '201'))){
                $memberlist = json_decode($res[0]);
            }else{
                $error = json_decode($res[0]);
                //output_error("保存会员信息失败！".$error[0]->message);
            }
        }
        //print_r($_POST['address_info']);exit;
        $param['address_info'] = $_POST['address_info'];
        $logic_buy = logic('buy');

        //得到会员等级
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if ($member_info){
            $member_gradeinfo = $model_member->getOneMemberGrade(intval($member_info['member_exppoints']));
            $member_discount = $member_gradeinfo['orderdiscount'];
            $member_level = $member_gradeinfo['level'];
        } else {
            $member_discount = $member_level = 0;
        }
        $result = $logic_buy->buyStep2($param, $this->member_info['member_id'], $this->member_info['member_name'], $this->member_info['member_email'],$member_discount,$member_level);
        if(!$result['state']) {
            output_error($result['msg']);
        }
        $order_info = current($result['data']['order_list']);
        output_data(array('order_id'=>$order_info['order_id'],'pay_sn' => $result['data']['pay_sn'],'payment_code'=>$order_info['payment_code']));
    }

    /**
     * 验证密码
     */
    public function check_passwordOp() {
        if(empty($_POST['password'])) {
            output_error('参数错误');
        }

        $model_member = Model('member');

        $member_info = $model_member->getMemberInfoByID($this->member_info['member_id']);
        if($member_info['member_paypwd'] == md5($_POST['password'])) {
            output_data('1');
        } else {
            output_error('密码错误');
        }
    }

    /**
     * 更换收货地址
     */
    public function change_addressOp() {
        $logic_buy = Logic('buy');
        if (empty($_POST['city_id'])) {
            $_POST['city_id'] = $_POST['area_id'];
        }
        
        $data = $logic_buy->changeAddr($_POST['freight_hash'], $_POST['city_id'], $_POST['area_id'], $this->member_info['member_id']);
        if(!empty($data) && $data['state'] == 'success' ) {
            output_data($data);
        } else {
            output_error('地址修改失败');
        }
    }

    /**
     * 实物订单支付(新接口)
     */
    public function payOp() {
        $pay_sn = $_POST['pay_sn'];
        if (!preg_match('/^\d{18}$/',$pay_sn)){
            output_error('该订单不存在');
        }

        //查询支付单信息
        $model_order= Model('order');
        $pay_info = $model_order->getOrderPayInfo(array('pay_sn'=>$pay_sn,'buyer_id'=>$this->member_info['member_id']),true);
        if(empty($pay_info)){
            output_error('该订单不存在');
        }
    
        //取子订单列表
        $condition = array();
        $condition['pay_sn'] = $pay_sn;
        $condition['order_state'] = array('in',array(ORDER_STATE_NEW,ORDER_STATE_PAY));
        $order_list = $model_order->getOrderList($condition,'','*','','',array(),true);
        if (empty($order_list)) {
            output_error('未找到需要支付的订单');
        }

        //定义输出数组
        $pay = array();
        //支付提示主信息
        //订单总支付金额(不包含货到付款)
        $pay['pay_amount'] = 0;
        //充值卡支付金额(之前支付中止，余额被锁定)
        $pay['payed_rcb_amount'] = 0;
        //预存款支付金额(之前支付中止，余额被锁定)
        $pay['payed_pd_amount'] = 0;
        //还需在线支付金额(之前支付中止，余额被锁定)
        $pay['pay_diff_amount'] = 0;
        //账户可用金额
        $pay['member_available_pd'] = 0;
        $pay['member_available_rcb'] = 0;

        $logic_order = Logic('order');

        //计算相关支付金额
        foreach ($order_list as $key => $order_info) {
            if (!in_array($order_info['payment_code'],array('offline','chain'))) {
                if ($order_info['order_state'] == ORDER_STATE_NEW) {
                    $pay['payed_rcb_amount'] += $order_info['rcb_amount'];
                    $pay['payed_pd_amount'] += $order_info['pd_amount'];
                    $pay['pay_diff_amount'] += $order_info['order_amount'] - $order_info['rcb_amount'] - $order_info['pd_amount'];
                }
            }
        }
        if ($order_info['chain_id'] && $order_info['payment_code'] == 'chain') {
            $order_list[0]['order_remind'] = '下单成功，请在'.CHAIN_ORDER_PAYPUT_DAY.'日内前往门店提货，逾期订单将自动取消。';
            $flag_chain = 1;
        }

        //如果线上线下支付金额都为0，转到支付成功页
        if (empty($pay['pay_diff_amount'])) {
            output_error('订单重复支付');
        }

        $payment_list = Model('mb_payment')->getMbPaymentOpenList();
        if(!empty($payment_list)) {
            foreach ($payment_list as $k => $value) {
                if ($value['payment_code'] == 'wxpay') {
                    unset($payment_list[$k]);
                    continue;
                }
                unset($payment_list[$k]['payment_id']);
                unset($payment_list[$k]['payment_config']);
                unset($payment_list[$k]['payment_state']);
                unset($payment_list[$k]['payment_state_text']);
            }
        }
            //显示预存款、支付密码、充值卡
            $pay['member_available_pd'] = $this->member_info['available_predeposit'];
            $pay['member_available_rcb'] = $this->member_info['available_rc_balance'];
            $pay['member_paypwd'] = $this->member_info['member_paypwd'] ? true : false;
        $pay['pay_sn'] = $pay_sn;
        $pay['payed_amount'] = ncPriceFormat($pay['payed_rcb_amount']+$pay['payed_pd_amount']);
        unset($pay['payed_pd_amount']);unset($pay['payed_rcb_amount']);
        $pay['pay_amount'] = ncPriceFormat($pay['pay_diff_amount']);
        unset($pay['pay_diff_amount']);
        $pay['member_available_pd'] = ncPriceFormat($pay['member_available_pd']);
        $pay['member_available_rcb'] = ncPriceFormat($pay['member_available_rcb']);
        $pay['payment_list'] = $payment_list ? array_values($payment_list) : array();
        output_data(array('pay_info'=>$pay));
    }

    /**
     * AJAX验证支付密码
     */
    public function check_pd_pwdOp(){
        if (empty($_POST['password'])) {
            output_error('支付密码格式不正确');
        }
        $buyer_info = Model('member')->getMemberInfoByID($this->member_info['member_id'],'member_paypwd');
        if ($buyer_info['member_paypwd'] != '') {
            if ($buyer_info['member_paypwd'] === md5($_POST['password'])) {
                output_data('1');
            }
        }
        output_error('支付密码验证失败');
    }

    /**
     * F码验证
     */
    public function check_fcodeOp() {
        $goods_id = intval($_POST['goods_id']);
        if ($goods_id <= 0) {
            output_error('商品ID格式不正确');
        }
        if ($_POST['fcode'] == '') {
            output_error('F码格式不正确');
        }
        $result = logic('buy')->checkFcode($goods_id, $_POST['fcode']);
        if ($result['state']) {
            output_data('1');
        } else {
            output_error('F码验证抢购');
        }
    }
    
    public function get_goods_infoOp(){
        
        if(empty($_POST['goods_id'])){
            output_error("参数错误：goods_id 不能为空");
        }
        if(empty($_POST['goods_type'])){
            output_error("参数错误：goods_type 不能为空");
        }
        $goods_id = $_POST['goods_id'];
        $goods_type = $_POST['goods_type'];
        
        $logic_buy_1 = Logic("buy_1");
        
        $member_id = $this->member_info['member_id'];
        $store_id = $this->member_info['store_id'];
        $company_id = $this->store_info['company_id'];
        $res = $logic_buy_1->apiGetGoodsInfo($goods_id,$goods_type,$store_id,$company_id,$member_id);
        if($res['state']==true){
            output_data($res['data']);
        }else{
            output_error($res['msg']);
        }
    }
}
