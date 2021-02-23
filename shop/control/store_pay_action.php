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
class store_pay_actionControl extends BaseSellerControl {
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }



    //支付记录
    public function indexOp(){
        $model_payment = Model('payment');
        $model_order = Model('order');
        $model_order_pay_action = Model('order_pay_action');
        $payment_list=$model_payment->getPaymentList(['payment_state'=>1]);

        $where = " orders.store_id =".$_SESSION['store_id'];


        if(trim($_GET['order_sn']) != ''){
            $where .=  " AND orders.order_sn ='". trim($_GET['order_sn']) ."'";
        }
        if($_GET['min_pay_date'] != ''){
            $where .= " AND order_pay_action.pay_date >= '". $_GET['min_pay_date'] ."'";
        }
        if($_GET['max_pay_date'] != ''){
            $where .= " AND order_pay_action.pay_date <= '".$_GET['max_pay_date']."'";
        }

        if($_GET['pay_code'] != ''){
            $where .= " AND order_pay_action.pay_code ='". $_GET['pay_code']."'";
        }
        if(trim($_GET['pay_sn']) != ''){
            $where .= " AND order_pay_action.pay_sn= '" .trim($_GET['pay_sn'])."'";
        }
        $order_pay_action_list =$model_order_pay_action->getOrderPayActionJoinList($where,'order_pay_action.*,orders.order_sn as o_order_sn,orders.buyer_name',10);
        Tpl::output('show_page',$model_order_pay_action->showpage());

        $countNum = $model_order_pay_action->gettotalnum();//获取总数量
        //总计
        $order_pay_action_sum = $model_order_pay_action->getOrderPayActionJoinList($where,'SUM(order_pay_action.deposit) as sum_pay_price',$countNum);
        if(!empty($order_pay_action_sum[0])){
            $sum_pay_price = $order_pay_action_sum[0]['sum_pay_price'];
        }else{
            $sum_pay_price = 0;
        }
        //导出Excel
        if (isset($_GET['exporttype']) && $_GET['exporttype'] == 'excel'){
            $statlist =$model_order_pay_action->getOrderPayActionJoinList($where,'order_pay_action.*,orders.order_sn as o_order_sn,orders.buyer_name',$countNum);
            //导出Excel
            import('libraries.excel');
            $excel_obj = new Excel();
            $excel_data = array();
            //设置样式
            $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
            //header
            //统计数据标题
            $statheader = array();
            $statheader[] = array('text'=>'订单号','key'=>'order_sn');
            $statheader[] = array('text'=>'收款金额','key'=>'deposit');
            $statheader[] = array('text'=>'收款方式','key'=>'pay_type');
            $statheader[] = array('text'=>'收款时间','key'=>'pay_date');
            $statheader[] = array('text'=>'收款人','key'=>'created_name');
            $statheader[] = array('text'=>'支付流水号','key'=>'pay_sn');
            $statheader[] = array('text'=>'客户姓名','key'=>'buyer_name');
            $statheader[] = array('text'=>'操作时间','key'=>'create_date');
            $statheader[] = array('text'=>'批注','key'=>'remark');
            foreach ($statheader as $k=>$v){
                $excel_data[0][] = array('styleid'=>'s_title','data'=>$v['text']);
            }
            //data
            foreach ($statlist as $k=>$v){
                foreach ($statheader as $h_k=>$h_v){
                    $excel_data[$k+1][] = array('data'=>$v[$h_v['key']]);
                }
            }
            $excel_data = $excel_obj->charset($excel_data,CHARSET);
            $excel_obj->addArray($excel_data);
            $excel_obj->addWorksheet($excel_obj->charset('订单付款记录',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('订单付款记录',CHARSET).date('Y-m-d-H',time()));
            exit();
        }


        Tpl::output('order_pay_action_list',$order_pay_action_list);
        Tpl::output('sum_pay_price',ncPriceFormat($sum_pay_price));

        Tpl::output('actionurl',"index.php?act=store_pay_action&op=index&order_sn=".trim($_GET['order_sn'])."&min_pay_date=".$_GET['min_pay_date']."&max_pay_date=".$_GET['max_pay_date']."&pay_code=".$_GET['pay_code']."&pay_sn=".trim($_GET['pay_sn']));
        Tpl::output('payment_list',$payment_list);
        Tpl::showpage('store_pay_action.index');
    }




}
