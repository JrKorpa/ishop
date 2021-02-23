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

class statistics_saleControl extends BaseSellerControl {
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


    private  function getWhere(){
        $where = array();
        $where['store_id'] = $_SESSION['store_id'];

        if(trim($_GET['order_state']) != ''){
            $where['order_state'] = trim($_GET['order_state']);
        }
        if(trim($_GET['is_zp']) != ''){
            $where['is_zp'] = trim($_GET['is_zp']);
        }
        if(trim($_GET['source_id']) != ''){
            $where['customer_source_id'] = trim($_GET['source_id']);
        }elseif($_GET['fenlei'] != ''){
            $sources_list = Model('stat')->getCustomerSourcesByIds(['fenlei'=>$_GET['fenlei']]);
            $sources_id_arr = array_values(array_column($sources_list,'id'));
            $where['customer_source_id'] = array('in',$sources_id_arr);
              /*
                $sources_list = $this->get_sources_list(['fenlei'=>trim($_GET['fenlei'])]);
                $sources_id_arr = array_values(array_column($sources_list,'id'));
                $where['customer_source_id'] = array('in',$sources_id_arr);
              */
       }


        if(trim($_GET['seller_id']) != ''){
            $where['seller_id'] = trim($_GET['seller_id']);
        }
        return $where;
    }
    /**
     * 销售统计
     */
    public function saleOp(){
        $model = Model('stat');
        //默认统计当前数据
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'day';
        }
        //计算昨天和今天时间
        if($this->search_arr['search_type'] == 'day'){
            $stime = $this->search_arr['day']['search_time'] - 86400;//昨天0点
            $etime = $this->search_arr['day']['search_time'] + 86400 - 1;//今天24点
            $curr_stime = $this->search_arr['day']['search_time'];//今天0点
        } elseif ($this->search_arr['search_type'] == 'week'){
            $current_weekarr = explode('|', $this->search_arr['week']['current_week']);
            $stime = strtotime($current_weekarr[0])-86400*7;
            $etime = strtotime($current_weekarr[1])+86400-1;
            $curr_stime = strtotime($current_weekarr[0]);//本周0点
        } elseif ($this->search_arr['search_type'] == 'month'){
            $stime = strtotime($this->search_arr['month']['current_year'].'-'.$this->search_arr['month']['current_month']."-01 -1 month");
            $etime = getMonthLastDay($this->search_arr['month']['current_year'],$this->search_arr['month']['current_month'])+86400-1;
            $curr_stime = strtotime($this->search_arr['month']['current_year'].'-'.$this->search_arr['month']['current_month']."-01");;//本月0点
        }

        $where = $this->getWhere();
        $where['payment_time'] = array('between',array($stime,$etime));

        //走势图
        $field = ' COUNT(*) as ordernum,SUM(order_amount + refund_amount + breach_amount) as orderamount ';
        $stat_arr = array();
        //$searchtime_arr = array($stime,$etime);
        if($this->search_arr['search_type'] == 'day'){
            //构造横轴数据
            for($i=0; $i<24; $i++){
                //统计图数据
                $curr_arr['orderamount'][$i] = 0;//今天
                $up_arr['orderamount'][$i] = 0;//昨天
                $curr_arr['ordernum'][$i] = 0;//今天
                $up_arr['ordernum'][$i] = 0;//昨天

                //统计表数据
                $currlist_arr[$i]['timetext'] = $i;

                $uplist_arr[$i]['val'] = 0;
                $currlist_arr[$i]['val'] = 0;
                //横轴
                $stat_arr['orderamount']['xAxis']['categories'][] = "$i";
                $stat_arr['ordernum']['xAxis']['categories'][] = "$i";
            }

            $today_day = @date('d', $etime);//今天日期
            $yesterday_day = @date('d', $stime);//昨天日期

            $field .= ' ,DAY(FROM_UNIXTIME(payment_time)) as dayval,HOUR(FROM_UNIXTIME(payment_time)) as hourval ';
            if (C('dbdriver') == 'mysqli') {
                $_group = 'dayval,hourval';
            } else {
                $_group = 'DAY(FROM_UNIXTIME(payment_time)),HOUR(FROM_UNIXTIME(payment_time))';
            }
            $orderlist = $model->statByStatorder($where, $field, 0, 0, '', $_group);
            foreach((array)$orderlist as $k => $v){
                if($today_day == $v['dayval']){
                    $curr_arr['ordernum'][$v['hourval']] = intval($v['ordernum']);
                    $curr_arr['orderamount'][$v['hourval']] = floatval($v['orderamount']);

                    $currlist_arr[$v['hourval']]['val'] = $v[$search_type];
                }
                if($yesterday_day == $v['dayval']){
                    $up_arr['ordernum'][$v['hourval']] = intval($v['ordernum']);
                    $up_arr['orderamount'][$v['hourval']] = floatval($v['orderamount']);

                    $uplist_arr[$v['hourval']]['val'] = $v[$search_type];
                }
            }
            $stat_arr['ordernum']['series'][0]['name'] = '昨天';
            $stat_arr['ordernum']['series'][0]['data'] = array_values($up_arr['ordernum']);
            $stat_arr['ordernum']['series'][1]['name'] = '今天';
            $stat_arr['ordernum']['series'][1]['data'] = array_values($curr_arr['ordernum']);

            $stat_arr['orderamount']['series'][0]['name'] = '昨天';
            $stat_arr['orderamount']['series'][0]['data'] = array_values($up_arr['orderamount']);
            $stat_arr['orderamount']['series'][1]['name'] = '今天';
            $stat_arr['orderamount']['series'][1]['data'] = array_values($curr_arr['orderamount']);
        }

        if($this->search_arr['search_type'] == 'week'){
            $up_week = @date('W', $stime);//上周
            $curr_week = @date('W', $etime);//本周
            //构造横轴数据
            for($i=1; $i<=7; $i++){
                $tmp_weekarr = getSystemWeekArr();
                //统计图数据
                $up_arr['ordernum'][$i] = 0;
                $curr_arr['ordernum'][$i] = 0;

                $up_arr['orderamount'][$i] = 0;
                $curr_arr['orderamount'][$i] = 0;

                //横轴
                $stat_arr['ordernum']['xAxis']['categories'][] = $tmp_weekarr[$i];
                $stat_arr['orderamount']['xAxis']['categories'][] = $tmp_weekarr[$i];

                //统计表数据
                $uplist_arr[$i]['timetext'] = $tmp_weekarr[$i];
                $currlist_arr[$i]['timetext'] = $tmp_weekarr[$i];
                $uplist_arr[$i]['val'] = 0;
                $currlist_arr[$i]['val'] = 0;
                unset($tmp_weekarr);
            }
            $field .= ',WEEKOFYEAR(FROM_UNIXTIME(payment_time)) as weekval,WEEKDAY(FROM_UNIXTIME(payment_time))+1 as dayofweekval ';
            if (C('dbdriver') == 'mysqli') {
                $_group = 'weekval,dayofweekval';
            } else {
                $_group = 'WEEKOFYEAR(FROM_UNIXTIME(payment_time)),WEEKDAY(FROM_UNIXTIME(payment_time))+1';
            }
            $orderlist = $model->statByStatorder($where, $field, 0, 0, '', $_group);
            foreach((array)$orderlist as $k=>$v){
                if ($up_week == $v['weekval']){
                    $up_arr['ordernum'][$v['dayofweekval']] = intval($v['ordernum']);
                    $up_arr['orderamount'][$v['dayofweekval']] = intval($v['orderamount']);

                    $uplist_arr[$v['dayofweekval']]['val'] = intval($v[$search_type]);
                }
                if ($curr_week == $v['weekval']){
                    $curr_arr['ordernum'][$v['dayofweekval']] = intval($v['ordernum']);
                    $curr_arr['orderamount'][$v['dayofweekval']] = intval($v['orderamount']);

                    $currlist_arr[$v['dayofweekval']]['val'] = intval($v[$search_type]);
                }
            }
            $stat_arr['ordernum']['series'][0]['name'] = '上周';
            $stat_arr['ordernum']['series'][0]['data'] = array_values($up_arr['ordernum']);
            $stat_arr['ordernum']['series'][1]['name'] = '本周';
            $stat_arr['ordernum']['series'][1]['data'] = array_values($curr_arr['ordernum']);

            $stat_arr['orderamount']['series'][0]['name'] = '上周';
            $stat_arr['orderamount']['series'][0]['data'] = array_values($up_arr['orderamount']);
            $stat_arr['orderamount']['series'][1]['name'] = '本周';
            $stat_arr['orderamount']['series'][1]['data'] = array_values($curr_arr['orderamount']);
        }

        if($this->search_arr['search_type'] == 'month'){
            $up_month = date('m',$stime);
            $curr_month = date('m',$etime);
            //计算横轴的最大量（由于每个月的天数不同）
            $up_dayofmonth = date('t',$stime);
            $curr_dayofmonth = date('t',$etime);
            $x_max = $up_dayofmonth > $curr_dayofmonth ? $up_dayofmonth : $curr_dayofmonth;

            //构造横轴数据
            for($i=1; $i<=$x_max; $i++){
                //统计图数据
                $up_arr['ordernum'][$i] = 0;
                $curr_arr['ordernum'][$i] = 0;

                $up_arr['orderamount'][$i] = 0;
                $curr_arr['orderamount'][$i] = 0;

                //横轴
                $stat_arr['ordernum']['xAxis']['categories'][] = $i;
                $stat_arr['orderamount']['xAxis']['categories'][] = $i;

                //统计表数据
                $currlist_arr[$i]['timetext'] = $i;
                $uplist_arr[$i]['val'] = 0;
                $currlist_arr[$i]['val'] = 0;
            }
            $field .= ',MONTH(FROM_UNIXTIME(payment_time)) as monthval,day(FROM_UNIXTIME(payment_time)) as dayval ';
			if (C('dbdriver') == 'mysqli') {
				$_group = 'monthval,dayval';
			} else {
				$_group = 'MONTH(FROM_UNIXTIME(payment_time)),DAY(FROM_UNIXTIME(payment_time))';
			}
            $orderlist = $model->statByStatorder($where, $field, 0, 0, '', $_group);
            foreach($orderlist as $k=>$v){
                if ($up_month == $v['monthval']){
                    $up_arr['ordernum'][$v['dayval']] = intval($v['ordernum']);
                    $up_arr['orderamount'][$v['dayval']] = floatval($v['orderamount']);

                    $uplist_arr[$v['dayval']]['val'] = intval($v[$search_type]);
                }
                if ($curr_month == $v['monthval']){
                    $curr_arr['ordernum'][$v['dayval']] = intval($v['ordernum']);
                    $curr_arr['orderamount'][$v['dayval']] = intval($v['orderamount']);

                    $currlist_arr[$v['dayval']]['val'] = intval($v[$search_type]);
                }
            }
            $stat_arr['ordernum']['series'][0]['name'] = '上月';
            $stat_arr['ordernum']['series'][0]['data'] = array_values($up_arr['ordernum']);
            $stat_arr['ordernum']['series'][1]['name'] = '本月';
            $stat_arr['ordernum']['series'][1]['data'] = array_values($curr_arr['ordernum']);

            $stat_arr['orderamount']['series'][0]['name'] = '上月';
            $stat_arr['orderamount']['series'][0]['data'] = array_values($up_arr['orderamount']);
            $stat_arr['orderamount']['series'][1]['name'] = '本月';
            $stat_arr['orderamount']['series'][1]['data'] = array_values($curr_arr['orderamount']);
        }

        $stat_arr['ordernum']['title'] = '订单量统计';
        $stat_arr['ordernum']['yAxis'] = '订单量';

        $stat_arr['orderamount']['title'] = '下单金额统计';
        $stat_arr['orderamount']['yAxis'] = '下单金额';

        $stat_json['ordernum'] = getStatData_LineLabels($stat_arr['ordernum']);
        $stat_json['orderamount'] = getStatData_LineLabels($stat_arr['orderamount']);
        Tpl::output('stat_json',$stat_json);
        Tpl::output('stattype',$search_type);
        //总数统计
        $where['payment_time'] = array('between',array($curr_stime,$etime));
        $statcount_arr = $model->getoneByStatorder($where,' COUNT(*) as ordernum, SUM(order_amount + refund_amount + breach_amount) as orderamount');
        $statcount_arr['ordernum'] = ($t = intval($statcount_arr['ordernum'])) > 0?$t:0;
        $statcount_arr['orderamount'] = ncPriceFormat(($t = floatval($statcount_arr['orderamount'])) > 0?$t:0);
        Tpl::output('statcount_arr',$statcount_arr);
        Tpl::output('searchtime',implode('|',array($curr_stime,$etime)));
        Tpl::output('show_page',$model->showpage(2));
        self::profile_menu('sale');
        Tpl::showpage('stat.sale.index');
    }

    /**
     * 订单列表
     */
    public function salelistOp(){
        $model = new statModel();
        $where = array();
        $where['stat_order.store_id'] = $_SESSION['store_id'];
        if(trim($_GET['order_state']) != ''){
            $where['stat_order.order_state'] = trim($_GET['order_state']);
        }
        if(trim($_GET['is_zp']) != ''){
            $where['stat_order.is_zp'] = trim($_GET['is_zp']);
        }
        if(trim($_GET['source_id']) != ''){
            $where['stat_order.customer_source_id'] = trim($_GET['source_id']);
        }elseif($_GET['fenlei'] != ''){
            $sources_list = Model('stat')->getCustomerSourcesByIds(['fenlei'=>$_GET['fenlei']]);
            $sources_id_arr = array_values(array_column($sources_list,'id'));
            $where['stat_order.customer_source_id'] = array('in',$sources_id_arr);
        }
        if(trim($_GET['seller_id']) != ''){
            $where['stat_order.seller_id'] = trim($_GET['seller_id']);
        }
        $searchtime_arr_tmp = explode('|',$this->search_arr['t']);
        foreach ((array)$searchtime_arr_tmp as $k=>$v){
            $searchtime_arr[] = intval($v);
        }
        $where['payment_time'] = array('between',$searchtime_arr);
        $field = "stat_order.order_sn,customer_sources.source_name,stat_order.seller_name,stat_ordergoods.goods_name,
                stat_ordergoods.rec_id,stat_ordergoods.style_sn,stat_ordergoods.goods_price,stat_ordergoods.goods_pay_price,stat_ordergoods.tuo_type,
                stat_ordergoods.is_xianhuo,stat_ordergoods.goods_type,stat_ordergoods.caizhi,stat_ordergoods.jinzhong,
                stat_ordergoods.cert_id,stat_ordergoods.cert_type,stat_ordergoods.carat,stat_ordergoods.bc_status,
                base_style_info.xilie,stat_order.order_amount,stat_order.rcb_amount,stat_order.goods_amount,stat_order.refund_amount,stat_order.breach_amount,
                (select count(*) from stat_ordergoods as so where so.order_id = stat_order.order_id) as goods_num,
                stat_order.order_state,stat_order.pay_status,stat_order.payment_time,stat_order.delay_time,stat_order.remark";
        $order_list = $model->getStatOrderGoodsItem($where, $field, 10, 0,'stat_order.payment_time desc,rec_id desc');
        Tpl::output('show_page',$model->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $page = $model->gettotalnum();
            $order_list = $model->getStatOrderGoodsItem($where, $field, $page, 0,'stat_order.payment_time desc,rec_id desc');
        }

        //获取本公司客户来源
        $sourcelist = $this->get_sources_list();
        $source_list = array();
        if(!empty($sourcelist)){
            foreach ($sourcelist as $key => $value) {
                $source_list[$value['id']] = $value['source_name'];
            }
        }

        //统计数据标题
        $statlist = array();
        $statheader = array();
        $statheader[] = array('text'=>'订单编号','key'=>'order_sn');
        $statheader[] = array('text'=>'来源','key'=>'source_name');
        $statheader[] = array('text'=>'销售顾问','key'=>'seller_name');

        $statheader[] = array('text'=>'货品名称','key'=>'goods_name');
        $statheader[] = array('text'=>'款号','key'=>'style_sn');
        $statheader[] = array('text'=>'原价','key'=>'goods_price');
        $statheader[] = array('text'=>'成交价','key'=>'goods_pay_price');
        $statheader[] = array('text'=>'金托类型','key'=>'tuo_type');
        $statheader[] = array('text'=>'期货\现货','key'=>'is_xianhuo');
        $statheader[] = array('text'=>'是否赠品','key'=>'is_zp');


        $statheader[] = array('text'=>'订单总金额','key'=>'order_amount');
        $statheader[] = array('text'=>'已付金额','key'=>'rcb_amount');
        $statheader[] = array('text'=>'未付金额','key'=>'pay_amount');
        $statheader[] = array('text'=>'订单商品总金额','key'=>'goods_amount');
        $statheader[] = array('text'=>'订单商品数量','key'=>'goods_num');
        $statheader[] = array('text'=>'订单状态','key'=>'order_statetext');
        $statheader[] = array('text'=>'付款状态','key'=>'pay_status');
        $statheader[] = array('text'=>'第一次点款时间','key'=>'payment_time');
        $statheader[] = array('text'=>'发货时间','key'=>'delay_time');
        $statheader[] = array('text'=>'订单备注','key'=>'remark');

        $statheader[] = array('text'=>'材质','key'=>'caizhi');
        $statheader[] = array('text'=>'金重','key'=>'jinzhong');
        $statheader[] = array('text'=>'系列','key'=>'xilie');
        $statheader[] = array('text'=>'证书号','key'=>'cert_id');
        $statheader[] = array('text'=>'证书类型','key'=>'cert_type');
        $statheader[] = array('text'=>'钻石大小','key'=>'carat');
        $statheader[] = array('text'=>'布产状态','key'=>'bc_status');
        $statheader[] = array('text'=>'绑定货号','key'=>'goods_id');
        $statheader[] = array('text'=>'款式分类','key'=>'cat_type');
        $statheader[] = array('text'=>'产品线','key'=>'product_type');
        $statheader[] = array('text'=>'成本','key'=>'jijiachengben');
        $statheader[] = array('text'=>'货品所在仓库','key'=>'warehouse');


        //系列
        $appStypeXilieModel = new app_style_xilieModel();
        $xilie_list = $appStypeXilieModel->getXiLieList(array('statue'=>1));


        $bc_arr =array(1=>'初始化',2=>'待分配',3=>'已分配',4=>'生产中',5=>'质检中',6=>'质检完成',7=>'部分出厂',8=>'作废',9=>'已出厂',10=>'已取消',11=>'不需布产',12=>'其他');
        $pay_status_arr = array('1'=>'待付款','2'=>'部分付款','3'=>'已付款');
        foreach ((array)$order_list as $k=>$v){
            $where = array('order_detail_id'=>$v['rec_id']);
            $field = 'goods_id,product_type,cat_type,jijiachengben,warehouse';
            $goods_item_arr = $model->statGoodsItem($where,$field);
            if(!empty($goods_item_arr)){
                $v['goods_id'] = join('/',array_values(array_column($goods_item_arr,'goods_id')));
                $v['cat_type'] = join('/',array_values(array_column($goods_item_arr,'cat_type')));
                $v['product_type'] = join('/',array_values(array_column($goods_item_arr,'product_type')));
                $v['warehouse'] = $goods_item_arr[0]['warehouse'];
                $v['jijiachengben'] = join('/',array_values(array_column($goods_item_arr,'jijiachengben')));
            }



            $v['order_sn'] = "'".$v['order_sn'];
            $v['payment_time'] = @date('Y-m-d H:i:s',$v['payment_time']);
            $v['delay_time'] = empty($v['delay_time']) ? '' :@date('Y-m-d H:i:s',$v['delay_time']);
            $v['bc_status'] = $bc_arr[$v['bc_status']];
            switch ($v['order_state']){
                case ORDER_STATE_CANCEL:
                    $v['order_statetext'] = '已取消';
                    break;
                case ORDER_STATE_NEW:
                    $v['order_statetext'] = '待付款';
                    break;
                case ORDER_STATE_TO_BC:
                    $v['order_statetext'] = '待布产';
                    break;
                case ORDER_STATE_MAKING:
                    $v['order_statetext'] = '生产中';
                    break;
                case ORDER_STATE_NEW:
                    $v['order_statetext'] = '待付款';
                    break;
                case ORDER_STATE_TOSEND:
                    $v['order_statetext'] = '待发货';
                    break;
                case ORDER_STATE_TO_SIGN:
                    $v['order_statetext'] = '待签收';
                    break;
                case ORDER_STATE_SUCCESS:
                    $v['order_statetext'] = '交易完成';
                    break;
            }

            $v['pay_status'] = $pay_status_arr[$v['pay_status']];


            $v['pay_amount'] = $v['order_amount'] - ($v['rcb_amount'] - $v['refund_amount']) + $v['breach_amount'];
            $v['is_xianhuo'] = $v['is_xianhuo'] == 1 ? '现货':'期货';
            $v['is_zp'] = $v['goods_type'] == 5 ? '是':'否';
            $v['tuo_type'] = $v['tuo_type'] == 1 ? '成品':'空托';

            //系列
            if(!empty(trim($v['xilie'],','))){
                $xilie =  trim($v['xilie'],',');
                $xilie_arr = explode(',',$xilie);
                $xilie_str = '';
                foreach ($xilie_list as $v){
                    if(in_array($v['id'],$xilie_arr)){
                        $xilie_str .= $v['name'].' ';
                    }
                }
                $v['xilie'] = $xilie_str;

            }


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
            $excel_obj->addWorksheet($excel_obj->charset('新增订单记录',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('新增订单记录',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('title','新增订单明细');
        Tpl::output('tag','1');
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&order_state={$this->search_arr['order_state']}&t={$this->search_arr['t']}&is_zp={$this->search_arr['is_zp']}&source_id={$this->search_arr['source_id']}&fenlei={$this->search_arr['fenlei']}&seller_id={$this->search_arr['seller_id']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }

    /**
     * 发货订单明细列表
     */
    public function send_salelistOp(){
        $model = new statModel();
        $where = array();
        $where['stat_order.store_id'] = $_SESSION['store_id'];
        if(trim($_GET['order_state']) != ''){
            $where['stat_order.order_state'] = trim($_GET['order_state']);
        }
        if(trim($_GET['is_zp']) != ''){
            $where['stat_order.is_zp'] = trim($_GET['is_zp']);
        }
        if(trim($_GET['source_id']) != ''){
            $where['stat_order.customer_source_id'] = trim($_GET['source_id']);
        }elseif($_GET['fenlei'] != ''){
            $sources_list = Model('stat')->getCustomerSourcesByIds(['fenlei'=>$_GET['fenlei']]);
            $sources_id_arr = array_values(array_column($sources_list,'id'));
            $where['stat_order.customer_source_id'] = array('in',$sources_id_arr);
        }
        if(trim($_GET['seller_id']) != ''){
            $where['stat_order.seller_id'] = trim($_GET['seller_id']);
        }
        $searchtime_arr_tmp = explode('|',$this->search_arr['t']);
        foreach ((array)$searchtime_arr_tmp as $k=>$v){
            $searchtime_arr[] = intval($v);
        }
        $where['delay_time'] = array('between',$searchtime_arr);
        $field = "stat_order.order_sn,customer_sources.source_name,stat_order.seller_name,stat_ordergoods.goods_name,
                stat_ordergoods.rec_id,stat_ordergoods.style_sn,stat_ordergoods.goods_price,stat_ordergoods.goods_pay_price,stat_ordergoods.tuo_type,
                stat_ordergoods.is_xianhuo,stat_ordergoods.goods_type,stat_ordergoods.caizhi,stat_ordergoods.jinzhong,
                stat_ordergoods.cert_id,stat_ordergoods.cert_type,stat_ordergoods.carat,stat_ordergoods.bc_status,
                base_style_info.xilie,stat_order.order_amount,stat_order.rcb_amount,stat_order.goods_amount,stat_order.refund_amount,stat_order.breach_amount,
                (select count(*) from stat_ordergoods as so where so.order_id = stat_order.order_id) as goods_num,
                stat_order.order_state,stat_order.pay_status,stat_order.payment_time,stat_order.delay_time,stat_order.remark";
        $order_list = $model->getStatOrderGoodsItem($where, $field, 10, 0,'stat_order.payment_time desc,rec_id desc');
        Tpl::output('show_page',$model->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $page = $model->gettotalnum();
            $order_list = $model->getStatOrderGoodsItem($where, $field, $page, 0,'stat_order.payment_time desc,rec_id desc');
        }

        //获取本公司客户来源
        $sourcelist = $this->get_sources_list();
        $source_list = array();
        if(!empty($sourcelist)){
            foreach ($sourcelist as $key => $value) {
                $source_list[$value['id']] = $value['source_name'];
            }
        }

        //统计数据标题
        $statlist = array();
        $statheader = array();
        $statheader[] = array('text'=>'订单编号','key'=>'order_sn');
        $statheader[] = array('text'=>'来源','key'=>'source_name');
        $statheader[] = array('text'=>'销售顾问','key'=>'seller_name');

        $statheader[] = array('text'=>'货品名称','key'=>'goods_name');
        $statheader[] = array('text'=>'款号','key'=>'style_sn');
        $statheader[] = array('text'=>'原价','key'=>'goods_price');
        $statheader[] = array('text'=>'成交价','key'=>'goods_pay_price');
        $statheader[] = array('text'=>'金托类型','key'=>'tuo_type');
        $statheader[] = array('text'=>'期货\现货','key'=>'is_xianhuo');
        $statheader[] = array('text'=>'是否赠品','key'=>'is_zp');


        $statheader[] = array('text'=>'订单总金额','key'=>'order_amount');
        $statheader[] = array('text'=>'已付金额','key'=>'rcb_amount');
        $statheader[] = array('text'=>'未付金额','key'=>'pay_amount');
        $statheader[] = array('text'=>'订单商品总金额','key'=>'goods_amount');
        $statheader[] = array('text'=>'订单商品数量','key'=>'goods_num');
        $statheader[] = array('text'=>'订单状态','key'=>'order_statetext');
        $statheader[] = array('text'=>'付款状态','key'=>'pay_status');
        $statheader[] = array('text'=>'第一次点款时间','key'=>'payment_time');
        $statheader[] = array('text'=>'发货时间','key'=>'delay_time');
        $statheader[] = array('text'=>'订单备注','key'=>'remark');

        $statheader[] = array('text'=>'材质','key'=>'caizhi');
        $statheader[] = array('text'=>'金重','key'=>'jinzhong');
        $statheader[] = array('text'=>'系列','key'=>'xilie');
        $statheader[] = array('text'=>'证书号','key'=>'cert_id');
        $statheader[] = array('text'=>'证书类型','key'=>'cert_type');
        $statheader[] = array('text'=>'钻石大小','key'=>'carat');
        $statheader[] = array('text'=>'布产状态','key'=>'bc_status');
        $statheader[] = array('text'=>'款式分类','key'=>'cat_type');
        $statheader[] = array('text'=>'产品线','key'=>'product_type');
        $statheader[] = array('text'=>'绑定货号','key'=>'goods_id');
        $statheader[] = array('text'=>'成本','key'=>'jijiachengben');
        $statheader[] = array('text'=>'货品所在仓库','key'=>'warehouse');


        //系列
        $appStypeXilieModel = new app_style_xilieModel();
        $xilie_list = $appStypeXilieModel->getXiLieList(array('statue'=>1));


        $bc_arr =array(1=>'初始化',2=>'待分配',3=>'已分配',4=>'生产中',5=>'质检中',6=>'质检完成',7=>'部分出厂',8=>'作废',9=>'已出厂',10=>'已取消',11=>'不需布产',12=>'其他');
        $pay_status_arr = array('1'=>'待付款','2'=>'部分付款','3'=>'已付款');
        foreach ((array)$order_list as $k=>$v){
            $where = array('order_detail_id'=>$v['rec_id']);
            $field = 'goods_id,product_type,cat_type,jijiachengben,warehouse';
            $goods_item_arr = $model->statGoodsItem($where,$field);
            if(!empty($goods_item_arr)){
                $v['goods_id'] = join('/',array_values(array_column($goods_item_arr,'goods_id')));
                $v['cat_type'] = join('/',array_values(array_column($goods_item_arr,'cat_type')));
                $v['product_type'] = join('/',array_values(array_column($goods_item_arr,'product_type')));
                $v['warehouse'] = $goods_item_arr[0]['warehouse'];
                $v['jijiachengben'] = join('/',array_values(array_column($goods_item_arr,'jijiachengben')));
            }


            $v['order_sn'] = "'".$v['order_sn'];
            $v['payment_time'] = @date('Y-m-d H:i:s',$v['payment_time']);
            $v['delay_time'] = empty($v['delay_time']) ? '' :@date('Y-m-d H:i:s',$v['delay_time']);
            $v['bc_status'] = $bc_arr[$v['bc_status']];
            switch ($v['order_state']){
                case ORDER_STATE_CANCEL:
                    $v['order_statetext'] = '已取消';
                    break;
                case ORDER_STATE_NEW:
                    $v['order_statetext'] = '待付款';
                    break;
                case ORDER_STATE_TO_BC:
                    $v['order_statetext'] = '待布产';
                    break;
                case ORDER_STATE_MAKING:
                    $v['order_statetext'] = '生产中';
                    break;
                case ORDER_STATE_NEW:
                    $v['order_statetext'] = '待付款';
                    break;
                case ORDER_STATE_TOSEND:
                    $v['order_statetext'] = '待发货';
                    break;
                case ORDER_STATE_TO_SIGN:
                    $v['order_statetext'] = '待签收';
                    break;
                case ORDER_STATE_SUCCESS:
                    $v['order_statetext'] = '交易完成';
                    break;
            }


            $v['pay_status'] = $pay_status_arr[$v['pay_status']];


            $v['pay_amount'] = $v['order_amount'] - ($v['rcb_amount'] - $v['refund_amount']) + $v['breach_amount'];
            $v['is_xianhuo'] = $v['is_xianhuo'] == 1 ? '现货':'期货';
            $v['is_zp'] = $v['goods_type'] == 5 ? '是':'否';
            $v['tuo_type'] = $v['tuo_type'] == 1 ? '成品':'空托';

            //系列
            if(!empty(trim($v['xilie'],','))){
                $xilie =  trim($v['xilie'],',');
                $xilie_arr = explode(',',$xilie);
                $xilie_str = '';
                foreach ($xilie_list as $v){
                    if(in_array($v['id'],$xilie_arr)){
                        $xilie_str .= $v['name'].' ';
                    }
                }
                $v['xilie'] = $xilie_str;

            }


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
            $excel_obj->addWorksheet($excel_obj->charset('发货订单记录',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('发货订单记录',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('title','发货订单明细');
        Tpl::output('tag','2');
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&order_state={$this->search_arr['order_state']}&t={$this->search_arr['t']}&is_zp={$this->search_arr['is_zp']}&source_id={$this->search_arr['source_id']}&fenlei={$this->search_arr['fenlei']}&seller_id={$this->search_arr['seller_id']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }



    /**
     * 退货订单明细列表
     */
    public function refund_salelistOp(){
        $model = new statModel();
        $where = array();
        $where['stat_order.store_id'] = $_SESSION['store_id'];
        if(trim($_GET['order_state']) != ''){
            $where['stat_order.order_state'] = trim($_GET['order_state']);
        }
        if(trim($_GET['is_zp']) != ''){
            $where['stat_order.is_zp'] = trim($_GET['is_zp']);
        }
        if(trim($_GET['source_id']) != ''){
            $where['stat_order.customer_source_id'] = trim($_GET['source_id']);
        }elseif($_GET['fenlei'] != ''){
            $sources_list = Model('stat')->getCustomerSourcesByIds(['fenlei'=>$_GET['fenlei']]);
            $sources_id_arr = array_values(array_column($sources_list,'id'));
            $where['stat_order.customer_source_id'] = array('in',$sources_id_arr);
        }
        if(trim($_GET['seller_id']) != ''){
            $where['stat_order.seller_id'] = trim($_GET['seller_id']);
        }
        $searchtime_arr_tmp = explode('|',$this->search_arr['t']);
        foreach ((array)$searchtime_arr_tmp as $k=>$v){
            $searchtime_arr[] = intval($v);
        }
        $where['refund_return.seller_time'] = array('between',$searchtime_arr);
        $field = "stat_order.order_sn,customer_sources.source_name,stat_order.seller_name,stat_ordergoods.goods_name,
                stat_ordergoods.rec_id,stat_ordergoods.goods_id,stat_ordergoods.style_sn,stat_ordergoods.goods_price,stat_ordergoods.goods_pay_price,stat_ordergoods.tuo_type,
                stat_ordergoods.is_xianhuo,stat_ordergoods.goods_type,stat_ordergoods.caizhi,stat_ordergoods.jinzhong,
                stat_ordergoods.cert_id,stat_ordergoods.cert_type,stat_ordergoods.carat,stat_ordergoods.bc_status,
                base_style_info.xilie,stat_order.order_amount,stat_order.rcb_amount,stat_order.goods_amount,stat_order.refund_amount,stat_order.breach_amount,
                (select count(*) from stat_ordergoods as so where so.order_id = stat_order.order_id) as goods_num,
                stat_order.order_state,stat_order.pay_status,stat_order.payment_time,stat_order.delay_time,stat_order.remark,
                refund_return.buyer_name,refund_return.refund_amount as r_refund_amount,refund_return.breach_amount as r_breach_amount,refund_return.return_type,
                refund_return.return_form,refund_return.buyer_message,refund_return.seller_time";
        $order_list = $model->getStatRefundOrderGoodsItem($where, $field, 10, 0,'refund_return.seller_time desc,refund_return.refund_id desc');
        Tpl::output('show_page',$model->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $page = $model->gettotalnum();
            $order_list = $model->getStatRefundOrderGoodsItem($where, $field, $page, 0,'refund_return.seller_time desc,refund_return.refund_id desc');
        }

        //获取本公司客户来源
        $sourcelist = $this->get_sources_list();
        $source_list = array();
        if(!empty($sourcelist)){
            foreach ($sourcelist as $key => $value) {
                $source_list[$value['id']] = $value['source_name'];
            }
        }

        //统计数据标题
        $statlist = array();
        $statheader = array();
        $statheader[] = array('text'=>'订单编号','key'=>'order_sn');
        $statheader[] = array('text'=>'来源','key'=>'source_name');
        $statheader[] = array('text'=>'销售顾问','key'=>'seller_name');
        $statheader[] = array('text'=>'退款申请人','key'=>'buyer_name');
        $statheader[] = array('text'=>'退款金额','key'=>'r_refund_amount');
        $statheader[] = array('text'=>'退款违约金','key'=>'r_breach_amount');
        $statheader[] = array('text'=>'退款类型','key'=>'return_form');
        $statheader[] = array('text'=>'退款审核时间','key'=>'seller_time');
        $statheader[] = array('text'=>'是否退商品','key'=>'return_type');
        $statheader[] = array('text'=>'申请原因','key'=>'buyer_message');

        $statheader[] = array('text'=>'货品名称','key'=>'goods_name');
        $statheader[] = array('text'=>'款号','key'=>'style_sn');
        $statheader[] = array('text'=>'原价','key'=>'goods_price');
        $statheader[] = array('text'=>'成交价','key'=>'goods_pay_price');
        $statheader[] = array('text'=>'金托类型','key'=>'tuo_type');
        $statheader[] = array('text'=>'期货\现货','key'=>'is_xianhuo');
        $statheader[] = array('text'=>'是否赠品','key'=>'is_zp');


        $statheader[] = array('text'=>'订单总金额','key'=>'order_amount');

        $statheader[] = array('text'=>'订单商品总金额','key'=>'goods_amount');
        $statheader[] = array('text'=>'订单商品数量','key'=>'goods_num');
        $statheader[] = array('text'=>'订单状态','key'=>'order_statetext');
        $statheader[] = array('text'=>'付款状态','key'=>'pay_status');
        $statheader[] = array('text'=>'第一次点款时间','key'=>'payment_time');
        $statheader[] = array('text'=>'发货时间','key'=>'delay_time');
       // $statheader[] = array('text'=>'订单备注','key'=>'remark');

        $statheader[] = array('text'=>'材质','key'=>'caizhi');
        $statheader[] = array('text'=>'金重','key'=>'jinzhong');
        $statheader[] = array('text'=>'系列','key'=>'xilie');
        $statheader[] = array('text'=>'证书号','key'=>'cert_id');
        $statheader[] = array('text'=>'证书类型','key'=>'cert_type');
        $statheader[] = array('text'=>'钻石大小','key'=>'carat');
        $statheader[] = array('text'=>'款式分类','key'=>'cat_type');
        $statheader[] = array('text'=>'产品线','key'=>'product_type');
        $statheader[] = array('text'=>'绑定货号','key'=>'goods_id');
        $statheader[] = array('text'=>'成本','key'=>'jijiachengben');
        $statheader[] = array('text'=>'货品所在仓库','key'=>'warehouse');


        //系列
        $appStypeXilieModel = new app_style_xilieModel();
        $xilie_list = $appStypeXilieModel->getXiLieList(array('statue'=>1));


        $pay_status_arr = array('1'=>'待付款','2'=>'部分付款','3'=>'已付款');
        $return_form_arr = array('1'=>'现金','2'=>'打卡','3'=>'转单');
        $return_type_arr = array('1'=>'不退商品','2'=>'退商品');
        foreach ((array)$order_list as $k=>$v){
            $where = array('goods_id'=>$v['goods_id']);
            $field = 'goods_id,product_type,cat_type,jijiachengben,warehouse';
            $goods_item_arr = $model->statGoodsItem($where,$field);
            if(!empty($goods_item_arr)){
                $v['goods_id'] = join('/',array_values(array_column($goods_item_arr,'goods_id')));
                $v['cat_type'] = join('/',array_values(array_column($goods_item_arr,'cat_type')));
                $v['product_type'] = join('/',array_values(array_column($goods_item_arr,'product_type')));
                $v['warehouse'] = $goods_item_arr[0]['warehouse'];
                $v['jijiachengben'] = join('/',array_values(array_column($goods_item_arr,'jijiachengben')));
            }


            $v['order_sn'] = "'".$v['order_sn'];
            $v['seller_time'] = @date('Y-m-d H:i:s',$v['seller_time']);
            $v['payment_time'] = @date('Y-m-d H:i:s',$v['payment_time']);
            $v['delay_time'] = empty($v['delay_time']) ? '' :@date('Y-m-d H:i:s',$v['delay_time']);
            $v['return_form'] = $return_form_arr[$v['return_form']];
            $v['return_type'] = $return_type_arr[$v['return_type']];

            switch ($v['order_state']){
                case ORDER_STATE_CANCEL:
                    $v['order_statetext'] = '已取消';
                    break;
                case ORDER_STATE_NEW:
                    $v['order_statetext'] = '待付款';
                    break;
                case ORDER_STATE_TO_BC:
                    $v['order_statetext'] = '待布产';
                    break;
                case ORDER_STATE_MAKING:
                    $v['order_statetext'] = '生产中';
                    break;
                case ORDER_STATE_NEW:
                    $v['order_statetext'] = '待付款';
                    break;
                case ORDER_STATE_TOSEND:
                    $v['order_statetext'] = '待发货';
                    break;
                case ORDER_STATE_TO_SIGN:
                    $v['order_statetext'] = '待签收';
                    break;
                case ORDER_STATE_SUCCESS:
                    $v['order_statetext'] = '交易完成';
                    break;
            }


            $v['pay_status'] = $pay_status_arr[$v['pay_status']];


            $v['pay_amount'] = $v['order_amount'] - ($v['rcb_amount'] - $v['refund_amount']) + $v['breach_amount'];
            $v['is_xianhuo'] = $v['is_xianhuo'] == 1 ? '现货':'期货';
            $v['is_zp'] = $v['goods_type'] == 5 ? '是':'否';
            $v['tuo_type'] = $v['tuo_type'] == 1 ? '成品':'空托';



            //系列
            if(!empty(trim($v['xilie'],','))){
                $xilie =  trim($v['xilie'],',');
                $xilie_arr = explode(',',$xilie);
                $xilie_str = '';
                foreach ($xilie_list as $v){
                    if(in_array($v['id'],$xilie_arr)){
                        $xilie_str .= $v['name'].' ';
                    }
                }
                $v['xilie'] = $xilie_str;

            }


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
            $excel_obj->addWorksheet($excel_obj->charset('退货订单记录',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('退货订单记录',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('title','退货订单明细');
        Tpl::output('tag','3');
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&order_state={$this->search_arr['order_state']}&t={$this->search_arr['t']}&is_zp={$this->search_arr['is_zp']}&source_id={$this->search_arr['source_id']}&fenlei={$this->search_arr['fenlei']}&seller_id={$this->search_arr['seller_id']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }










   //销售统计
    public function statisticsOp(){
        $model = new statModel();
        $where = $this->getWhere();
        // $where['order_isvalid'] = 1;//计入统计的有效订单
        $where['pay_status'] = array('gt',ORDER_PAY_TODO);
        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        if(isset($searchtime_arr[0]) && isset($searchtime_arr[1])){
            $where['payment_time'] = array('between',$searchtime_arr);
        }

        $statcount_arr = $this->getSalePric($where);
        //产品销售数量、金额
        $where_sale_cat_type = array();
        foreach ($where as $key=>$value){
            $where_sale_cat_type["o.".$key] = $value;
        }
        $sale_cat_type_arr = $model->getSaleByCatType($where_sale_cat_type);
        $sale_cat_type_sum_num =array();
        $sale_cat_type_sum_price =array();
        foreach ($sale_cat_type_arr as $k=>$v){
            //产品销售数量占比
            $sale_cat_type_sum_num[$k]['p_name'] = empty($v['cat_type']) ? '销售数量': $v['cat_type'];
            $sale_cat_type_sum_num[$k]['allnum'] = intval($v['sum_sale_num']);

            //产品销售金额占比
            $sale_cat_type_sum_price[$k]['p_name'] = empty($v['cat_type']) ? '销售金额': $v['cat_type'];
            $sale_cat_type_sum_price[$k]['allnum'] = floatval($v['sum_sale_price']);
        }
        $data1 = array(
            'title'=>'产品销售数量占比',
            'name'=>'销售数量',
            'label_show'=>true,
            'series'=>$sale_cat_type_sum_num
        );
        Tpl::output('stat_json1',getStatData_Pie($data1));
        $data2 = array(
            'title'=>'产品销售金额占比',
            'name'=>'销售金额',
            'label_show'=>true,
            'series'=>$sale_cat_type_sum_price
        );
        Tpl::output('stat_json2',getStatData_Pie($data2));


        //各渠道分类销售占比
        $sale_cs_fenlei_arr = $model->getSaleByCSFL($where);
        $sale_cs_fenlei_sum_price = array();
        foreach ($sale_cs_fenlei_arr as $k=>$v){
            $sale_cs_fenlei_sum_price[$k]['p_name'] = empty($v['fenlei_name']) ? '销售金额': $v['fenlei_name'];
            $sale_cs_fenlei_sum_price[$k]['allnum'] = floatval($v['sum_sale_price']);
        }
        $data3 = array(
            'title'=>'各渠道分类销售占比',
            'name'=>'各渠道分类',
            'label_show'=>true,
            'series'=>$sale_cs_fenlei_sum_price
        );
        Tpl::output('stat_json3',getStatData_Pie($data3));
        Tpl::output('searchtime',implode('|',$searchtime_arr));
        Tpl::output('statcount_arr',$statcount_arr);
        self::profile_menu('sale');
        Tpl::showpage('stat.sale.statistics');
    }

    //客户来源销售统计明细
    public function statisticslistOp(){
        $model = new statModel();
        $where = $this->getWhere();
        // $where['order_isvalid'] = 1;//计入统计的有效订单
        $where['pay_status'] = array('gt',ORDER_PAY_TODO);
        $searchtime_arr_tmp = explode('|',$this->search_arr['t']);
        foreach ((array)$searchtime_arr_tmp as $k=>$v){
            $searchtime_arr[] = intval($v);
        }
        $where['payment_time'] = array('between',$searchtime_arr);
        $where_sale_sc =array();
        foreach ($where as $key=>$value){
            $where_sale_sc["stat_order.".$key] = $value;
        }

       $field = "CASE customer_sources.fenlei WHEN - 1 THEN '其他' WHEN 1 THEN '异业联盟' WHEN 2 THEN '社区' WHEN 3 THEN '珂兰相关' WHEN 4 THEN '团购' WHEN 5 THEN '老顾客' WHEN 6 THEN '数据' WHEN 7 THEN 	'网络来源' END AS 'fenlei',customer_sources.source_name,SUM(stat_order.order_amount + stat_order.refund_amount + stat_order.breach_amount) as sum_order_amount ,SUM(stat_order.rcb_amount) as sum_rcb_amount,SUM(IFNULL(stat_order.order_amount,0) - IFNULL(stat_order.rcb_amount,0) + IFNULL(stat_order.refund_amount,0) + IFNULL(stat_order.breach_amount,0)) as sum_pay_amount,SUM(stat_order.refund_amount) as sum_refund_amount,count(distinct stat_order.buyer_phone) as buyer_num";
       $group_by = "stat_order.customer_source_id" ;
       $order_by="customer_sources.fenlei DESC";


        $order_list = $model->statOrderCustomerSources($where_sale_sc, $field, 10, 0,$order_by,$group_by);
        Tpl::output('show_page',$model->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $order_list = $model->statOrderCustomerSources($where_sale_sc, $field, 10000, 0,$order_by,$group_by);
        }

        //统计数据标题
        $statlist = array();
        $statheader = array();
        $statheader[] = array('text'=>'来源分类','key'=>'fenlei');
        $statheader[] = array('text'=>'客户来源','key'=>'source_name');
        $statheader[] = array('text'=>'订单金额','key'=>'sum_order_amount');
        $statheader[] = array('text'=>'已付金额','key'=>'sum_rcb_amount');
        $statheader[] = array('text'=>'应收尾款','key'=>'sum_pay_amount');
        $statheader[] = array('text'=>'实退金额','key'=>'sum_refund_amount');
        $statheader[] = array('text'=>'客单价','key'=>'unit_price');
        $statheader[] = array('text'=>'新增预约人数','key'=>'reservation_num');
        $statheader[] = array('text'=>'实际到店人数','key'=>'to_store_num');
        $statheader[] = array('text'=>'成交客户','key'=>'buyer_num');
        foreach ((array)$order_list as $k=>$v){
            if(!empty($v['buyer_num']) && $v['buyer_num'] != 0){
                $v['unit_price'] = ncPriceFormat(round($v['sum_order_amount']/$v['buyer_num'],2)) ;
            }else{
                $v['unit_price'] = 0.00;
            }
            //预约人数、实际到店人数 暂时为0
            $v['reservation_num'] = 0;
            $v['to_store_num'] = 0;

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
            $excel_obj->addWorksheet($excel_obj->charset('客户来源销售统计明细',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('客户来源销售统计明细',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&order_state={$this->search_arr['order_state']}&t={$this->search_arr['t']}&is_zp={$this->search_arr['is_zp']}&source_id={$this->search_arr['source_id']}&fenlei={$this->search_arr['fenlei']}&seller_id={$this->search_arr['seller_id']}");
        Tpl::showpage('stat.listandorder','null_layout');
    }


    //各渠道销售统计
    public function channels_statisticsOp(){
        $model = new statModel();
        $where = $this->getWhere();
        // $where['order_isvalid'] = 1;//计入统计的有效订单
        $where['pay_status'] = array('gt',ORDER_PAY_TODO);
        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        if(isset($searchtime_arr[0]) && isset($searchtime_arr[1])){
            $where['payment_time'] = array('between',$searchtime_arr);
        }
        $statcount_arr = $this->getSalePric($where);
        $where_sale_cs =array();
        foreach ($where as $key=>$value){
            $where_sale_cs["o.".$key] = $value;
        }
        $sale_sc_list = $model->getSaleByCS($where_sale_cs);
        $sale_sc_fenlei_list = array();//组装成以客户来源分类分组的数组
        foreach ($sale_sc_list as $k=>$v){
            $sale_sc_fenlei_list[$v['fenlei']][] = $v;
        }

        $where_sale_ct =array();
        foreach ($where as $key=>$value){
            $where_sale_ct["o.".$key] = $value;
        }

        $i = 0;
        foreach ($sale_sc_fenlei_list as $fenklei=>$customer_sources_list){
            $stocklist_sc_price =array();
            $stocklist_ct_price =array();
            //渠道占比
            foreach ($customer_sources_list as $k=>$v){
                $stocklist_sc_price[$k]['p_name'] = empty($v['source_name']) ? '订单金额': $v['source_name'];
                $stocklist_sc_price[$k]['allnum'] = floatval($v['sum_order_amount']);
            }
            $data_cs = array(
                'title'=>$this->cs_fenlei[$fenklei].' 渠道占比',
                'name'=>'订单金额',
                'label_show'=>true,
                'series'=>$stocklist_sc_price,
                'legend'=>array(


                )

            );
            Tpl::output('stat_cs_json'.$i,getStatData_Pie($data_cs));
            //产品分类占比
            $where_sale_ct['c.fenlei'] = $fenklei;
            $sale_cat_type_arr = $model->getSaleByCatType($where_sale_ct);
            foreach ($sale_cat_type_arr as $k=>$v){
                $stocklist_ct_price[$k]['p_name'] = empty($v['cat_type']) ? '商品金额': $v['cat_type'];
                $stocklist_ct_price[$k]['allnum'] = floatval($v['sum_sale_price']);
            }
            $data_ct = array(
                'title'=>$this->cs_fenlei[$fenklei].' 产品分类占比',
                'name'=>'商品总金额',
                'label_show'=>true,
                'series'=>$stocklist_ct_price
            );
            Tpl::output('stat_ct_json'.$i,getStatData_Pie($data_ct));
            $i++;
        }
        Tpl::output('stocklist_num',$i);


        Tpl::output('statcount_arr',$statcount_arr);
        self::profile_menu('sale');
        Tpl::showpage('stat.sale.channels_statistics');
    }






    private function getSalePric($where){
        $model = new statModel();
        $statcount_arr =array();
        $count = $model->getStatOrderCount($where,'*');
        if($count){
            //订单总金额、收款总金额、退款总金额、违约金、订单总数量、成交会员数,商品数量
            $field = "SUM(order_amount + refund_amount + breach_amount) as sum_order_amount,SUM(rcb_amount) as sum_rcb_amount,SUM(refund_amount) as sum_refund_amount,SUM(breach_amount) as sum_breach_amount,count(*) as sum_order_num,count(distinct buyer_phone) as sum_buyer_deal_num";
            $order_sum_arr = $model->getoneByStatorder($where,$field);


            $statcount_arr['sum_order_amount'] = ncPriceFormat($order_sum_arr['sum_order_amount']);
            $statcount_arr['sum_rcb_amount'] = ncPriceFormat($order_sum_arr['sum_rcb_amount']);
            $statcount_arr['sum_refund_amount'] = ncPriceFormat($order_sum_arr['sum_refund_amount']);
            $statcount_arr['sum_breach_amount'] = ncPriceFormat($order_sum_arr['sum_breach_amount']);
            //真实收款 = 已收金额-退款金额
            $statcount_arr['sum_real_amount'] = ncPriceFormat($statcount_arr['sum_rcb_amount'] - $statcount_arr['sum_refund_amount']);
            //应收尾款 = 订单总金额（包含违约金和退款金额）- 已付金额
            $statcount_arr['sum_pay_amount'] = ncPriceFormat($statcount_arr['sum_order_amount'] - $statcount_arr['sum_rcb_amount']);
            //订单总数量
            $statcount_arr['sum_order_num'] = $order_sum_arr['sum_order_num'];

            //预约人员、到店人员目前默认为0,成交会员数、
            $statcount_arr['sum_buyer_reservation_num'] = 0;
            $statcount_arr['sum_buyer_toshop_num'] = 0;
            $statcount_arr['sum_buyer_deal_num'] = $order_sum_arr['sum_buyer_deal_num'];
            //平均客单价
            $statcount_arr['average_buyer_price'] = ncPriceFormat(round($statcount_arr['sum_order_amount']/$statcount_arr['sum_buyer_deal_num'],2));


            $field = "SUM(goods_num) as sum_goods_num ";
            $where1 = array();
            foreach ($where as $k=>$v){
                $where1['stat_order.'.$k] = $v;
            }
            $order_goods_arr = $model->getStatOrderGoods($where1,$field);
            if(!empty($order_goods_arr[0])){
                //商品数量
                $statcount_arr['sum_goods_num'] = $order_goods_arr[0]['sum_goods_num'];
                //平均价格（商品）
                $statcount_arr['average_price'] = ncPriceFormat(round($statcount_arr['sum_order_amount']/$statcount_arr['sum_goods_num'],2));
            }
        }

        return $statcount_arr;
    }


    /**
     * 地区分布
     */
    public function areaOp(){
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'day';
        }
        $model = Model('stat');
        //获得搜索的开始时间和结束时间
        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        $where = array();
        $where['store_id'] = $_SESSION['store_id'];
       // $where['order_isvalid'] = 1;//计入统计的有效订单
        $where['pay_status'] = array('gt',ORDER_PAY_TODO);
        $where['payment_time'] = array('between',$searchtime_arr);
        $memberlist = array();
        //查询统计数据
        $field = ' reciver_province_id,SUM(order_amount + refund_amount + breach_amount) as orderamount,COUNT(*) as ordernum,COUNT(DISTINCT buyer_phone) as membernum ';
        $orderby = 'reciver_province_id';
        $statlist = $model->statByStatorder($where, $field, 10, 0, $orderby, 'reciver_province_id');
        $datatype = array('orderamount'=>'下单金额','ordernum'=>'下单量','membernum'=>'下单会员数');
        //处理数组，将数组按照$datatype分组排序
        $statlist_tmp = array();
        foreach ((array)$statlist as $k=>$v){
            foreach ((array)$datatype as $t_k=>$t_v){
                $statlist_tmp[$t_k][$k] = $v[$t_k];
            }
        }
        foreach ((array)$statlist_tmp as $t_k=>$t_v){
            arsort($statlist_tmp[$t_k]);
        }
        $statlist_tmp2 = $statlist_tmp;
        $statlist_tmp = array();
        foreach ((array)$statlist_tmp2 as $t_k=>$t_v){
            foreach ($t_v as $k=>$v){
                $statlist[$k]['orderamount'] = floatval($statlist[$k]['orderamount']);
                $statlist[$k]['ordernum'] = intval($statlist[$k]['ordernum']);
                $statlist[$k]['membernum'] = intval($statlist[$k]['membernum']);
                $statlist_tmp[$t_k][] = $statlist[$k];
            }
        }
        // 地区
        $province_array = Model('area')->getTopLevelAreas();
        //地图显示等级数组
        $level_arr = array(array(1,2,3),array(4,5,6),array(7,8,9),array(10,11,12));
        $statlist = array();
        $stat_arr_bar = array();
        foreach ((array)$statlist_tmp as $t_k=>$t_v){
            foreach ((array)$t_v as $k=>$v){
                $v['level'] = 4;//排名
                foreach ($level_arr as $lk=>$lv){
                    if (in_array($k+1,$lv)){
                        $v['level'] = $lk;//排名
                    }
                }
                $province_id = intval($v['reciver_province_id']);
                $statlist[$t_k][$province_id] = $v;
                if ($province_id){
                    //数据
                    $stat_arr_bar[$t_k]['series'][0]['data'][] = array('name'=>strval($province_array[$province_id]),'y'=>$v[$t_k]);
                    //横轴
                    $stat_arr_bar[$t_k]['xAxis']['categories'][] = strval($province_array[$province_id]);
                } else {
                    //数据
                    $stat_arr_bar[$t_k]['series'][0]['data'][] = array('name'=>'未知','y'=>$v[$t_k]);
                    //横轴
                    $stat_arr_bar[$t_k]['xAxis']['categories'][] = '未知';
                }
            }
        }
        $stat_arr_map = array();
        foreach ((array)$province_array as $k=>$v){
            foreach ($datatype as $t_k=>$t_v){
                if ($statlist[$t_k][$k][$t_k]){
                    $des = "，{$t_v}：{$statlist[$t_k][$k][$t_k]}";
                    $stat_arr_map[$t_k][] = array('cha'=>$k,'name'=>$v,'des'=>$des,'level'=>$statlist[$t_k][$k]['level']);
                } else {
                    $des = "，无订单数据";
                    $stat_arr_map[$t_k][] = array('cha'=>$k,'name'=>$v,'des'=>$des,'level'=>4);
                }
            }
        }
        $stat_json_map = array();
        $stat_json_bar = array();
        if ($statlist){
            foreach ($datatype as $t_k=>$t_v){
                //得到地图数据
                $stat_json_map[$t_k] = getStatData_Map($stat_arr_map[$t_k]);
                //得到统计图数据
                $stat_arr_bar[$t_k]['series'][0]['name'] = $t_v;
                $stat_arr_bar[$t_k]['title'] = "地区排行";
                $stat_arr_bar[$t_k]['legend']['enabled'] = false;
                $stat_arr_bar[$t_k]['yAxis']['title']['text'] = $t_v;
                $stat_arr_bar[$t_k]['yAxis']['title']['align'] = 'high';
                $stat_json_bar[$t_k] = getStatData_Basicbar($stat_arr_bar[$t_k]);
            }
        }
        Tpl::output('stat_json_map',$stat_json_map);
        Tpl::output('stat_json_bar',$stat_json_bar);
        self::profile_menu('area');
        Tpl::showpage('stat.sale.area');
    }
    /**
     * 购买分析
     */
    public function buyingOp(){
        if(!$this->search_arr['search_type']){
            $this->search_arr['search_type'] = 'day';
        }
        $model = Model('stat');
        //获得搜索的开始时间和结束时间
        $searchtime_arr = $model->getStarttimeAndEndtime($this->search_arr);
        /*
         * 客单价分布
         */
        $where = $this->getWhere();
        //$where['order_isvalid'] = 1;//计入统计的有效订单
        $where['pay_status'] = array('gt',ORDER_PAY_TODO);
        $where['order_add_time'] = array('between',$searchtime_arr);
        $field = '1';
        $pricerange = Model('store_extend')->getfby_store_id($_SESSION['store_id'],'orderpricerange');
        $pricerange_arr = $pricerange?unserialize($pricerange):array();
        if ($pricerange_arr){
            $stat_arr['series'][0]['name'] = '下单量';
            //设置价格区间最后一项，最后一项只有开始值没有结束值
            $pricerange_count = count($pricerange_arr);
            if ($pricerange_arr[$pricerange_count-1]['e']){
                $pricerange_arr[$pricerange_count]['s'] = $pricerange_arr[$pricerange_count-1]['e'] + 1;
                $pricerange_arr[$pricerange_count]['e'] = '';
            }
            foreach ((array)$pricerange_arr as $k=>$v){
                $v['s'] = intval($v['s']);
                $v['e'] = intval($v['e']);
                //构造查询字段
                if (C('dbdriver') == 'mysqli') {
                    if ($v['e']){
                        $field .= " ,SUM(IF(order_amount + refund_amount + breach_amount > {$v['s']} and order_amount + refund_amount + breach_amount <= {$v['e']},1,0)) as ordernum_{$k}";
                    } else {
                        $field .= " ,SUM(IF(order_amount + refund_amount + breach_amount > {$v['s']},1,0)) as ordernum_{$k}";
                    }                    
                } elseif (C('dbdriver') == 'oracle') {
                    if ($v['e']){
                        $field .= " ,SUM((case when order_amount + refund_amount + breach_amount > {$v['s']} and order_amount + refund_amount + breach_amount <= {$v['e']} then 1 else 0 end)) as ordernum_{$k}";
                    } else {
                        $field .= " ,SUM((case when order_amount + refund_amount + breach_amount > {$v['s']} then 1 else 0 end)) as ordernum_{$k}";
                    }
                }else{
                    if ($v['e']){
                        $field .= " ,SUM(IF(order_amount + refund_amount + breach_amount > {$v['s']} and order_amount <= {$v['e']},1,0)) as ordernum_{$k}";
                    } else {
                        $field .= " ,SUM(IF(order_amount + refund_amount + breach_amount > {$v['s']},1,0)) as ordernum_{$k}";
                    }
                }

            }
            $orderlist = $model->getoneByStatorder($where, $field);
            if($orderlist){
                foreach ((array)$pricerange_arr as $k=>$v){
                    //横轴
                    if ($v['e']){
                        $stat_arr['xAxis']['categories'][] = $v['s'].'-'.$v['e'];
                    } else {
                        $stat_arr['xAxis']['categories'][] = $v['s'].'以上';
                    }
                    //统计图数据
                    if ($orderlist['ordernum_'.$k]){
                        $stat_arr['series'][0]['data'][] = intval($orderlist['ordernum_'.$k]);
                    } else {
                        $stat_arr['series'][0]['data'][] = 0;
                    }
                }
            }
            //得到统计图数据
            $stat_arr['title'] = '客单价分布';
            $stat_arr['legend']['enabled'] = false;
            $stat_arr['yAxis'] = '下单量';
            $guestprice_statjson = getStatData_LineLabels($stat_arr);
        } else {
            $guestprice_statjson = '';
        }
        unset($stat_arr);

        //购买时段分布
        $where = $this->getWhere();
        //$where['order_isvalid'] = 1;//计入统计的有效订单
        $where['pay_status'] = array('gt',ORDER_PAY_TODO);
        $where['order_add_time'] = array('between',$searchtime_arr);
        $field = ' HOUR(FROM_UNIXTIME(order_add_time)) as hourval,COUNT(*) as ordernum ';
        if (C('dbdriver') == 'mysqli') {
            $_group = 'hourval';
        } else {
            $_group = 'HOUR(FROM_UNIXTIME(order_add_time))';
        }
        $orderlist = $model->statByStatorder($where, $field, 0, 0, 'hourval asc', $_group);
        $stat_arr = array();
        $stat_arr['series'][0]['name'] = '下单量';
        //构造横轴坐标
        for ($i=0; $i<24; $i++){
            //横轴
            $stat_arr['xAxis']['categories'][] = $i;
            $stat_arr['series'][0]['data'][$i] = 0;
        }
        foreach ((array)$orderlist as $k=>$v){
            //统计图数据
            $stat_arr['series'][0]['data'][$v['hourval']] = intval($v['ordernum']);
        }
        //得到统计图数据
        $stat_arr['title'] = '购买时段分布';
        $stat_arr['legend']['enabled'] = false;
        $stat_arr['yAxis'] = '下单量';
        $hour_statjson = getStatData_LineLabels($stat_arr);
        Tpl::output('hour_statjson',$hour_statjson);
        Tpl::output('guestprice_statjson',$guestprice_statjson);
        self::profile_menu('sale');
        Tpl::showpage('stat.sale.buying');
    }


    public function get_sources_listOp(){
        $where =array();
        if(isset($_GET['fenlei']) && !empty($_GET['fenlei'])){
            $where['fenlei'] = $_GET['fenlei'];
        }
        $sourcelist = $this->get_sources_list($where);
        echo json_encode($sourcelist);
        die;
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
           // 2=>array('menu_key'=>'member','menu_name'=>'会员分析','menu_url'=>'index.php?act=statistics_member&op=member'),
            3=>array('menu_key'=>'loss','menu_name'=>'损益分析','menu_url'=>'index.php?act=statistics_loss&op=loss'),
            4=>array('menu_key'=>'stock','menu_name'=>'库存分析','menu_url'=>'index.php?act=statistics_stock&op=stock'),
        );
        Tpl::output('member_menu',$menu_array);
        Tpl::output('menu_key',$menu_key);
    }


   //获取客户来源
    private function get_sources_list($where = array()){
        $store_company_id = $this->store_info['store_company_id'];
        $where['company_ids'] = array($store_company_id);
        $management_api = data_gateway('imanagement');

        $res = $management_api->get_customer_sources_list($where);
        return $sourcelist = isset($res['return_msg'])?$res['return_msg']:array();
        /*
        $store_company_id = $this->store_info['store_company_id'];
        $where['company_ids'] = array($store_company_id);
        $model = new statModel();
        $sourcelist =$model->getCustomerSourcesByIds($where);
        return $sourcelist;
       */
    }




}
