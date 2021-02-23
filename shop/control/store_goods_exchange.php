<?php
/**
 * 换货
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class store_goods_exchangeControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index,deliver');
    }

    /**
     * 换货列表
     *
     */

    public function indexOp(){
        $order_sn = isset($_GET['order_sn']) ? $_GET['order_sn'] : '';

        if($order_sn != ''){
            $model_order = Model('order');
            $condition = array();
            $condition['order_sn'] = $order_sn;
            $condition['store_id'] = $_SESSION['store_id'];
            $order_info = $model_order->getOrderInfo($condition,array('order_common','order_goods'));
            Tpl::output('order_info',$order_info);

        }
        Tpl::output('order_sn',$order_sn);


        Tpl::showpage('store_order.exchange');
    }

      public function exchangeOp(){
          $return = array('code'=>1,'msg'=>'');
          $order_id = isset($_POST['order_id'])?$_POST['order_id']:'';
          $rec_id = isset($_POST['goods_id'])?$_POST['goods_id']:'';
          $goods_sn = isset($_POST['goods_sn'])?$_POST['goods_sn']:'';
          if($order_id == '' || $rec_id == '' || $goods_sn == ''){
              $return = array('code'=>0,'msg'=>'缺失参数');
               die(json_encode($return));
          }
          $model_order = new orderModel();
          $model_goods_items = Model('goods_items');
          $model_order->beginTransaction();
          $model_goods_items->beginTransaction();


          //判断订单是否能换货（order_state >= ORDER_STATE_NEW 且 order_state < ORDER_STATE_SEND 并不是锁定状态）
          $order_arr = $model_order->getOrderInfo(['order_id'=>$order_id],array(),'order_sn,order_state,lock_state,is_xianhuo,pay_status');
          if($order_arr['order_state'] < ORDER_STATE_NEW || $order_arr['order_state'] > ORDER_STATE_SEND || $order_arr['lock_state']){
              $return = array('code'=>0,'msg'=>'订单没付款或者订单已发货或者订单已被锁定');
              die(json_encode($return));
          }

          //获取要换货的商品信息
          $company_id=$_SESSION['store_company_id'];
          $goods_w_arr = $model_goods_items->getGoodsItemInfo(['goods_id'=>$goods_sn,'is_on_sale'=>2,'company_id'=>$company_id,'order_detail_id'=>0],'goods_sn,product_type,cat_type,zhengshuhao');
          if(empty($goods_w_arr)){
              $return = array('code'=>0,'msg'=>'货号不存在或者此商品不是库存状态或者此商品已经绑定了订单');
              die(json_encode($return));
          }
          //获取被换货的商品信息
          $goods_b_arr = $model_order->getOrderGoodsInfo(['rec_id'=>$rec_id],'style_sn,goods_itemid,cert_id,bc_id,is_xianhuo');
          if ($goods_b_arr['style_sn'] == 'DIA' && (!in_array($goods_w_arr['product_type'], ['钻石', '裸石']) || $goods_w_arr['zhengshuhao'] != $goods_b_arr['cert_id'])) {
            $return = array('code'=>0,'msg'=>'产品线不是裸石，或裸石证书号不一致');
            die(json_encode($return));
          } else if($goods_b_arr['style_sn'] != 'DIA' && $goods_w_arr['goods_sn'] !=  $goods_b_arr['style_sn']) {
            $return = array('code'=>0,'msg'=>'换货款号不一致');
            die(json_encode($return));
          }


         try{

             //更新订单实物货号,同时把布产状态改成不需布产；如果订单状态在允许布产前且没有生产，订单明细期货变成现货
             $change_is_xianhuo = false;
             if($order_arr['order_state'] <= ORDER_STATE_TO_BC && empty($goods_b_arr['bc_id']) && $goods_b_arr['is_xianhuo'] == 0){
                  $data = array('goods_itemid'=>$goods_sn,'is_xianhuo'=>1);
                  $change_is_xianhuo = true;
              }else{
                  $data = array('goods_itemid'=>$goods_sn,'bc_status'=>11);
              }
              $data['is_exchange'] = 1;
              $res = $model_order->editOrderGoods($data,['rec_id'=>$rec_id]);
              if($res){

                  //如果原来商品有货号，则取消商品绑定订单
                  if($goods_b_arr['goods_itemid'] != ''){
                      $arr = $model_goods_items->getGoodsItemInfo(['goods_id'=>$goods_b_arr['goods_itemid']]);
                      if($arr) $model_goods_items->editGoodsItems(['order_detail_id'=>'0','order_sn'=>''],['goods_id'=>$goods_b_arr['goods_itemid']]);
                      $msg = "明细".$goods_b_arr['goods_itemid']." 换货成 $goods_sn";
                  }else{
                      $msg = "明细换货成 $goods_sn";
                  }

                  //货号绑定订单
                  $model_goods_items->editGoodsItems(['order_detail_id'=>$rec_id,'order_sn'=>$order_arr['order_sn']],['goods_id'=>$goods_sn]);


                  //如果订单明细期货改变现货了，需判断订单明细是否已经全是现货，如果全是现货，期货订单变成现货订单；
                  //如果付完全款，订单状态为代发货，没付完全款则为待付尾款；
                  if($change_is_xianhuo){
                      $is_qihuo_arr = $model_order->getOrderGoodsList(['order_id'=>$order_id,'is_xianhuo'=>0],'rec_id');
                      if(empty($is_qihuo_arr)){
                          $order_data['is_xianhuo'] = 1;
                          if($order_arr['pay_status'] == 3){
                              $order_data['order_state'] = ORDER_STATE_TOSEND;
                              $msg .= ";是否现货改变成是，订单状态改变成待发货";
                          }else{
                              $order_data['order_state'] = ORDER_STATE_NEW;
                              $msg .= ";是否现货改变成是，订单状态改变成待付尾款";
                          }
                          $model_order->editOrder($order_data,['order_id'=>$order_id]);

                      }
                  }



                  //记录订单日志
                  $data = array();
                  $data['order_id'] = $order_id;
                  $data['log_role'] = 'seller';
                  $data['log_user'] = $_SESSION['seller_name'];
                  $data['log_msg'] = $msg;
                  $data['log_orderstate'] = $order_arr['order_state'];
                  $model_order->addOrderLog($data);
                  $model_order->commit();
                  $model_goods_items->commit();

                  if ($order_arr['is_xianhuo'] == 0) {
                      EventEmitter::dispatch("ishop", array('event' => 'pull_order', 'order_sn' => $order_arr['order_sn'], 'order_id' => $order_id));
                  }

              }else{
                  $return = array('code'=>0,'msg'=>'换货失败');
                  die(json_encode($return));
              }
         }catch (Exception $e){
             $model_order->rollback();
             $model_goods_items->rollback();

            $return = array('code'=>0,'msg'=>$e->getMessage());
            die(json_encode($return));
         }
          $return['msg'] = '换货成功';
          die(json_encode($return));















      }






}
