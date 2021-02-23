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

class statistics_lossControl extends BaseSellerControl {
    private $search_arr;//处理后的参数
    private $gc_arr;//分类数组
    private $choose_gcid;//选择的分类ID
    private $cs_fenlei =array(
        '-1'=>'其他',
        '1'=>'异业联盟',
        '2'=>'社区',
        '3'=>'珂兰相关',
        '4'=>'团购',
        '5'=>'老顾客',
        '6'=>'数据',
        '7'=>'网络来源',

    );

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
        //获得系统年份
        $year_arr = getSystemYearArr();
        //获得系统月份
        $month_arr = getSystemMonthArr();
        //获得本月的周时间段
        $week_arr = getMonthWeekArr($this->search_arr['week']['current_year'], $this->search_arr['week']['current_month']);
        Tpl::output('year_arr', $year_arr);
        Tpl::output('month_arr', $month_arr);
        Tpl::output('week_arr', $week_arr);
        Tpl::output('search_arr', $this->search_arr);


        //销售顾问
        $model_seller = Model('seller');
        $condition = array(
            'seller_store.store_id' => $_SESSION['store_id'],
            'seller.is_hidden' => 0,
            'seller_store.seller_group_id' => array('gt', 0)
        );
        $seller_list = $model_seller->getSellerStoreList($condition);
        Tpl::output('seller_list', $seller_list);
        //搜索表单提交地址
        Tpl::output('search_url', $this->search_arr['op']);

        Tpl::output('cs_fenlei', $this->cs_fenlei);



    }




    //损益表报
    public function lossOp(){

        $model = new statModel();
        $where = array();
        $where['orders.store_id'] = $_SESSION['store_id'];
        $where['orders.order_state'] = ORDER_STATE_SUCCESS;
        //销售出库单与销售退货单
        $where['erp_bill.item_type'] = 'LS';
        $where['erp_bill.bill_type|erp_bill.bill_type'] = array('S','D','_multi'=>1);


        if(trim($_GET['source_id']) != ''){
            $where['customer_sources.id'] = trim($_GET['source_id']);
        }else if(trim($_GET['fenlei']) != ''){
            $where['customer_sources.fenlei'] = trim($_GET['fenlei']);
        }

        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        if(isset($searchtime_arr[0]) && isset($searchtime_arr[1])){
            $check_time_arr[0] = date('Y-m-d H:i:s',$searchtime_arr[0]);
            $check_time_arr[1] = date('Y-m-d H:i:s',$searchtime_arr[1]);
            $where['erp_bill.check_time'] = array('between',$check_time_arr);
        }

        //毛利率
        $mao_lv_field = "ROUND(if(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)>0,(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0))/(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0)),0),4) AS sum_mao_lv";
       //毛利额
        $mao_amount_field = "IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0) as sum_mao_amount";

       //汇总
        $field = "IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0) as sum_sale_amount,IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0) as sum_refund_amount";
        $field .= ",".$mao_lv_field;
        $field .= ",".$mao_amount_field;
        $loss_sum_arr = $model->sataLossGoodsItem($where,$field);
        $statcount_arr = $loss_sum_arr[0];



        //各渠道毛利分析
        $field = "CASE customer_sources.fenlei WHEN - 1 THEN '其他' WHEN 1 THEN '异业联盟' WHEN 2 THEN '社区' WHEN 3 THEN '珂兰相关' WHEN 4 THEN '团购' WHEN 5 THEN '老顾客' WHEN 6 THEN '数据' WHEN 7 THEN 	'网络来源' END AS 'fenlei_name',IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0) as sum_sale_amount,IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0) as sum_refund_amount";
        $field .= ",".$mao_lv_field;
        $group = "customer_sources.fenlei";
        $loss_cs_sum_arr = $model->sataLossGoodsItem($where,$field,0,0,'',$group);
        $sale_cs_maolv =array();
        foreach ($loss_cs_sum_arr as $k=>$v){
            //各渠道毛利分析
            $sale_cs_maolv[$k]['p_name'] = empty($v['fenlei_name']) ? '客户来源': $v['fenlei_name'];
            $sale_cs_maolv[$k]['allnum'] = floatval(round($v['sum_mao_lv'],4)*100);
        }

        $data1 = array(
            'title'=>'各渠道毛利分析（%）',
            'name'=>'毛利率',
            'label_show'=>true,
            'series'=>$sale_cs_maolv
        );
        Tpl::output('stat_json1',getStatData_Pie1($data1));


        //各产品毛利分析
        $field = "if(goods_items.cat_type='','未知',goods_items.cat_type) AS cat_type,IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0) as sum_sale_amount,IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0) as sum_refund_amount";
        $field .= ",".$mao_lv_field;
        $group = "goods_items.cat_type";
        $loss_ct_sum_arr = $model->sataLossGoodsItem($where,$field,0,0,'',$group);

        //构造横轴数据
        foreach($loss_ct_sum_arr as $k => $v){
            //数据
            $stat_arr['series'][0]['data'][] = floatval(round($v['sum_mao_lv']*100,2));
            //横轴
            $stat_arr['xAxis']['categories'][] = strval($v['cat_type']);
        }

        //显示值,
        $stat_arr['series'][0]['dataLabels'] = array(
            'enabled'=>true,
            'format'=>'{point.y:.2f} %'
        );
        $stat_arr['series'][0]['tooltip'] = array(
            'valueSuffix'=>'%'   //单位
        );
        $caption = "各产品毛利分析（%）";
        //得到统计图数据
        $stat_arr['series'][0]['name'] = $caption;
        $stat_arr['title'] = $caption;
        $stat_arr['legend']['enabled'] = false;
        $stat_arr['yAxis']['title']['text'] = $caption;
        $stat_arr['yAxis']['title']['align'] = 'high';

        //echo getStatData_Column2D($stat_arr);exit;
        Tpl::output('stat_json2',getStatData_Column2D($stat_arr));


        Tpl::output('searchtime',implode('|',$searchtime_arr));
        Tpl::output('statcount_arr',$statcount_arr);
        self::profile_menu('loss');
        Tpl::showpage('stat.loss.loss');
    }

    /**
     * 客户来源损益汇总列表
     */
    public function losscslistOp(){
        $model = new statModel();
        $where = array();
        $where['orders.store_id'] = $_SESSION['store_id'];
        $where['orders.order_state'] = ORDER_STATE_SUCCESS;

        //销售出库单与销售退货单
        $where['erp_bill.item_type'] = 'LS';
        $where['erp_bill.bill_type|erp_bill.bill_type'] = array('S','D','_multi'=>1);


        if(trim($_GET['source_id']) != ''){
            $where['customer_sources.id'] = trim($_GET['source_id']);
        }else if(trim($_GET['fenlei']) != ''){
            $where['customer_sources.fenlei'] = trim($_GET['fenlei']);
        }
        $searchtime_arr_tmp = explode('|',$this->search_arr['t']);
        foreach ((array)$searchtime_arr_tmp as $k=>$v){
            $searchtime_arr[] = date('Y-m-d H:i:s',$v);
        }
        $where['erp_bill.check_time'] = array('between',$searchtime_arr);


        $field = "customer_sources.source_name,CASE customer_sources.fenlei WHEN - 1 THEN '其他' WHEN 1 THEN '异业联盟' WHEN 2 THEN '社区' WHEN 3 THEN '珂兰相关' WHEN 4 THEN '团购' WHEN 5 THEN '老顾客' WHEN 6 THEN '数据' WHEN 7 THEN 	'网络来源' END AS 'fenlei_name',IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0) as sum_sale_amount,IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0) as sum_refund_amount";
        $field .= ",SUM(IF(erp_bill_goods.bill_type='S',1 ,0)) as sum_sale_num";//销售数量
        //毛利额
        $field .=",IFNULL(SUM(IF(erp_bill.bill_type='S',ROUND(erp_bill_goods.sale_price-goods_items.jijiachengben,2) ,0)),0) - IFNULL(SUM(IF(erp_bill.bill_type='D',ROUND(erp_bill_goods.sale_price-goods_items.jijiachengben,2) ,0)),0) as sum_mao_amount";
        //毛利率
        $field .=",ROUND(if(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)>0,(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0))/(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0)),0),4) AS sum_mao_lv";
        $group = "orders.customer_source_id";

        $order_list = $model->sataLossGoodsItem($where, $field, 10, 0,'customer_sources.fenlei',$group);
        Tpl::output('show_page',$model->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $order_list = $model->sataLossGoodsItem($where, $field, 10000, 0,'customer_sources.fenlei',$group);
        }
        //统计数据标题
        $statlist = array();
        $statheader = array();
        $statheader[] = array('text'=>'来源分类','key'=>'fenlei_name');
        $statheader[] = array('text'=>'客户来源','key'=>'source_name');
        $statheader[] = array('text'=>'销售数量','key'=>'sum_sale_num');
        $statheader[] = array('text'=>'销售金额','key'=>'sum_sale_amount');
        $statheader[] = array('text'=>'退货金额','key'=>'sum_refund_amount');
        $statheader[] = array('text'=>'毛利额','key'=>'sum_mao_amount');
        $statheader[] = array('text'=>'毛利率','key'=>'sum_mao_lv');

        foreach ((array)$order_list as $k=>$v){
            $v['sum_mao_lv'] = $v['sum_mao_lv']*100 ."%";
            $statlist[$k]= $v;
        }


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
            $excel_obj->addWorksheet($excel_obj->charset('客户来源损益汇总',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('客户来源损益汇总',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);
        Tpl::output('tag','1');

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&t={$this->search_arr['t']}&source_id={$this->search_arr['source_id']}&fenlei={$this->search_arr['fenlei']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }


    /**
     * 客户来源损益汇总列表
     */
    public function lossctlistOp(){
        $model = new statModel();
        $where = array();
        $where['orders.store_id'] = $_SESSION['store_id'];
        $where['orders.order_state'] = ORDER_STATE_SUCCESS;

        //销售出库单与销售退货单
        $where['erp_bill.item_type'] = 'LS';
        $where['erp_bill.bill_type|erp_bill.bill_type'] = array('S','D','_multi'=>1);


        if(trim($_GET['source_id']) != ''){
            $where['customer_sources.id'] = trim($_GET['source_id']);
        }else if(trim($_GET['fenlei']) != ''){
            $where['customer_sources.fenlei'] = trim($_GET['fenlei']);
        }
        $searchtime_arr_tmp = explode('|',$this->search_arr['t']);
        foreach ((array)$searchtime_arr_tmp as $k=>$v){
            $searchtime_arr[] = date('Y-m-d H:i:s',$v);
        }
        $where['erp_bill.check_time'] = array('between',$searchtime_arr);


        $field = "if(goods_items.cat_type='','未知',goods_items.cat_type) AS cat_type,IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0) as sum_sale_amount,IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0) as sum_refund_amount";
        $field .= ",SUM(IF(erp_bill_goods.bill_type='S',1 ,0)) as sum_sale_num";//销售数量
        //毛利额
        $field .=",IFNULL(SUM(IF(erp_bill.bill_type='S',ROUND(erp_bill_goods.sale_price-goods_items.jijiachengben,2) ,0)),0) - IFNULL(SUM(IF(erp_bill.bill_type='D',ROUND(erp_bill_goods.sale_price-goods_items.jijiachengben,2) ,0)),0) as sum_mao_amount";
        //毛利率
        $field .=",ROUND(if(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)>0,(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price-goods_items.jijiachengben ,0)),0))/(IFNULL(SUM(IF(erp_bill.bill_type='S',erp_bill_goods.sale_price ,0)),0)-IFNULL(SUM(IF(erp_bill.bill_type='D',erp_bill_goods.sale_price ,0)),0)),0),4) AS sum_mao_lv";
        $group = "goods_items.cat_type";

        $order_list = $model->sataLossGoodsItem($where, $field, 0, 0,'customer_sources.fenlei',$group);
        if ($_GET['exporttype'] == 'excel'){
            $order_list = $model->sataLossGoodsItem($where, $field, 0, 0,'customer_sources.fenlei',$group);
        }
        //统计数据标题
        $statlist = array();
        $statheader = array();
        $statheader[] = array('text'=>'产品分类','key'=>'cat_type');
        $statheader[] = array('text'=>'销售数量','key'=>'sum_sale_num');
        $statheader[] = array('text'=>'销售金额','key'=>'sum_sale_amount');
        $statheader[] = array('text'=>'退货金额','key'=>'sum_refund_amount');
        $statheader[] = array('text'=>'毛利额','key'=>'sum_mao_amount');
        $statheader[] = array('text'=>'毛利率','key'=>'sum_mao_lv');
        foreach ((array)$order_list as $k=>$v){
            $v['sum_mao_lv'] = $v['sum_mao_lv']*100 ."%";
            $statlist[$k]= $v;
        }

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
            $excel_obj->addWorksheet($excel_obj->charset('各产品损益汇总',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('各产品损益汇总',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);
        Tpl::output('tag','2');

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&t={$this->search_arr['t']}&source_id={$this->search_arr['source_id']}&fenlei={$this->search_arr['fenlei']}");
        Tpl::showpage('stat.listandorder','null_layout');
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
