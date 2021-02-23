<?php
/**
 * 无订单退货
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_haControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct() ;
        Language::read('store_bill,store_goods_index');
    }

    /**
     * 无订单退货单
     */
    public function indexOp(){
        $bill_model=Model('erp_bill');
        $company_id = $_SESSION['store_company_id'];
        $warehouse_model = Model('erp_warehouse');
        $model_goodsitems = Model('goods_items');
        $model_refund = Model('refund_return');
        if(chksubmit()){
            try{
                $model_refund->beginTransaction();
                $goods_itemid=$_POST['goods_itemid'];
                if(empty($goods_itemid)) throw new Exception("货号不能为空！");
                if(empty($_POST['return_amount'])) throw new Exception("退货价不能为空！");
                if(empty($_POST['sales_price'])) throw new Exception("销售价不能为空！");
                //if(empty($_POST['to_house_id'])) throw new Exception("入库仓不能为空！");
                if(empty($_POST['buyer_message'])) throw new Exception("备注信息不能为空！");
                $to_store_id = "";
                $to_house_id = "";
                //$house_arr = explode("|",$_POST['to_house_id']);
                //if(count($house_arr)==2){
                //    $to_store_id = (int)$house_arr[0];
                //    $to_house_id = (int)$house_arr[1];
                //}else{
                //    throw new Exception("入库仓库参数错误！");
                //}
                $billha_info = array(
                    'sales_price'=>$_POST['sales_price'],
                    'to_store_id'=>$to_store_id,
                    'to_house_id'=>$to_house_id
                    );
                $itemsinfo = $model_goodsitems->getGoodsItemInfo(array('goods_id'=>$goods_itemid));
                if($itemsinfo['is_on_sale'] !=3){
                    throw new Exception("货号不是已销售状态");
                }
                if(!empty($itemsinfo['order_sn']) || !empty($itemsinfo['order_detail_id'])){
                    throw new Exception("请走正常退货流程");
                }
                if($itemsinfo['company_id'] != $company_id){
                    throw new Exception("只能退自己门店的货品");
                }
                $warehouse = $warehouse_model->getWareHouseInfo(array('house_id'=>$itemsinfo['warehouse_id']));
                if(isset($warehouse) && $warehouse['lock']>0){
                    throw new Exception("{$goods_itemid}货号所属仓库正在盘点中");
                }
                $refund_array = array();
                $refund_array['reason_info'] = '无订单退货';
                $refund_array['reason_id'] = 0;//退货退款原因
                $refund_array['pic_info'] = "";//上传凭证
                $refund_array['order_lock'] = '1';//锁定类型:1为不用锁定,2为需要锁定
                $refund_array['refund_type'] = '2';//类型:1为退款,2为退货
                $refund_array['return_type'] = '2';//退货类型:1为不用退货,2为需要退货
                $refund_array['seller_state'] = '1';//状态:1为待审核,2为同意,3为不同意
                $refund_array['refund_amount'] = ncPriceFormat($_POST['return_amount']);
                $refund_array['breach_amount'] = '0';
                $refund_array['goods_num'] = '1';
                $refund_array['buyer_message'] = $_POST['buyer_message'];
                $refund_array['add_time'] = time();
                $refund_array['return_form'] = '0';
                $refund_array['store_id'] = $_SESSION['store_id'];
                $refund_array['billha_info'] = serialize($billha_info);
                $state = $model_refund->addHaReturn($refund_array,$itemsinfo);
                if(!$state) throw new Exception("无订单退货单保存失败");
                $model_refund->commit();
                $result['success'] = 1;
                $result['data'] = $state;
                exit(json_encode($result));
            }catch (Exception $ex){
                $result = callback(false,$ex->getMessage());
                $model_refund->rollback();
                exit(json_encode($result));
            }
        }
        $warehouse_list = $warehouse_model->getWareHouseList(array('company_id'=>$company_id,'is_enabled'=>1));
        Tpl::output('warehouse_list', $warehouse_list);
        Tpl::showpage('erp_bill_ha.index');
    }
}
