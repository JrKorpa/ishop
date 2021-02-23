<?php
/**
 * 统计概述
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class statistics_sale_giftControl extends BaseSellerControl {

    public function __construct(){
        parent::__construct();
        Language::read('member_store_statistics');


    }
    /**
     * 商品列表
     */
    public function indexOp(){
        /*
         * 近30天
         */

        //$stat_time = strtotime(date('Y-m-d',time())) - 86400;
        //$stime = $stat_time - (86400*29);//30天前
       // $etime = $stat_time + 86400 - 1;//昨天23:59

        // import('function.statistics');
        $where = array();
        if(trim($_GET['style_sn'])){
            $where['style_sn'] = $_GET['style_sn'];
        }
        if(isset($_GET['start_time'])){
            $where['start_time'] = $_GET['start_time'];
        }else{
           // $where['start_time'] = date('Y-m-d',$stime);
        }
        if(isset($_GET['end_time'])){
            $where['end_time'] = $_GET['end_time'];
        }else{
           //$where['end_time'] = date('Y-m-d',$etime);
        }
        if(isset($_SESSION['store_id'])){
            $where['store_id'] = $_SESSION['store_id'];
        }
        $model = new orderModel();// Model('order');
        $goodslist = $model->get_gift_order_goods($where,5000,'order_goods.`order_id`,order_goods.`order_id`,order_goods.`goods_id`,order_goods.`style_sn`,order_goods.`goods_name`,order_goods.`zhiquan`,sum(order_goods.goods_num) as xuqiu','order_goods.`style_sn`,order_goods.`zhiquan`');

       // Tpl::output('start_time',date('Y-m-d',$stime));
       // Tpl::output('end_time',date('Y-m-d',$etime));


        Tpl::output('goodslist',$goodslist);
        Tpl::output('show_page',$model->showpage(2));

        //print_r($goodslist);



        Tpl::showpage('stat.sale.gift');
    }


}
