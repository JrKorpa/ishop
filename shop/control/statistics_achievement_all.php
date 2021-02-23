<?php
/**
 * Created by PhpStorm.
 * User: kela016
 * Date: 2019/3/26
 * Time: 18:01
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class statistics_achievement_allControl extends BaseSellerControl
{

    public function __construct()
    {
        parent::__construct();
        //存储参数
        $this->search_arr = $_REQUEST;
        //时间处理
        $this->search_arr = $this->getYearMonth($this->search_arr);
        //搜索表单提交地址
        Tpl::output('search_arr', $this->search_arr);
        Tpl::output('act', $this->search_arr['act']);
        Tpl::output('op', $this->search_arr['op']);
    }

    public function achievementOp(){
        $this->achievement_list();
        //Tpl::showpage('stat.statistics_achievement_all.achievement');
    }

    public function achievement_yiyeOp(){
        $this->achievement_yiye_list();
        //Tpl::showpage('stat.statistics_achievement_all.achievement_yiye');
    }


    public function achievement_zirenOp(){
        $this->achievement_ziren();
        //Tpl::showpage('stat.statistics_achievement_all.achievement_ziren');
    }



    //判断时间范围是否夸月，并返回年月
    private function getYearMonth($search_arr){
        if(isset($search_arr['start_date']) && isset($search_arr['end_date'])){
            $start_date = $search_arr['end_date'];
            $end_date = $search_arr['end_date'];
            if(date('Y',strtotime($start_date)) != date('Y',strtotime($end_date))){
                return false;
            }else{
                $search_arr['year'] = date('Y',strtotime($start_date));
            }
            if(date('m',strtotime($start_date)) != date('m',strtotime($end_date))){
                return false;
            }else{
                $search_arr['month'] = date('m',strtotime($start_date));
            }

        }else{
            $search_arr['year'] =date('Y',time());
            $search_arr['month'] =date('m',time());
            $search_arr['start_date'] = $search_arr['year']."-".$search_arr['month']."-01";
            $search_arr['end_date'] = date('Y-m-d',time());
        }
        return $search_arr;

    }




    public function achievement_list(){
        $storeModel = new storeModel();
        $where = array('sc_id'=>2);
        $field = 'store_id,store_name';
        $store_list = $storeModel->getStoreList($where,10,'',$field);
        Tpl::output('show_page',$storeModel->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $page = $storeModel->gettotalnum();
            $store_list = $storeModel->getStoreList($where, $page, '',$field);
        }
        $statlist = array();
        foreach ($store_list as $key=>$value){
            $store_id = $value['store_id'];
            $statcount_arr = $this->achievement($store_id);
            $statcount_arr['store_name'] = $value['store_name'];
            $statlist[] = $statcount_arr;
        }

        $statheader = array();
        $statheader[] = array('text'=>'门店','key'=>'store_name');
        $statheader[] = array('text'=>'任务','key'=>'task');
        $statheader[] = array('text'=>'月销售','key'=>'month_amount');
        $statheader[] = array('text'=>'达成比（%）','key'=>'achieve_ratio');
        $statheader[] = array('text'=>'销售','key'=>'amount');
        $statheader[] = array('text'=>'进店量','key'=>'shop_num');
        $statheader[] = array('text'=>'成交单数','key'=>'num');
        $statheader[] = array('text'=>'转化率（%）','key'=>'cconversion_rate');
        $statheader[] = array('text'=>'异业进店量','key'=>'yiye_shop_num');
        $statheader[] = array('text'=>'异业成交单数','key'=>'yiye_num');
        $statheader[] = array('text'=>'异业转化率（%）','key'=>'yiye_cconversion_rate');
        $statheader[] = array('text'=>'自然进店','key'=>'ziren_shop_num');
        $statheader[] = array('text'=>'自然成交单数','key'=>'ziren_num');
        $statheader[] = array('text'=>'自然转化率（%）','key'=>'ziren_cconversion_rate');
        $statheader[] = array('text'=>'老顾客及转介绍成单数','key'=>'laoguke_num');
        $statheader[] = array('text'=>'老顾客及转介绍成交占比（%）','key'=>'laoguke_num_rate');

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
            $excel_obj->addWorksheet($excel_obj->charset('统计表',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('统计表',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('title','统计表');
        Tpl::output('tag','1');
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&start_date={$this->search_arr['start_date']}&end_date={$this->search_arr['end_date']}");
        Tpl::showpage('stat.listandorder','null_layout');

    }



    public function achievement_yiye_list(){
        $storeModel = new storeModel();
        $where = array('sc_id'=>2);
        $field = 'store_id,store_name';
        $store_list = $storeModel->getStoreList($where,10,'',$field);
        Tpl::output('show_page',$storeModel->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $page = $storeModel->gettotalnum();
            $store_list = $storeModel->getStoreList($where, $page, '',$field);
        }
        $statlist = array();
        foreach ($store_list as $key=>$value){
            $store_id = $value['store_id'];
            $statcount_arr = $this->achievement_yiye($store_id);
            $statcount_arr['store_name'] = $value['store_name'];
            $statlist[] = $statcount_arr;
        }

        $statheader = array();
        $statheader[] = array('text'=>'门店','key'=>'store_name');
        $statheader[] = array('text'=>'合计','key'=>'task');
        $statheader[] = array('text'=>'A类客户','key'=>'month_amount');
        $statheader[] = array('text'=>'B类客户','key'=>'achieve_ratio');
        $statheader[] = array('text'=>'C类客户','key'=>'amount');
        $statheader[] = array('text'=>'D类客户','key'=>'shop_num');
        $statheader[] = array('text'=>'E类客户','key'=>'num');
        $statheader[] = array('text'=>'成交','key'=>'cconversion_rate');
        $statheader[] = array('text'=>'转化率（%）','key'=>'yiye_shop_num');
        $statheader[] = array('text'=>'A类客户占比（%）','key'=>'yiye_num');
        $statheader[] = array('text'=>'E类客户占比（%）','key'=>'yiye_cconversion_rate');

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
            $excel_obj->addWorksheet($excel_obj->charset('异业统计表',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('异业统计表',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('title','异业统计表');
        Tpl::output('tag','1');
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&start_date={$this->search_arr['start_date']}&end_date={$this->search_arr['end_date']}");
        Tpl::showpage('stat.listandorder','null_layout');

    }



    public function achievement_ziren_list(){
        $storeModel = new storeModel();
        $where = array('sc_id'=>2);
        $field = 'store_id,store_name';
        $store_list = $storeModel->getStoreList($where,10,'',$field);
        Tpl::output('show_page',$storeModel->showpage(2));
        if ($_GET['exporttype'] == 'excel'){
            $page = $storeModel->gettotalnum();
            $store_list = $storeModel->getStoreList($where, $page, '',$field);
        }
        $statlist = array();
        foreach ($store_list as $key=>$value){
            $store_id = $value['store_id'];
            $statcount_arr = $this->achievement_ziren($store_id);
            $statcount_arr['store_name'] = $value['store_name'];
            $statlist[] = $statcount_arr;
        }

        $statheader = array();
        $statheader[] = array('text'=>'门店','key'=>'store_name');
        $statheader[] = array('text'=>'合计','key'=>'task');
        $statheader[] = array('text'=>'A类客户','key'=>'month_amount');
        $statheader[] = array('text'=>'B类客户','key'=>'achieve_ratio');
        $statheader[] = array('text'=>'C类客户','key'=>'amount');
        $statheader[] = array('text'=>'D类客户','key'=>'shop_num');
        $statheader[] = array('text'=>'E类客户','key'=>'num');
        $statheader[] = array('text'=>'成交','key'=>'cconversion_rate');
        $statheader[] = array('text'=>'转化率（%）','key'=>'yiye_shop_num');
        $statheader[] = array('text'=>'A类客户占比（%）','key'=>'yiye_num');
        $statheader[] = array('text'=>'E类客户占比（%）','key'=>'yiye_cconversion_rate');

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
            $excel_obj->addWorksheet($excel_obj->charset('自然进店统计表',CHARSET));
            $excel_obj->generateXML($excel_obj->charset('自然进店统计表',CHARSET).date('Y-m-d-H',time()));
            exit();
        }
        Tpl::output('title','自然进店统计表');
        Tpl::output('tag','1');
        Tpl::output('statheader',$statheader);
        Tpl::output('statlist',$statlist);

        Tpl::output('actionurl',"index.php?act={$this->search_arr['act']}&op={$this->search_arr['op']}&start_date={$this->search_arr['start_date']}&end_date={$this->search_arr['end_date']}");
        Tpl::showpage('stat.listandorder','null_layout');

    }
    //统计表
    private function achievement($store_id){
        $statcount_arr = array();
        $task = $this->getTask($store_id);
        //任务
        $statcount_arr['task'] = $task;

        $saleAndBuyNum = $this->getAmountAndNum($store_id);
        //每月销售
        $statcount_arr['month_amount'] = $saleAndBuyNum['month_amount'];

        //选择时间内销售
        $statcount_arr['amount'] = $saleAndBuyNum['amount'];
        //成交数
        $statcount_arr['num'] = $saleAndBuyNum['num'];
        //到店数
        $statcount_arr['shop_num'] = $this->getShopCount($store_id);
        //达成比
        if($statcount_arr['task'] != 0){
            $statcount_arr['achieve_ratio'] = round($statcount_arr['month_amount']/$statcount_arr['task'] * 100,2);
        }else{
            $statcount_arr['achieve_ratio'] = 0;
        }
        //转化率
        if($statcount_arr['shop_num'] != 0){
            $statcount_arr['cconversion_rate'] = round($statcount_arr['num']/$statcount_arr['shop_num'] * 100,2);
        }else{
            $statcount_arr['cconversion_rate'] = 0;
        }

        $yiyeSaleAndBuyNum = $this->getAmountAndNum($store_id,1);
        //异业成交数
        $statcount_arr['yiye_num'] = $yiyeSaleAndBuyNum['num'];
        //异业到店数
        $statcount_arr['yiye_shop_num'] = $this->getShopCount($store_id,1);

        //异业转化率
        if($statcount_arr['yiye_shop_num'] != 0){
            $statcount_arr['yiye_cconversion_rate'] = round($statcount_arr['yiye_num']/$statcount_arr['yiye_shop_num'] * 100,2);
        }else{
            $statcount_arr['yiye_cconversion_rate'] = 0;
        }

        $zirenSaleAndBuyNum = $this->getAmountAndNum($store_id,-1,'自然进店');
        //自然进店成交数
        $statcount_arr['ziren_num'] = $zirenSaleAndBuyNum['num'];
        //自然进店到店数
        $statcount_arr['ziren_shop_num'] = $this->getShopCount($store_id,-1,null,'自然进店');
        //自然进店转化率
        if($statcount_arr['ziren_shop_num'] != 0){
            $statcount_arr['ziren_cconversion_rate'] = round($statcount_arr['ziren_num']/$statcount_arr['ziren_shop_num'] * 100,2);
        }else{
            $statcount_arr['ziren_cconversion_rate'] = 0;
        }

        $laogukeSaleAndBuyNum = $this->getAmountAndNum($store_id,5);
        //老顾客成交数
        $statcount_arr['laoguke_num'] = $laogukeSaleAndBuyNum['num'];
        //老顾客成交占比
        if($statcount_arr['num'] != 0){
            $statcount_arr['laoguke_num_rate'] = round($statcount_arr['laoguke_num']/$statcount_arr['num']*100,2);
        }else{
            $statcount_arr['laoguke_num_rate'] = 0;
        }

        return $statcount_arr;
    }



        $saleAndBuyNum = $this->getAmountAndNum($store_id,$fenlei);
        $num = $saleAndBuyNum['num'];
        $statcount_arr['num'] = $num;

        //A类客户
        $statcount_arr['A_shop_num'] = $this->getShopCount($store_id,$fenlei,'A1') + $this->getShopCount($store_id,$fenlei,'A2') + $this->getShopCount($store_id,$fenlei,'A');

        //B类客户
        $statcount_arr['B_shop_num'] = $this->getShopCount($store_id,$fenlei,'B');

        //C类客户
        $statcount_arr['C_shop_num'] = $this->getShopCount($store_id,$fenlei,'C');

        //D类客户
        $statcount_arr['D_shop_num'] = $this->getShopCount($store_id,$fenlei,'D');

        //E类客户
        $statcount_arr['E_shop_num'] = $this->getShopCount($store_id,$fenlei,'E');

        //合计
        $statcount_arr['shop_num'] = $statcount_arr['A_shop_num'] + $statcount_arr['B_shop_num'] + $statcount_arr['C_shop_num'] + $statcount_arr['D_shop_num'] + $statcount_arr['E_shop_num'];

        //转化率、A类客户占比、E类客户占比
        if($statcount_arr['shop_num'] != 0){
            $statcount_arr['cconversion_rate'] = round($statcount_arr['num']/$statcount_arr['shop_num']*100,2);
            $statcount_arr['A_cconversion_rate'] = round($statcount_arr['A_shop_num']/$statcount_arr['shop_num']*100,2);
            $statcount_arr['E_cconversion_rate'] = round($statcount_arr['E_shop_num']/$statcount_arr['shop_num']*100,2);
        }else{
            $statcount_arr['cconversion_rate'] = 0;
            $statcount_arr['A_cconversion_rate'] = 0;
            $statcount_arr['E_cconversion_rate'] = 0;
        }



        return $statcount_arr;


    }


    //自然进店客户
    private function achievement_ziren($store_id){
        $statcount_arr = array();
        //成交数
        $source_name= "自然进店";
        $fenlei = -1;
        $saleAndBuyNum = $this->getAmountAndNum($store_id,$fenlei,$source_name);
        $num = $saleAndBuyNum['num'];
        $statcount_arr['num'] = $num;

        //A类客户
        $statcount_arr['A_shop_num'] = $this->getShopCount($store_id,$fenlei,'A1',$source_name) + $this->getShopCount($store_id,$fenlei,'A2',$source_name) + $this->getShopCount($store_id,$fenlei,'A',$source_name);

        //B类客户
        $statcount_arr['B_shop_num'] = $this->getShopCount($store_id,$fenlei,'B',$source_name);

        //C类客户
        $statcount_arr['C_shop_num'] = $this->getShopCount($store_id,$fenlei,'C',$source_name);

        //D类客户
        $statcount_arr['D_shop_num'] = $this->getShopCount($store_id,$fenlei,'D',$source_name);

        //E类客户
        $statcount_arr['E_shop_num'] = $this->getShopCount($store_id,$fenlei,'E',$source_name);

        //合计
        $statcount_arr['shop_num'] = $statcount_arr['A_shop_num'] + $statcount_arr['B_shop_num'] + $statcount_arr['C_shop_num'] + $statcount_arr['D_shop_num'] + $statcount_arr['E_shop_num'];

        //转化率、A类客户占比、E类客户占比
        if($statcount_arr['shop_num'] != 0){
            $statcount_arr['cconversion_rate'] = round($statcount_arr['num']/$statcount_arr['shop_num']*100,2);
            $statcount_arr['A_cconversion_rate'] = round($statcount_arr['A_shop_num']/$statcount_arr['shop_num']*100,2);
            $statcount_arr['E_cconversion_rate'] = round($statcount_arr['E_shop_num']/$statcount_arr['shop_num']*100,2);
        }else{
            $statcount_arr['cconversion_rate'] = 0;
            $statcount_arr['A_cconversion_rate'] = 0;
            $statcount_arr['E_cconversion_rate'] = 0;
        }



        return $statcount_arr;


    }

    //获取当月销售、销售和成交数
    public function getAmountAndNum($store_id,$fenlei=null,$source_name=null){
        $model = new statModel();
        $start_date = strtotime($this->search_arr['start_date']." 00:00:00");
        $end_date = strtotime($this->search_arr['end_date']." 23:59:59");

        $where = array('store_id'=>$store_id);

        $where['payment_time'] = array('between',array($start_date,$end_date));
        if($fenlei != null) $where['fenlei'] = $fenlei;
        if($source_name != null) $where['source_name'] = array('like',$source_name);

        //订单总金额、收款总金额、退款总金额、违约金、订单总数量、成交会员数,商品数量
        $field = "SUM(rcb_amount) as sum_rcb_amount,SUM(refund_amount) as sum_refund_amount,SUM(breach_amount) as sum_breach_amount,count(distinct buyer_phone) as sum_buyer_deal_num";
        $order_sum_arr = $model->getoneByStatorder($where,$field);
        $sum_real_amount = ncPriceFormat($order_sum_arr['sum_rcb_amount'] - $order_sum_arr['sum_refund_amount']);
        $sum_buyer_num = $order_sum_arr['sum_buyer_deal_num'];

        $start_date1 = strtotime($this->search_arr['year']."-".$this->search_arr['month']."-01 00:00:00");
        $end_date1 = strtotime(date('Y-m-t',$start_date)." 23:59:59");
        $where['payment_time'] = array('between',array($start_date1,$end_date1));
        $field = "SUM(rcb_amount) as sum_rcb_amount,SUM(refund_amount) as sum_refund_amount,SUM(breach_amount) as sum_breach_amount,count(distinct buyer_phone) as sum_buyer_deal_num";
        $order_sum_arr = $model->getoneByStatorder($where,$field);
        $month_sum_real_amount = ncPriceFormat($order_sum_arr['sum_rcb_amount'] - $order_sum_arr['sum_refund_amount']);


        return array('amount'=>$sum_real_amount,'num'=>$sum_buyer_num,'month_amount'=>$month_sum_real_amount);

    }

    //获取到店数
    private function getShopCount($store_id,$fenlei=null,$grade=null,$source_name=null){
        $post_data = array('department_id'=>$store_id);
        $post_data['start_time'] = $this->search_arr['start_date'];
        $post_data['end_time'] = $this->search_arr['end_date'];
        if($fenlei != null){
            $post_data['fenlei'] = $fenlei;
        }
        if($grade != null){
            $post_data['grade'] = $grade;
        }
        if($source_name != null){
            $post_data['source_name'] = $source_name;
        }

        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $this->api_url."?".http_build_query($post_data));
        //设置头文件的信息作为数据流输出
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        //curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据

        //curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //执行命令
        $data = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        $datas = json_decode($data,true);

        //返回200/201表示请求成功
        if( $httpCode == '200'||$httpCode == '201'){
            if($datas['code'] == 1) {
                return $datas['to_shop_count'];
            }else{
                return 0;
            }
        } else{
            return 0;
        }

    }






}