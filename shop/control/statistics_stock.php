<?php
/**
 * 用户中心店铺统计
 *
 *
 *
 * * @ (c) 2015-2018 kelan Inc. (http://官网)
 * @license    http://www.官网
 * @link       交流群号：官网群
 * @since      提供技术支持 授权请购买正版授权
 */



defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class statistics_stockControl extends BaseSellerControl {
    private $search_arr;//处理后的参数
    private $gc_arr;//分类数组
    private $choose_gcid;//选择的分类ID

    public function __construct() {
        parent::__construct();
        Language::read('member_store_statistics');
        import('function.statistics');
        import('function.datehelper');

        $model = Model('stat');
        //存储参数
        $this->search_arr = $_REQUEST;
        //处理搜索时间
        $this->search_arr = $model->dealwithSearchTime($this->search_arr);
    }



    /**
     * 库存统计
     */
    public function stockOp(){
        $model = new statModel();
        $statcount_arr = array(
            'sum_num'=>0,
            'sum_price'=>0,
            'waiting_sum_num'=>0,
            'waiting_sum_price'=>0,
            'sale_sum_num'=>0,
            'sale_sum_price'=>0,
        );
        //总库存
        $field = 'SUM(num) as sum_num, SUM(jijiachengben) as sum_price ';
        $stock_goods_sum = $model->get_stock_goods($field);
        if($stock_goods_sum[0]['sum_num']!=0){
            $statcount_arr['sum_num'] = $stock_goods_sum[0]['sum_num'];
            $statcount_arr['sum_price'] = ncPriceFormat($stock_goods_sum[0]['sum_price']);
        }




        //待取货品库存
        $field = 'SUM(num) as waiting_sum_num, SUM(jijiachengben) as waiting_sum_price ';
        $waiting_stock_goods_sum = $model->get_waiting_stock_goods($field);

        if($waiting_stock_goods_sum[0]['waiting_sum_num']!=0){
            $statcount_arr['waiting_sum_num'] = $waiting_stock_goods_sum[0]['waiting_sum_num'];
            $statcount_arr['waiting_sum_price'] = ncPriceFormat($waiting_stock_goods_sum[0]['waiting_sum_price']);
        }


        //可销售货品库存 = 总库存 - 待取货品库存
        $statcount_arr['sale_sum_num'] = $statcount_arr['sum_num'] - $statcount_arr['waiting_sum_num'];
        $statcount_arr['sale_sum_price'] = ncPriceFormat($statcount_arr['sum_price']-$statcount_arr['waiting_sum_price']);

       //已到货应付尾款
        $order_price_arr = $model->get_order_sum_price(2);
        $statcount_arr['order_sale_sum_price'] = ncPriceFormat($order_price_arr['sum_pay_price']);



        //总库存
        $_group = "if(product_type in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(cat_type='','未知',cat_type))";
        $field = "{$_group} as cat_type,SUM(num) as sum_num, SUM(jijiachengben) as sum_price ";
        $stocklist = $model->get_stock_goods($field, $_group);

        $stocklist_num = array();
        $stocklist_price = array();
        //处理数组数据
        if(!empty($stocklist)){
            foreach ($stocklist as $k => $v){
                //总库存数量
                $stocklist_num[$k]['p_name'] = empty($v['cat_type']) ? '库存数量': $v['cat_type'];
                $stocklist_num[$k]['allnum'] = intval($v['sum_num']);

                //总库存价格
                $stocklist_price[$k]['p_name'] = empty($v['cat_type']) ? '库存成本': $v['cat_type'];
                $stocklist_price[$k]['allnum'] = floatval($v['sum_price']);

            }
            $data1 = array(
                'title'=>'库存数量占比',
                'name'=>'库存数量',
                'label_show'=>true,
                'series'=>$stocklist_num
            );
            Tpl::output('stat_json1',getStatData_Pie($data1));
            $data2 = array(
                'title'=>'库存成本占比',
                'name'=>'库存成本',
                'label_show'=>true,
                'series'=>$stocklist_price
            );
            Tpl::output('stat_json2',getStatData_Pie($data2));
        }



        $data3 = array(
            'title'=>'库存状态占比',
            'name'=>'库存成本',
            'label_show'=>true,
            'series'=>array(
                0=>array(
                  'p_name' =>'可销售',
                  'allnum' =>floatval($statcount_arr['sale_sum_price'])
                ),
                1=>array(
                    'p_name' =>'待取',
                    'allnum' =>floatval($statcount_arr['waiting_sum_price'])
                )
            ),
        );
        Tpl::output('stat_json3',getStatData_Pie($data3));

        Tpl::output('statcount_arr',$statcount_arr);
        Tpl::output('active','stock');
        self::profile_menu('stock');
        Tpl::showpage('stat.stock.index');
    }

    /**
     * 库存明细列表
     */
    public function stocklistOp(){

        $model = new  statModel();
        //总库存
        $_group = "if(product_type in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(cat_type=''or cat_type='0','未知',cat_type))";
        $field = "{$_group} as cat_type,SUM(num) as sum_num, SUM(jijiachengben) as sum_price ";
        $cat_type_stock_list = $model->get_stock_goods($field, $_group);

        //待取库存
        $_group = "if(goods_items.product_type in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(goods_items.cat_type='' or goods_items.cat_type ='0','未知',goods_items.cat_type))";
       $field = " {$_group} as cat_type,SUM(goods_items.num) as waiting_sum_num, SUM(goods_items.jijiachengben) as waiting_sum_price ";
        $cat_type_waiting_stock_list = $model->get_waiting_stock_goods( $field, $_group );

        //总库存、待取库存、可销售库存 组装数组
        $cat_type_stock_list = array_column($cat_type_stock_list,null,'cat_type') ;
        $cat_type_waiting_stock_list = array_column($cat_type_waiting_stock_list,null,'cat_type') ;
        $cat_type_stock_arr = array_merge_recursive($cat_type_stock_list,$cat_type_waiting_stock_list);
        $sum_num = $sum_price = $waiting_sum_num = $waiting_sum_price = $sale_sum_num = $sale_sum_price = 0;
        foreach ($cat_type_stock_arr as $key=>$val){

            if(empty($val['waiting_sum_num'])){
                $val['waiting_sum_num'] = 0;
            }
            if(empty($val['waiting_sum_price'])){
                $val['waiting_sum_price'] = 0;
            }
            //明细可销售
            $val['sale_sum_num'] = $val['sum_num'] - $val['waiting_sum_num'];
            $val['sale_sum_price'] = $val['sum_price'] - $val['waiting_sum_price'];

            $val['sum_price'] = ncPriceFormat($val['sum_price']);
            $val['waiting_sum_price'] = ncPriceFormat($val['waiting_sum_price']);
            $val['sale_sum_price'] = ncPriceFormat($val['sale_sum_price']);


            $sum_num += $val['sum_num'];
            $sum_price += $val['sum_price'];
            $waiting_sum_num += $val['waiting_sum_num'];
            $waiting_sum_price += $val['waiting_sum_price'];
            $sale_sum_num += $val['sale_sum_num'];
            $sale_sum_price += $val['sale_sum_price'];

            $val['cat_type'] = $key;
            $cat_type_stock_arr[$key] = $val;
        }
        $cat_type_stock_arr = array_values($cat_type_stock_arr);
        $total_arr = array(
            'cat_type'=>"<b style='color:#000'>总计</b>",
            'sum_num'=>"<b style='color:#000'>".$sum_num."</b>",
            'sum_price'=>"<b style='color:#000'>".ncPriceFormat($sum_price)."</b>",
            'waiting_sum_num'=>"<b style='color:#000'>".$waiting_sum_num."</b>",
            'waiting_sum_price'=>"<b style='color:#000'>".ncPriceFormat($waiting_sum_price)."</b>",
            'sale_sum_num'=>"<b style='color:#000'>".$sale_sum_num."</b>",
            'sale_sum_price'=>"<b style='color:#000'>".ncPriceFormat($sale_sum_price)."</b>",
        );
        array_push($cat_type_stock_arr,$total_arr);
        $statlist = $cat_type_stock_arr;

        //统计数据标题
        $statheader = array();
        $statheader[] = array('text'=>'产品分类','key'=>'cat_type');
        $statheader[] = array('text'=>'库存数量','key'=>'sum_num');
        $statheader[] = array('text'=>'库存成本','key'=>'sum_price');
        $statheader[] = array('text'=>'可销售数量','key'=>'sale_sum_num');
        $statheader[] = array('text'=>'可销售成本','key'=>'sale_sum_price');
        $statheader[] = array('text'=>'待取数量','key'=>'waiting_sum_num');
        $statheader[] = array('text'=>'待取成本','key'=>'waiting_sum_price');


        //导出Excel
        if ($this->search_arr['exporttype'] == 'excel'){
            //导出Excel
            import('libraries.excel');
            $excel_obj = new Excel();
            $excel_data = array();
            //设置样式
            $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
            //header
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
            $excel_obj->addWorksheet($excel_obj->charset('库存明细记录',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('库存明细记录',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);
        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }


    //待取库存统计
    public function waiting_stockOp(){
        $model = new statModel();
        $statcount_arr = array();
        //待取订单金额
        $order_waiting_arr = $model->get_order_sum_price(1);
        $statcount_arr['sum_order_amount'] = ncPriceFormat($order_waiting_arr['sum_order_amount']);
        $statcount_arr['sum_shi_price'] = ncPriceFormat($order_waiting_arr['sum_shi_price']);
        $statcount_arr['sum_pay_price'] = ncPriceFormat($order_waiting_arr['sum_pay_price']);

        //待取订单已到货金额
        $on_order_waiting_arr = $model->get_order_sum_price(2);
        $statcount_arr['on_sum_order_amount'] = ncPriceFormat($on_order_waiting_arr['sum_order_amount']);
        $statcount_arr['on_sum_shi_price'] = ncPriceFormat($on_order_waiting_arr['sum_shi_price']);
        $statcount_arr['on_sum_pay_price'] = ncPriceFormat($on_order_waiting_arr['sum_pay_price']);

        //待取订单未到货金额
        $no_order_waiting_arr = $model->get_order_sum_price(3);
        $statcount_arr['no_sum_order_amount'] = ncPriceFormat($no_order_waiting_arr['sum_order_amount']);
        $statcount_arr['no_sum_shi_price'] = ncPriceFormat($no_order_waiting_arr['sum_shi_price']);
        $statcount_arr['no_sum_pay_price'] = ncPriceFormat($no_order_waiting_arr['sum_pay_price']);



        //待取库存
        $stocklist_price = array();
        $statcount_arr['waiting_sum_num'] = 0;
        $statcount_arr['waiting_sum_price'] = 0;
        $_group = "if(goods_items.product_type in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(goods_items.cat_type='','未知',goods_items.cat_type))";
        $field = "{$_group} as cat_type,SUM(goods_items.num) as waiting_sum_num, SUM(goods_items.jijiachengben) as waiting_sum_price ";
        $cat_type_waiting_stock_list = $model->get_waiting_stock_goods( $field, $_group );
        foreach ($cat_type_waiting_stock_list as $k=> $stock){
            //累加待取库存数量与金额
            $statcount_arr['waiting_sum_num'] += $stock['waiting_sum_num'];
            $statcount_arr['waiting_sum_price'] += ncPriceFormat($stock['waiting_sum_price']);

            //待取库存价格饼图数据
            $stocklist_price[$k]['p_name'] = empty($stock['cat_type']) ? '待取货品成本': $stock['cat_type'];
            $stocklist_price[$k]['allnum'] = floatval($stock['waiting_sum_price']);
        }
        $data2 = array(
            'title'=>'待取货品成本占比',
            'name'=>'待取货品成本',
            'label_show'=>true,
            'series'=>$stocklist_price
        );
        Tpl::output('stat_json2',getStatData_Pie($data2));


        $data1 = array(
            'title'=>'待取订单金额占比',
            'name'=>'待取订单金额',
            'label_show'=>true,
            'series'=>array(
                0=>array(
                    'p_name' =>'已到货',
                    'allnum' =>floatval($statcount_arr['on_sum_order_amount'])
                ),
                1=>array(
                    'p_name' =>'未到货',
                    'allnum' =>floatval($statcount_arr['no_sum_order_amount'])
                )
            ),
        );

       // print_r($data1);exit;
        Tpl::output('stat_json1',getStatData_Pie($data1));


        Tpl::output('statcount_arr',$statcount_arr);
        Tpl::output('active','waiting_stock');
        self::profile_menu('stock');
        Tpl::showpage('stat.stock.waiting');
    }

    /**
     * 订单列表
     */
    public function stock_waiting_listOp(){
        $model = new statModel();
        //统计数据标题
        $field = "goods_items.goods_id,goods_items.cat_type,goods_items.jijiachengben,(orders.order_amount - orders.rcb_amount + orders.refund_amount + IFNULL(orders.breach_amount,0)) as pay_amount,orders.order_sn,orders.buyer_name,orders.buyer_phone";
        $statlist = $model->get_waiting_stock_goods($field , '', 10);
        $show_page = $model->showpage(2);
        if ($_GET['exporttype'] == 'excel'){
            $page = $model->gettotalnum();
            $statlist = $model->get_waiting_stock_goods($field, '', $page);
        } else {

        }
        $statheader = array();
        $statheader[] = array('text'=>'货号','key'=>'goods_id');
        $statheader[] = array('text'=>'产品分类','key'=>'cat_type');
        $statheader[] = array('text'=>'库存成本','key'=>'jijiachengben');
        $statheader[] = array('text'=>'应收尾款','key'=>'pay_amount');
        $statheader[] = array('text'=>'订单号','key'=>'order_sn');
        $statheader[] = array('text'=>'客户姓名','key'=>'buyer_name');
        $statheader[] = array('text'=>'手机号码','key'=>'buyer_phone');


        //导出Excel
        if ($this->search_arr['exporttype'] == 'excel'){
            //导出Excel
            import('libraries.excel');
            $excel_obj = new Excel();
            $excel_data = array();
            //设置样式
            $excel_obj->setStyle(array('id'=>'s_title','Font'=>array('FontName'=>'宋体','Size'=>'12','Bold'=>'1')));
            //header
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
            $excel_obj->addWorksheet($excel_obj->charset('待取库存记录',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('待取库存记录',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);
        Tpl::output('show_page',$show_page);
        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }



    //可销售库存统计
    public function sale_stockOp(){
        $model = new statModel();
        $statcount_arr = array(
            'sale_sum_num'=>0,
            'sale_sum_price'=>0,
        );
        $stocklist_price = array();
        $stocklist_num = array();
        //总库存
        $_group = "if(product_type in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(cat_type='','未知',cat_type))";
        $field = "{$_group} as cat_type,SUM(num) as sum_num, SUM(jijiachengben) as sum_price ";
        $cat_type_stock_list = $model->get_stock_goods($field, $_group);

        //待取库存
        $_group = "if(goods_items.product_type in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(goods_items.cat_type='','未知',goods_items.cat_type))";
        $field = "{$_group} as cat_type,SUM(goods_items.num) as waiting_sum_num, SUM(goods_items.jijiachengben) as waiting_sum_price ";
        $cat_type_waiting_stock_list = $model->get_waiting_stock_goods( $field, $_group );

        //总库存、待取库存、可销售库存 组装数组
        $cat_type_stock_list = array_column($cat_type_stock_list,null,'cat_type') ;
        $cat_type_waiting_stock_list = array_column($cat_type_waiting_stock_list,null,'cat_type') ;
        $cat_type_stock_arr = array_merge_recursive($cat_type_stock_list,$cat_type_waiting_stock_list);
        foreach ($cat_type_stock_arr as $key=>$val){

            if(empty($val['waiting_sum_num'])){
                $val['waiting_sum_num'] = 0;
            }
            if(empty($val['waiting_sum_price'])){
                $val['waiting_sum_price'] = 0;
            }
            //明细可销售
            $sale_num = $val['sum_num'] - $val['waiting_sum_num'];
            $sale_price = $val['sum_price'] - $val['waiting_sum_price'];

            $statcount_arr['sale_sum_num'] += $sale_num;
            $statcount_arr['sale_sum_price'] += $sale_price;

            //可销售货品价格饼图数据
            $stocklist_price[$key]['p_name'] = empty($key) ? '可销售库存成本': $key;
            $stocklist_price[$key]['allnum'] = $sale_price;

            //可销售货品数量饼图数据
            $stocklist_num[$key]['p_name'] = empty($key) ? '可销售库存数量': $key;
            $stocklist_num[$key]['allnum'] = $sale_num;


        }
        $data1 = array(
            'title'=>'可销售库存数量占比',
            'name'=>'可销售库存数量',
            'label_show'=>true,
            'series'=>$stocklist_num
        );
        Tpl::output('stat_json1',getStatData_Pie($data1));
        $data2 = array(
            'title'=>'可销售库存成本占比',
            'name'=>'可销售库存成本',
            'label_show'=>true,
            'series'=>$stocklist_price
        );
        Tpl::output('stat_json2',getStatData_Pie($data2));


        Tpl::output('statcount_arr',$statcount_arr);
        Tpl::output('active','sale_stock');
        self::profile_menu('stock');
        Tpl::showpage('stat.stock.sale');
    }


    /**
     * 用户中心右边，小导航
     *
     * @param string    $menu_type  导航类型
     * @param string    $menu_key   当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key='') {
        $menu_array = array(
            1=>array('menu_key'=>'sale','menu_name'=>'销售分析','menu_url'=>'index.php?act=statistics_sale&op=sale'),
            //2=>array('menu_key'=>'member','menu_name'=>'会员分析','menu_url'=>'index.php?act=statistics_member&op=member'),
            3=>array('menu_key'=>'loss','menu_name'=>'损益分析','menu_url'=>'index.php?act=statistics_loss&op=loss'),
            4=>array('menu_key'=>'stock','menu_name'=>'库存分析','menu_url'=>'index.php?act=statistics_stock&op=stock'),
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }
}
