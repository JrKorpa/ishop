<?php
/**
 * 任务计划 - 每天执行的任务
 *
 *
 * @提供技术支持 授权请购买正版授权
 * @license    http://官网
 * @link       交流群号：官网群
 */
//defined('INTELLIGENT_SYS') or exit('Access Invalid!');

class calc_xianhuo_sale_priceControl extends BaseCronControl { 
    /**
     * 执行频率常量 1天
     * @var int
     */
    const EXE_TIMES = 3600;
    private $channel_list = array(178,153,344,166,392,152);
   

    /**
     * 默认方法
     */
    public function indexOp() {
        //更新全文搜索内容
        //echo "test"; 
        $param_channel_id = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
		$param_goods_id = !empty($_GET['goods_id']) ? $_GET['goods_id'] : 0;
		$param_output = !empty($_GET['output']) ? $_GET['output'] : 0;
		if (PHP_SAPI == 'cli') {
		    $param_channel_id = empty($_SERVER['argv'][3]) ? 0 : $_SERVER['argv'][3];
		    $param_goods_id = empty($_SERVER['argv'][4]) ? 0 : $_SERVER['argv'][4];
		    $param_output = empty($_SERVER['argv'][5]) ? 0 : $_SERVER['argv'][5];
		}		
		if(empty($param_channel_id))
		    return false;        
        
        $this->calculateNew($param_channel_id,$param_goods_id,$param_output);
    }

    private function calculateNew($param_channel_id,$param_goods_id,$param_output){
    	    //header("Content-Type:text/html; charset=utf-8");
    	    ini_set('memory_limit', '256M');
    	    set_time_limit(0);
		    $date = date("Y-m-d H:i:s");
		   	//$note_string = $date."--可供智慧门店销售的总部现货数量:".count($xianhuo_goods_list)."\r\n<br>";
			echo "start....";
		    //file_put_contents('cron_calculate_ishop_sale_price.log',$note_string,FILE_APPEND);
		    $k2=0;
           //echo "<pre>";
		    $multi_price_outfile = "";
		    $unfind_price_outfile = "";
           	if(!empty($param_output)){
		       $multi_price_outfile = __DIR__."multi_price_outfile.csv";
		       $unfind_price_outfile = __DIR__."unfind_price_outfile.csv";	    		
		    } 	
           if(!empty($param_channel_id))
               $channel_list = array($param_channel_id);
           else{
               //$channel_list = $this->channel_list;
               $channel_list = DB::getAll("select * from store");
               $channel_list = array_column($channel_list,'store_id');
           }    

           $pagesize = 300;           
		   foreach ($channel_list as $key1 => $channel_id) {
		   	   echo "start ....".$channel_id."<br>";
		   	   $sql_insert = "";
		   	   $k =0;
		   	   $sql = "select a.policy_id from base_salepolicy_info a,app_salepolicy_channel b where a.policy_id=b.policy_id and a.is_delete=0 and a.bsi_status=3 and b.channel='{$channel_id}'";
		   	   $check_channel = DB::getAll($sql);
		   	   if(empty($check_channel)){
		   	   	   echo "没有销售政策";
		   	   	   continue;
		   	   }


		   	   $store_info = DB::getRow2("select store_company_id,store_name from store where store_id='{$channel_id}'");
		   	   //print_r($store_info);
		   	   $store_company_id = !empty($store_info) ? $store_info['store_company_id'] : 0;
		   	   $store_name = !empty($store_info) ? $store_info['store_name'] : '';

		       $sql_goods_list = "select g.id,g.goods_id,g.goods_sn,g.goods_name,g.zuanshidaxiao,
					g.jietuoxiangkou,
					g.jietuoxiangkou as xiangkou,
					g.shoucun as finger,g.caizhi,g.yanse,1 as isXianhuo,1 as is_xianhuo,
					g.product_type,
					g.product_type as product_type,
					g.zhengshuleibie,
					g.cat_type,
					g.cat_type as category,
					g.mingyichengben,g.jijiachengben,g.update_time,g.warehouse,g.put_in_type,
					g.zhengshuhao,g.jinzhong,g.zuanshidaxiao as cart,g.qiegong as cut,g.jingdu as clarity,g.yanse as color,g.tuo_type,'0' as is_quick_diy,'1' as is_chengpin,g.company_id from goods_items g where is_on_sale=2 and company_id='{$store_company_id}' ";
			   if(!empty($param_goods_id))
                    $sql_goods_list .= " and g.goods_id='{$param_goods_id}'";
		       //$xianhuo_goods_list = DB::getAll($sql_goods_list);
		       //print_r($xianhuo_goods_list);
               
               $flag = 1;
               $page = 1;
               while($flag){				   
		               $start =0;
		               if($page<=0){
                            $start = 1;
                       }else{
                       	    $start = ($page - 1) * $pagesize + 1;
                       }     
		               $sql_goods_list_limit = $sql_goods_list ." limit ". ($start-1) .",". $pagesize; 
		               //echo $sql_goods_list_limit ;
				       $xianhuo_goods_list = DB::getAll($sql_goods_list_limit);
				       if(empty($xianhuo_goods_list))
				       	    $flag = false;
				       $page = $page+1 ;	
				       foreach ($xianhuo_goods_list as $key2 => $goods) {
				       	    $data=array();
				       	    $data[] = $goods;
				            //$res = $this->getpolicygoods($data,$channel_id,0,$this->getCaizhiList(),$this->getJinseList());
				            $policyGoodsModel = new app_salepolicy_goodsModel();
				            $goods_price = $policyGoodsModel->getXianhuoPrice($goods, $channel_id, $$goods['company_id'],$multi_price_outfile);
				            //print_r($res);        	    
				       	    if(!empty($goods_price) && $goods_price>0){
				              	    $sql_insert .= " (0,'{$goods['goods_id']}','{$channel_id}','{$goods_price}','{$date}'),";
				       	            $k++;		       	       
				       	    }else{
		                            if(!empty($unfind_price_outfile)){
		                            	file_put_contents($unfind_price_outfile, iconv("utf-8", "GB18030",$store_name.$channel_id.",".$goods['goods_id']."\n"),FILE_APPEND);
		                            }
				       	    }
				       	    $k2++;
				       	    echo $channel_id.'--'.$k2."\r\n<br>";
				       }
			    }   
			   if(!empty($sql_insert)){
			   	    $sql_delete = "delete from warehouse_goods_ishop_price where channel_id='{$channel_id}'";
			   	    if(!empty($param_goods_id))
			   	    	$sql_delete .=" and goods_id='{$param_goods_id}'";
			   	    DB::query($sql_delete);
			   	    $sql_insert = "insert into warehouse_goods_ishop_price values ".rtrim($sql_insert,',');
			   	    //echo $sql_insert;
			   	    DB::query($sql_insert);
			   	    //$note_string = $date.'--'.$channel_row['channel_name']."计算出销售价的商品共:".$k."\r\n<br>";
			   	    //echo $note_string;
			   	    //file_put_contents('cron_calculate_ishop_sale_price.log',$note_string,FILE_APPEND);
			   }       
		   }
		   
		   echo "<br>end";

    }

}	
?>	    