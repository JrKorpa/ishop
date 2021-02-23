<?php
/**
 * 我的购物车
 *
 *
 * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class member_cartControl extends mobileMemberControl {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 购物车列表
     */
    public function cart_listOp() {
        $model_cart = Model('cart');

        $condition = array('buyer_id' => $this->member_info['member_id'],'store_id'=>$this->member_info['store_id']);
        $cart_list  = $model_cart->listCart('db', $condition);

        // 购物车列表 [得到最新商品属性及促销信息]
        $logic_buy_1 = logic('buy_1');
        $cart_list = $logic_buy_1->getGoodsCartList($cart_list);

        //购物车商品以店铺ID分组显示,并计算商品小计,店铺小计与总价由JS计算得出
        $store_cart_list = array();
        $sum = 0;
        foreach ($cart_list as $cart) {
            if (!empty($cart['gift_list'])) {
                foreach ($cart['gift_list'] as $key => $val) {
                    $cart['gift_list'][$key]['goods_image_url'] = cthumb($val['gift_goodsimage'], $cart['store_id']);
                }
                $cart['gift_list'] = array_values($cart['gift_list']);
            }
            $store_cart_list[$cart['store_id']]['store_id'] = $cart['store_id'];
            $store_cart_list[$cart['store_id']]['store_name'] = $cart['store_name'];
            $cart['goods_image_url'] = cthumb($cart['goods_image'], $cart['store_id']);
            $cart['goods_total'] = ncPriceFormat($cart['goods_price'] * $cart['goods_num']);
            $cart['xianshi_info'] = $cart['xianshi_info'] ? $cart['xianshi_info'] : array();
            $cart['groupbuy_info'] = $cart['groupbuy_info'] ? $cart['groupbuy_info'] : array();
            //print_r($cart);
            $cart['kezi'] = formatKezi($cart['kezi']);
            $store_cart_list[$cart['store_id']]['goods'][] = $cart;
            $sum += $cart['goods_total'];
        }
        
        // 店铺优惠券
        /*
        $condition = array();
        $condition['voucher_t_gettype'] = 3;
        $condition['voucher_t_state'] = 1;
        $condition['voucher_t_end_date'] = array('gt', time());
        $condition['voucher_t_mgradelimit'] = array('elt', $this->member_info['level']);
        $condition['voucher_t_store_id'] = array('in', array_keys($store_cart_list));
        $voucher_template = Model('voucher')->getVoucherTemplateList($condition);
        if (!empty($voucher_template)) {
            foreach ($voucher_template as $val) {
                $param = array();
                $param['voucher_t_id'] = $val['voucher_t_id'];
                $param['voucher_t_price'] = $val['voucher_t_price'];
                $param['voucher_t_limit'] = $val['voucher_t_limit'];
                $param['voucher_t_end_date'] = date('Y年m月d日', $val['voucher_t_end_date']);
                $store_cart_list[$val['voucher_t_store_id']]['voucher'][] = $param;
            }
        }
        */
        //取得店铺级活动 - 可用的满即送活动
        /*$mansong_rule_list = $logic_buy_1->getMansongRuleList(array_keys($store_cart_list));
        if (!empty($mansong_rule_list)) {
            foreach ($mansong_rule_list as $key => $val) {
                $store_cart_list[$key]['mansong'] = $val;
            }
        }
        
        //取得哪些店铺有满免运费活动
        $free_freight_list = $logic_buy_1->getFreeFreightActiveList(array_keys($store_cart_list));
        if (!empty($free_freight_list)) {
            foreach ($free_freight_list as $key => $val) {
                $store_cart_list[$key]['free_freight'] = $val;
            }
        }*/
        output_data(array('cart_list' => array_values($store_cart_list), 'sum' => ncPriceFormat($sum), 'cart_count' => count($cart_list)));
    }

    /**
     * 购物车列表
     */
    public function cart_list_oldOp() {
        $model_cart = Model('cart');
    
        $condition = array('buyer_id' => $this->member_info['member_id']);
        $cart_list  = $model_cart->listCart('db', $condition);
    
        // 购物车列表 [得到最新商品属性及促销信息]
        $cart_list = logic('buy_1')->getGoodsCartList($cart_list, $jjgObj);
        $sum = 0;
        foreach ($cart_list as $key => $value) {
            $cart_list[$key]['goods_image_url'] = cthumb($value['goods_image'], $value['store_id']);
            $cart_list[$key]['goods_sum'] = ncPriceFormat($value['goods_price'] * $value['goods_num']);
            $sum += $cart_list[$key]['goods_sum'];
        }
    
        output_data(array('cart_list' => $cart_list, 'sum' => ncPriceFormat($sum)));
    }

    /**
     * 购物车添加(支持批量添加)
     * by gaopeng 2018-3-30
     */
    public function cart_addOp() {
        $goods_ids = explode("@",$_POST['goods_id']);
        $quantitys = explode("@",$_POST['quantity']);
        $goods_types = explode("@",$_POST['goods_type']);
        //$styleApi = data_gateway('istyle');
        //$diamondApi = data_gateway('idiamond');
        $model_cart = Model('cart');
        $logic_buy_1 = Logic('buy_1');
        $cart_list = array();
        $member_id = $this->member_info['member_id'];        
        $store_id = $this->member_info['store_id'];
        $company_id = $this->store_info['store_company_id'];

        //校验商品信息
        foreach ($goods_ids as $k=>$goods_id){
            $quantity   = $quantitys[$k];
            $goods_type = $goods_types[$k];
            if(empty($goods_id) || $quantity <= 0 || $goods_type<=0) {
                output_error('参数错误!');
            }
            $goods_id_list = array($goods_id);
            if($goods_type==2){//裸钻
                $diamond_api = data_gateway('idiamond');
                $where = array('cert_id' => $goods_id);
                $byApi = $diamond_api->get_diamond_info($where);
                $diamondlist = isset($byApi['return_msg'])?$byApi['return_msg']:array();
                $diainfo=$diamond_api->get_diamond_by_kuan_sn($diamondlist['kuan_sn']);//天生一对
                $diainfo = isset($diainfo['return_msg'])?$diainfo['return_msg']:array();
                if(!empty($diainfo)){
                    $cert_ids = array_column($diainfo, 'cert_id');
                    $goods_id_list = array_merge($goods_id_list,$cert_ids);
                    $goods_id_list = array_unique($goods_id_list);
                }
            }
            foreach ($goods_id_list as $goodsid) {
                $res = $logic_buy_1->apiGetGoodsInfo($goodsid,$goods_type,$store_id,$company_id,$member_id,true);
                if($res['state']==false){
                    output_error($res['msg']);
                }else{
                    $goods_info = $res['data'];
                    //裸钻+空托一起下单 
                    if(count($goods_ids)==2 && $goods_info['goods_type']==2){
                        $goods_info['xiangqian'] = '需工厂镶嵌';
                    }
                }
                $goods_tsyd = "";
                if($goods_type ==2 && !empty($goods_info['kuan_sn'])){
                    $goods_tsyd = $goods_info['kuan_sn'];
                }
                $param = array();
                $param['buyer_id']  = $this->member_info['member_id'];
                $param['store_id']  = $this->member_info['store_id'];
                $param['store_name'] = $this->store_info['store_name'];
                $param['goods_id']  = $goods_info['goods_id'];
                $param['goods_name'] = $goods_info['goods_name'];
                $param['goods_price'] = $goods_info['goods_price'];
                $param['goods_num'] = $quantity;
                $param['goods_image'] = $goods_info['goods_image'];
                $param['goods_type'] = $goods_type;
                $param['goods_tsyd'] = $goods_tsyd;
                $param['goods_info']  = serialize($goods_info);
                $cart_list[] = $param;
            }     
        }
        try{
            //商品信息保存到购物车
            $model_cart->beginTransaction();            
            $data = array();
            foreach ($cart_list as $cart){
                $cart_id = $model_cart->addCart($cart, 'db', $cart['goods_num']);
                if(!$cart_id){
                    throw new Exception("收藏失败");
                }

                //记录裸钻加入购物车的信息
                if($cart['goods_type'] == 2){
                    //print_r($cart);exit;
                    $goods_info = unserialize($cart['goods_info']);
                    $diainfo_pay = array(
                        'seller_id'=>$cart['buyer_id'],
                        'cart_id'=>$cart_id,
                        'cert_id'=>$goods_info['cert_id'],
                        'pifajia'=>$goods_info['pifajia'],
                        'goods_price'=>$goods_info['goods_price'],
                        'jiajialv'=>$goods_info['jiajialv'],
                        'add_time'=>date('Y-m-d H:i:s',time()),
                        'store_id'=>$cart['store_id'],
                        'store_name'=>$cart['store_name'],
                        'type'=>1,
                    );

                    $diamond_pay_log_model = new diamond_pay_logModel();
                    $res = $diamond_pay_log_model->getDiamondPayLog(['cart_id'=>$cart_id]);
                    if(!$res){
                        $diamond_pay_log_model ->addDiamondPayLog($diainfo_pay);
                    }else{
                        $diamond_pay_log_model ->editDiamondPayLog($diainfo_pay,['cart_id'=>$cart_id]);
                    }


                }



                $data[] = array('cart_id'=>$cart_id,'goods_type'=>$cart['goods_type'],'goods_price'=>$cart['goods_price']);
            }
            $model_cart->commit();
            output_data(array('cart_list'=>$data));
        }catch (Exception $e){
            $model_cart->rollback();
            output_error('收藏失败');
        }
    }

    /**
     * 购物车删除
     */
    public function cart_delOp() {
        $cart_id = intval($_POST['cart_id']);

        $model_cart = Model('cart');

        if($cart_id > 0) {
            $condition = array();
            $condition['buyer_id'] = $this->member_info['member_id'];
            $condition['cart_id'] = $cart_id;
            $cart_list  = $model_cart->listCart('db', $condition);
            $goods_tsyd = isset($cart_list[0]['goods_tsyd'])?$cart_list[0]['goods_tsyd']:"";
            if(!empty($goods_tsyd)){
                $condition = array();
                $condition['buyer_id'] = $this->member_info['member_id'];//删除天生一对
                $condition['goods_tsyd'] = $goods_tsyd;
                $model_cart->delCart('db', $condition);
            }else{
                $model_cart->delCart('db', $condition);
            }

            //购物车裸钻删除时,把裸钻购买记录同时删除
            $diamond_pay_log_model = new diamond_pay_logModel();
            $res = $diamond_pay_log_model->getDiamondPayLog(['cart_id'=>$cart_id]);
            if($res){
                $diamond_pay_log_model->delDiamondPayLog(['cart_id'=>$cart_id]);
            }

        }

        output_data('1');
    }

    /**
     * 更新购物车购买数量
     */
    public function cart_edit_quantityOp() {
        $cart_id = intval(abs($_POST['cart_id']));
        $quantity = intval(abs($_POST['quantity']));
        if(empty($cart_id) || empty($quantity)) {
            output_error('参数错误');
        }

        $model_cart = Model('cart');

        $cart_info = $model_cart->getCartInfo(array('cart_id'=>$cart_id, 'buyer_id' => $this->member_info['member_id']));

        //检查是否为本人购物车
        if($cart_info['buyer_id'] != $this->member_info['member_id']) {
            output_error('参数错误');
        }

        //检查库存是否充足
        if(!$this->_check_goods_storage($cart_info, $quantity, $this->member_info['member_id'])) {
            output_error('超出限购数或库存不足');
        }

        $data = array();
        $data['goods_num'] = $quantity;
        $update = $model_cart->editCart($data, array('cart_id'=>$cart_id));
        if ($update) {
            $return = array();
            $return['quantity'] = $quantity;
            $return['goods_price'] = ncPriceFormat($cart_info['goods_price']);
            $return['total_price'] = ncPriceFormat($cart_info['goods_price'] * $quantity);
            output_data($return);
        } else {
            output_error('修改失败');
        }
    }

    /**
     * 检查库存是否充足
     */
    private function _check_goods_storage(& $cart_info, $quantity, $member_id) {
        $model_goods= Model('goods');
        $model_bl = Model('p_bundling');
        $logic_buy_1 = Logic('buy_1');

        if ($cart_info['bl_id'] == '0') {
            //普通商品
            $goods_info = $model_goods->getGoodsOnlineInfoAndPromotionById($cart_info['goods_id']);

            //手机专享
            $logic_buy_1->getMbSoleInfo($goods_info);
            
            //抢购
            $logic_buy_1->getGroupbuyInfo($goods_info);
            if ($goods_info['ifgroupbuy']) {
                if ($goods_info['upper_limit'] && $quantity > $goods_info['upper_limit']) {
                    return false;
                }
            }

            //限时折扣
            $logic_buy_1->getXianshiInfo($goods_info,$quantity);

            if(intval($goods_info['goods_storage']) < $quantity) {
                return false;
            }
            $goods_info['cart_id'] = $cart_info['cart_id'];
            $cart_info = $goods_info;
        } else {
            //优惠套装商品
            $bl_goods_list = $model_bl->getBundlingGoodsList(array('bl_id' => $cart_info['bl_id']));
            $goods_id_array = array();
            foreach ($bl_goods_list as $goods) {
                $goods_id_array[] = $goods['goods_id'];
            }
            $bl_goods_list = $model_goods->getGoodsOnlineListAndPromotionByIdArray($goods_id_array);

            //如果有商品库存不足，更新购买数量到目前最大库存
            foreach ($bl_goods_list as $goods_info) {
                if (intval($goods_info['goods_storage']) < $quantity) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * 查询购物车商品数量
     */
    function cart_countOp() {
        $param['cart_count'] = Model('cart')->countCartByMemberId($this->member_info['member_id']);
        output_data($param);
    }

    /**
     * 批量添加购物车
     * cartlist 格式为goods_id1,num1|goods_id2,num2
     */
    public function cart_batchaddOp(){
        $param = $_POST;
        $cartlist_str = trim($param['cartlist']);
        $cartlist_arr = $cartlist_str?explode('|',$cartlist_str):array();
        if(!$cartlist_arr) {
            output_error('参数错误');
        }

        $cartlist_new =  array();
        foreach($cartlist_arr as $k=>$v){
            $tmp = $v?explode(',',$v):array();
            if (!$tmp) {
                continue;
            }
            $cartlist_new[$tmp[0]]['goods_num'] = $tmp[1];
        }
        Model('cart')->batchAddCart($cartlist_new, $this->member_info['member_id'], $this->member_info['store_id']);
        output_data('1');
    }

    //确认天生一对都加入订单
    public function check_tsydOp()
    {
        $cart_id = $_POST['cart_id'];
        $model_cart = Model('cart');
        $condition = array('cart_id'=>$cart_id);
        $cart_list  = $model_cart->listCart('db', $condition);
        $cartlist = isset($cart_list[0])&&!empty($cart_list[0])?$cart_list[0]:array();
        $fx_cart_id = "";
        if(!empty($cartlist)){
            $goods_tsyd = $cartlist['goods_tsyd'];
            if(!empty($goods_tsyd)){
                $condition = array();
                $condition['buyer_id'] = $this->member_info['member_id'];//删除天生一对
                $condition['goods_tsyd'] = $goods_tsyd;
                $cartinfo = $model_cart->listCart('db', $condition);
                if(!empty($cartinfo)){
                    foreach ($cartinfo as $key => $cart) {
                        if($cart['cart_id'] != $cart_id){
                            $fx_cart_id = $cart['cart_id'];
                        }
                    }
                }
            }
        }
        $data = array('fx_cart_id'=>$fx_cart_id);
        output_data($data);
    }
}
