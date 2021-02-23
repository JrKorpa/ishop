<?php
/**
 * ERP单据查询
 * *  (c) 2015-2018 . (http://www.kela.cn)
 * @license    http://www.kela.cn
 * @link       交流QQ：191793328
 * @since      珂兰技术中心提供技术支持
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class erp_bill_checkoutControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct() ;
        Language::read('store_bill,store_goods_index');
        
    }

    /**
     * 门店批发销售结算
     */
    public function indexOp(){
        $bill_model=Model('erp_bill');
        if(chksubmit()){
            try{
                $bill_no=$_POST['bill_no'];
                $process_type=$_POST['process_type'];
                if(empty($_POST['bill_no'])) throw new Exception("批发销售单号不能为空！");
                if(empty($_POST['goods_itemid'])) throw new Exception("货号不能为空！");
                $bill_info=$bill_model->getErpBillInfo(array('bill_no'=>$bill_no));
                if(empty($bill_info)) throw new Exception("门店批发销售单{$bill_no}不存在！");
                if($bill_info["bill_type"]!='S'||$bill_info["item_type"]!='PF') throw new Exception("订单号{$bill_no}非批发销售单！");
                if($bill_info['from_company_id'] != $_SESSION['store_company_id']){
                    throw new Exception("只有制单公司才能操作结算！");
                }
                $erp_bill_goods_model =Model('erp_bill_goods');
                $goods_list = preg_replace("/\s+/is",",",trim($_POST['goods_itemid']));
                $goods_list = explode(',',$goods_list);
                $goods_list = array_filter($goods_list);
                $total_num = 0;
                $pass_goods_list = array();
                $bill_model->beginTransaction();
                foreach ($goods_list as $goods_itemid) {
                    if (in_array($goods_itemid, $pass_goods_list)) continue;
                    $pass_goods_list[] = $goods_itemid;
                    $total_num++;
                    if ($total_num > 10000)  throw new Exception("文件记录行不能大于10000！");
                    $erp_bill_goods_info= $erp_bill_goods_model->getErpBillGoodsInfo(array('bill_no' => $bill_no,'goods_itemid'=>$goods_itemid));
                    if (empty($erp_bill_goods_info)) throw new Exception("订单{$bill_no}中货号{$goods_itemid}不存在");
                    if ($erp_bill_goods_info["is_settled"]>0) throw new Exception("货号{$goods_itemid}已结算");

                    $erp_bill_goods_data=array();
                    $erp_bill_goods_data["settle_user"]=$_SESSION["seller_name"];
                    $erp_bill_goods_data["settle_time"]=date('Y-m-d H:i:s');
                    //JS：结算，TH:退货
                    if($process_type=="JS"){
                        $erp_bill_goods_data["is_settled"]=1;
                        $erp_bill_goods_model->editErpBillGoods(array('bill_no' => $bill_no,'goods_itemid'=>$goods_itemid),$erp_bill_goods_data);
                    }else if($process_type=="TH"){
                        //验证下此货号是否有【已审核】的【批发类型的销售退货单】
                        $condition["sql"]=array("exp"," bill_no in (select distinct(bill_no) from erp_bill_goods where goods_itemid='{$goods_itemid}' and bill_no like 'D%')");
                        $bill_info=$bill_model->getErpBillInfo($condition);
                        if(empty($bill_info)|| !in_array($bill_info["bill_status"],array(2, 4))) throw new Exception("货号{$goods_itemid}没有【已审核】或【已签收】的销售退货单");
                        $erp_bill_goods_data["is_settled"]=1;
                        $erp_bill_goods_model->editErpBillGoods(array('bill_no' => $bill_no,'goods_itemid'=>$goods_itemid),$erp_bill_goods_data);
                    }
                }
                //改变单据的结算状态
                $un_settle_list=$erp_bill_goods_model->getErpBillGoodsList(array('is_settled'=>0,'bill_no'=>$bill_no));
                if(count($un_settle_list)==0){
                    $bill_data=array();
                    $bill_data["settle_time"]=date('Y-m-d H:i:s');
                    $bill_data["is_settled"]=1;
                    $bill_data["settle_user"]=$_SESSION["seller_name"];
                    $bill_model->editErpBill(array('bill_no'=>$bill_no),$bill_data);
                }
                $bill_model->commit();
                $result['success'] = 1;
                $result['data'] = 1;
                exit(json_encode($result));
            }catch (Exception $ex){
                $result = callback(false,$ex->getMessage());
                $bill_model->rollback();
                exit(json_encode($result));
            }
        }
        Tpl::showpage('erp_bill_check_out');
    }
}
