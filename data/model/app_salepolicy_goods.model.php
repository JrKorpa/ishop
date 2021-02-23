<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyGoodsModel.php
 *   @link		: 珂兰钻石 www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-08-28
 *   @update	:
 *  -------------------------------------------------
 */
class app_salepolicy_goodsModel extends Model{

    public function __construct(){
        parent::__construct('app_salepolicy_goods');
    }
    /**
     * 期货价格计算
     * @param unknown $goodsInfo  （list_style_goods表标准字段）
     * @param unknown $channel_id
     * @return Ambigous <number, mixed>
     */
    public function getQihuoPrice($goodsInfo,$channel_id){
        
        $tuo_type = $goodsInfo['xiangkou']==0?1:2; 
        $data = array(
            'id'=>$goodsInfo['goods_id'],
            'goods_id'=>$goodsInfo['goods_sn'],
            'goods_sn'=>$goodsInfo['style_sn'],
            'goods_name'=>$goodsInfo['style_name'],
            'xiangkou'=>$goodsInfo['xiangkou'],
            'finger'=>$goodsInfo['shoucun'],
            'caizhi'=>$goodsInfo['caizhi'],
            'product_type'=>$goodsInfo['product_type_id'],
            'cat_type'=>$goodsInfo['cat_type_id'],
            'stone'=>$goodsInfo['zhushizhong'],
            'mingyichengben'=>$goodsInfo['dingzhichengben'],
            'tuo_type'=>$tuo_type,
            'is_chengpin'=>0,
            'is_xianhuo'=>0,
        );
        //print_r($data);
        //echo $filter['channel_id'];
        $sdata = $this->getPolicyGoods([$data],$channel_id);
        //用于admin账号查看那个销售政策 policy_name---id
        //if($_COOKIE['username'] == 'admin')
        // print_r($sdata);
        if(empty($sdata[0]['sprice'])){
            $goods_price = 0;
        }else{
            $goods_price_list = array();
            foreach ($sdata[0]['sprice'] as $vo){
                $goods_price_list[$vo['sale_price']] = $vo;
            }
            krsort($goods_price_list);
            $goods_price_info = current($goods_price_list);
            $goods_price = $goods_price_info['sale_price'];
        }
        return $goods_price;
    }
    /**
     * 现货价格计算
     * @param unknown $goods_info （goods_items表标准字段）
     * @param unknown $store_id
     * @param unknown $company_id
     * @return Ambigous <number, string, mixed>
     */
    public function getXianhuoPrice($goods_info,$store_id,$company_id,$multi_price_outfile=null){
        $diamond_api = data_gateway('idiamond');
        if($goods_info['product_type'] == '钻石' && $goods_info['cat_type'] =='裸石'){
            //裸石价格计算
            $diaInfo = array(
                'carat'=>$goods_info['zuanshidaxiao'],
                'good_type'=>1,
                'cert'=>$goods_info['zhengshuleibie'],
                'pifajia'=>$goods_info['jijiachengben'],
            );
            $diamond_api->multiply_jiajialv($diaInfo, $store_id, $company_id);
            $goods_price = sprintf("%.2f",$diaInfo['shop_price']);
        }else{
            $goods_info['is_xianhuo'] = 1;
            if(!empty($goods_price['jietuoxiangkou'])){
               $goods_info['xiangkou'] = $goods_price['jietuoxiangkou'];
            }
            $sdata = $this->getPolicyGoods([$goods_info],$store_id);
            if(empty($sdata[0]['sprice'])){
                $goods_price = 0;
            }else{
                $goods_price_list = array();
                foreach ($sdata[0]['sprice'] as $vo){
                    $goods_price_list[$vo['sale_price']] = $vo;
                }
                krsort($goods_price_list);
                $goods_price_info = current($goods_price_list);
                $goods_price = $goods_price_info['sale_price'];
	            if(!empty($multi_price_outfile) && count($sdata[0]['sprice'])>1){
	                foreach ($sdata[0]['sprice'] as $vo){
	                    file_put_contents($multi_price_outfile, iconv("utf-8", "GB18030",$store_id.",". $vo['goods_id'] .",". $vo['id'] .",". $vo['policy_name'] .",". $vo['jiajia'] .",". $vo['sta_value'] .",". $vo['sale_price'] ."\n"),FILE_APPEND);
	                }	            	
	            }	           
            }
        }
        return $goods_price;
    }		
	//根据商品数据列表,匹配销售政策
	public function getPolicyGoods($data,$channel_id,$policy_id=0)
	{	
	    $goodsAttrModel = new goods_attributeModel();
	    $caizhi = $goodsAttrModel->getCaizhiList();
	    $yanse  = $goodsAttrModel->getJinseList();

	    //经销商款
        $sql_gold = "select style_sn from app_style_jxs WHERE  status = 1";
        $jxs_style_arr = DB::getAll($sql_gold);
        $jxs_style_sn_arr = array_values(array_column($jxs_style_arr,'style_sn'));

		foreach($data as $k=>$ginfo)	
		{
			//拿出产品线和款式分类去找销售政策
			$baoxianfei = 0;
			$product_type = $ginfo['product_type'];
			$cat_type = $ginfo['cat_type'];
			$is_chengpin = 0;
			//$xiangkou = $ginfo['jietuoxiangkou'];
			
			//如果是现货 首先算出保险费
			if($ginfo['is_xianhuo'] == 1){
			    $xiangkou = $ginfo['jietuoxiangkou'];
				//如果是经销商（只要大于0就ok）
				if($ginfo['jijiachengben']>0 )
				{
					$ginfo['mingyichengben'] = $ginfo['jijiachengben'];
				}
				if($xiangkou >0 )
				{
					$baoxian_xiankou = $xiangkou;
				}else{
					$baoxian_xiankou = $ginfo['zuanshidaxiao'];
					$ginfo['jietuoxiangkou'] = $ginfo['zuanshidaxiao'];
				}
				if($ginfo['tuo_type'] != 1){
					$baoxianfei = $this->getbaoxianfei($product_type,$baoxian_xiankou);
				}
			}else{
			    $xiangkou = $ginfo['xiangkou'];
			}
			
			$chengben = $ginfo['mingyichengben'] + $baoxianfei;
			
			$policy_list = array();
			//如果不是按款定价的,那么我们就走正常的销售政策(是否按款定价为否)
			$ginfo = $this->getYikoujia($ginfo,$caizhi,$policy_id,$channel_id);
			if(isset($ginfo['yikoujia']) && !empty($ginfo['yikoujia']))
			{
			    foreach($ginfo['yikoujia'] as $obj)
			    {
			        $tmp['goods_id'] = $ginfo['goods_id'];
			        $tmp['is_chengpin'] = $ginfo['is_chengpin'];
			        $tmp['goods_sn'] = $ginfo['goods_sn'];
			        $tmp['id'] = $obj['policy_id'];
			        $tmp['policy_name'] = $obj['policy_name'];
			        $tmp['chengben'] = $chengben;
			        $tmp['sale_price'] = $obj['price'];
			        $tmp['cert'] = $obj['cert'];
			        $tmp['color'] = $obj['color'];
			        $tmp['clarity'] = $obj['clarity'];
			        $tmp['tuo_type'] = $obj['tuo_type'];
			        $tmp['is_yikoujia'] = 1;
			        //array_push($policynames,$obj['policy_name']);
			        //array_push($saleprices,$tmp['sale_price']);
			       // array_push($policyids,$obj['policy_id']);
			        $policy_list[] = $tmp;
			    }
			    unset($ginfo['yikoujia']);
			    //如果找到了一口价,那么接着找满足条件的活动政策
			     //$ginfo = $this->getPolicyInfo($ginfo,$channel_id,$policy_id,1);
			    //不需要走下去了, 意味着这个商品是按款定价的东西,可以不用管了
			}else{
			    //如果不是按款定价的,那么我们就走正常的销售政策(是否按款定价为否)
			    $ginfo = $this->getPolicyInfo($ginfo,$channel_id,$policy_id,0);
			}

			if(isset($ginfo['putong_data']) && !empty($ginfo['putong_data']))
			{
				foreach($ginfo['putong_data'] as $policy)
				{   
				    //echo "{$chengben} * {$policy['jiajia']} + {$policy['sta_value']}";
				    if($policy['jiajia']<=0){
				        continue;
				    }

				    //定制销售价=（定制成本+保险费）*批发加价率*加价率+固定值
                    //批发加价率：系列款为1.21（系列款为经销商系列款里的款），非系列款为1.17
				    if($ginfo['is_xianhuo'] == 1){
                        $pf_jiajia = 1;
                    }elseif(!empty($ginfo['goods_sn']) && in_array($ginfo['goods_sn'],$jxs_style_sn_arr)){
				        $pf_jiajia = 1.21;
                    }else{
                        $pf_jiajia = 1.17;
                    }
				    $tmp['id'] = $policy['policy_id'];
					$tmp['goods_id'] = $ginfo['goods_id'];
					$tmp['goods_sn'] = $ginfo['goods_sn'];					
					$tmp['policy_name'] = $policy['policy_name'];
					$tmp['sale_price'] = round($chengben * $policy['jiajia']*$pf_jiajia) + $policy['sta_value'];
					$tmp['cert'] = $policy['cert'];//a.cert,a.color,a.clarity,a.tuo_type
					$tmp['color'] = $policy['color'];
					$tmp['clarity'] = $policy['clarity'];
					$tmp['tuo_type'] = $policy['tuo_type'];
					$tmp['chengben'] = $chengben;
					$tmp['jiajia'] = $policy['jiajia'];
					$tmp['sta_value'] = $policy['sta_value'];
					$policy_list[] = $tmp;
				}
				unset($ginfo['putong_data']);
			}else{
                //产品线为“普通黄金”的现货货品如果找不到一口价销售政策则按新的定价规则（销售价=当日金价*金重+工费*加价率）
                //如果有一口价销售政策 则按销售政策（即按目前方法定价）
                //产品线为“普通黄金”的现货货品如果找不到一口价销售政策则按新的定
                //价规则（销售价=当日金价*金重+工费*加价率）
                //当日金价：取黄金价格最后一条记录的价格，
                //金重：取商品列表金重，
                //工费：取商品列表买入工费（mairugongfei),
                //加价率：全国统一 取黄金价格最后一条记录的计价率
                //var_dump($ginfo);die;
                if($ginfo['product_type'] == '普通黄金' && empty($tmpobj)){
                    $gold_price = 0;
                    $gold_jiajialv = 0;
                    //销售价=当日金价*金重+工费*加价率
                    //当日金价and加价率
                    $sql_gold = "select gold_price,jiajialv from app_gold_jiajialv where is_usable = 1 order by id desc limit 1";
                    $gold_price_info = DB::getRow2($sql_gold);
                    //var_dump($gold_price_info);die;
                    if(!empty($gold_price_info)){
                        $gold_price = $gold_price_info['gold_price'];
                        $gold_jiajialv = $gold_price_info['jiajialv'];
                    }
                    $tmp['id'] = 0;
                    $tmp['goods_id'] = $ginfo['goods_id'];
                    $tmp['goods_sn'] = $ginfo['goods_sn'];                    
                    $tmp['policy_name'] = '普通黄金定价';
                    $tmp['sale_price'] = bcadd(bcmul($gold_price,$ginfo['jinzhong'],3),bcmul($ginfo['mairugongfei'],$gold_jiajialv,3),2);
                    $tmp['sale_price'] = round($tmp['sale_price']);
                    $tmp['chengben'] = $gold_price;
                    $tmp['jiajia'] = $gold_jiajialv;
                    $tmp['sta_value'] = 0;  
                    $policy_list[] = $tmp;
                }
            }			
			$data[$k] = $ginfo;
			$data[$k]['channel'] = $channel_id;
			if(!empty($policy_list))
			{
				$data[$k]['sprice']= $policy_list;
			}else{
				$data[$k]['sprice']= array();				
			}
		}
		return $data;
	}
	//判断是否为空,在调用前判断
	public function getYikoujia($ginfo,$caizhi,$policyid=0,$channelid=0)
	{   
	    if(isset($ginfo['jietuoxiangkou'])){
	        $ginfo['xiangkou'] = $ginfo['jietuoxiangkou'];
	    }
	    $goods_data = $ginfo;	    
	    $sql = " select a.policy_id,a.price,b.policy_name,b.jiajia,b.sta_value,a.cert,a.color,a.clarity,a.tuo_type
			from app_yikoujia_goods as a
			inner join base_salepolicy_info as b on a.policy_id=b.policy_id
			inner join app_salepolicy_channel as d on a.policy_id=d.policy_id
			where b.is_kuanprice=1 and b.is_delete=0 and b.bsi_status=3 and a.is_delete=0 and ";
	    //b.is_kuanprice=0 and b.is_delete=0 and b.bsi_status=3 增加销售政策的管控
	    //一口价也增加销售渠道的管控
	    if($channelid>0)
	    {
	        $sql .=" d.channel = $channelid and ";
	    }
	    if(isset($ginfo['is_xianhuo']))
	    {
	        $sql .=" a.isXianhuo ={$ginfo['is_xianhuo']} and ";
	    }
	    if($policyid>0)
	    {
	        $sql .=" a.policy_id = $policyid and ";
	    }
	    if(isset($ginfo['goods_id']) && $ginfo['goods_id'] !='')
	    {
	        //$sql .= " a.goods_id='{$ginfo['goods_id']}' and ";
	        $sql_one = $sql." a.goods_id>0 and a.goods_id='{$ginfo['goods_id']}' order by a.price desc limit 1";
	        $data = DB::getAll($sql_one);
	        if(!empty($data))
	        {
	            $goods_data['yikoujia'] = $data;
	            return $goods_data;
	        }
	    }
	    if(isset($ginfo['goods_sn']) && $ginfo['goods_sn'] !='')
	    {
	        //要排除掉 指定了货号的一口价
	        $sql .=" a.goods_sn='{$ginfo['goods_sn']}' and a.goods_id< 1 and ";
	    }
	    if(isset($ginfo['caizhi']) && $ginfo['caizhi'] !='')
	    {
	        $caizhiid = $ginfo['caizhi'];
	        if($ginfo['is_xianhuo']==1)
	        {
	            $caizhiid = $this->getCaizhiId($caizhi,$ginfo['caizhi']);
	        }
	        $sql .=" a.caizhi='{$caizhiid}' and ";
	    }
	    if(isset($ginfo['xiangkou']) && $ginfo['xiangkou'] !='')
	    {
	        $sql .=" a.small <= {$ginfo['xiangkou']} and a.sbig >= {$ginfo['xiangkou']}  and ";
	    }
	    if(isset($ginfo['color']) && $ginfo['color'] !=''){
	        $sql .=" a.color in ('全部','{$ginfo['color']}')  and ";
	    }
	    if(isset($ginfo['clarity']) && $ginfo['clarity'] !=''){
	        $sql .=" a.clarity in('全部','{$ginfo['clarity']}')  and ";
	    }
	    if(isset($ginfo['cert']) && $ginfo['cert'] !=''){
	        $sql .=" a.cert in('{$ginfo['cert']}','全部') and ";
	    }
	    if(isset($ginfo['shape']) && $ginfo['shape'] !=''){
	        //$sql .=" a.shape = '{$ginfo['shape']}'  and ";
	    }
	    //金托类型
	    if(isset($ginfo['tuo_type']))
	    {
	        $tuo_type = $ginfo['tuo_type'];
	        if($tuo_type==1){
	            $sql .=" a.tuo_type in (0,1) and ";
	        }else{
	            $sql .=" a.tuo_type in (0,2,3) and ";
	        }	        
	    }
	
	    $sql .= " 1 order by a.price desc limit 1";
	    $data = DB::getAll($sql);
	    //echo $sql."\r\n";
	    if(!empty($data))
	    {
	        $goods_data['yikoujia'] = $data;
	    }else{
	        $goods_data['yikoujia'] = array();
	    }
	    return $goods_data;
	}
	
	
	//根据货品属性拿取销售政策(活动的销售政策,和默认的销售政策,非按款定价的政策)
	//$ginfo 是否为空,在调用方法之前去做
	//告诉我们是否只取活动的  非默认的
	public function getPolicyInfo($ginfo,$channel_id,$policy_id=0,$is_active=0)
	{
		$goods_data = $ginfo;
		$time = date('Y-m-d');
		$sql = " 
		select a.policy_id,a.policy_name,a.jiajia,a.sta_value,a.range_begin,a.range_end,a.cert,a.color,a.clarity,a.tuo_type 
		from base_salepolicy_info as a 
		left join app_salepolicy_channel as b on a.policy_id=b.policy_id   
		where a.is_kuanprice=0 and a.is_delete=0 and a.bsi_status=3 and  
		a.policy_start_time <= '".$time."' and a.policy_end_time >= '".$time."' ";
		
		if(!empty($ginfo['product_type1'])){
		    $ginfo['product_type'] = $ginfo['product_type1'];
		}
		if(!empty($ginfo['cat_type1'])){
		    $ginfo['cat_type'] = $ginfo['cat_type1'];
		}
		if($is_active>0){
		    $sql .=" and a.is_default !=1";
		}else{
		    $sql .=" and a.is_default =1";
		}
		//如果是满足了按款定价的之后,那么只需要找出活动的销售政策即可
		if(empty($ginfo['is_xianhuo']))
		{
			if(isset($ginfo['product_type'])&& $ginfo['product_type'] != '')
			{
				//产品线id
				$sql .= " and a.product_type_id in(0,1,{$ginfo['product_type']}) ";
			}
			if(isset($ginfo['cat_type'])&& $ginfo['cat_type'] != '')
			{
				//款式分类id
				$sql .= " and a.cat_type_id in(0,1,{$ginfo['cat_type']}) ";
			}
			
			if(isset($ginfo['xiangkou']) && $ginfo['xiangkou'] !='')
			{
				//镶口范围
				$xiangkou = $ginfo['xiangkou'];
				$sql .= " and $xiangkou >= a.range_begin and $xiangkou <= a.range_end ";
			}
			
		    if(!empty($ginfo['zhengshuleibie']))
		    {
		        $zslb = $ginfo['zhengshuleibie'];
		        $sql .=" and (a.cert='全部类型' or a.cert regexp '{$zslb}' ) ";
		    }			   
				
			if(empty($ginfo['is_chengpin']) && !empty($ginfo['color'])){
			    $sql .=" and (a.color='全部' or a.color regexp '{$ginfo['color']}')";
			}
			if(empty($ginfo['is_chengpin']) && !empty($ginfo['clarity'])){
			    $sql .=" and (a.clarity='全部' or a.clarity regexp '{$ginfo['clarity']}')";
			} 
   			//期货目前只针对空托和空托女戒,政策货品类型为期货或者全部
    		//$sql .=" and a.tuo_type in(0,2,3) and a.huopin_type in(0,2) ";			
			$sql .=" and a.huopin_type in(0,2) ";
		}else{
			//现货
			if(isset($ginfo['product_type'])&& $ginfo['product_type'] != '')
			{
				//产品线
				$sql .= " and a.product_type in('全部','','{$ginfo['product_type']}') ";
			}
			if(isset($ginfo['cat_type'])&& $ginfo['cat_type'] != '')
			{
				//款式分类
				$sql .= " and a.cat_type in('全部','','{$ginfo['cat_type']}') ";
			}
			
			if(isset($ginfo['jietuoxiangkou']) && $ginfo['jietuoxiangkou'] !=='')
			{
				//镶口范围
				$xiangkou = $ginfo['jietuoxiangkou'];
				$sql .= " and $xiangkou >= a.range_begin and $xiangkou <= a.range_end ";
			}
			if(isset($ginfo['zuanshidaxiao']) && $ginfo['zuanshidaxiao'] !=='')
			{
				$zuanshidaxiao = $ginfo['zuanshidaxiao'];
				$sql .= " and $zuanshidaxiao >= a.zhushi_begin and $zuanshidaxiao <= a.zhushi_end ";
			}
			//现货   政策货品类型为现货或者全部
			$sql .=" and a.huopin_type in(1,2) ";
			
			//现货再追加一个证书类型
			if(isset($ginfo['zhengshuleibie']))
			{
				if(empty($ginfo['zhengshuleibie']))
				{
					$zslb = '无';
				}else{
					$zslb = $ginfo['zhengshuleibie'];
				}
				$sql .=" and (a.cert='全部类型' or a.cert regexp '{$zslb}' ) ";
			}
		}
		if(isset($ginfo['tuo_type'])){
    		if($ginfo['tuo_type']==1){
    		    $sql .=" and a.tuo_type in(0,1)";
    		}else{
    		    $sql .=" and a.tuo_type in(0,2,3)";
    		}
		}
		//追加一个根据款而定的系列
		if(!empty($ginfo['goods_sn'])){
    		$xilie = $this->getxilie($ginfo['goods_sn']);
   		    $sql.=" and ( a.xilie='全部系列' or a.xilie regexp '{$xilie}' ) ";
		}		
		//echo $sql;die();	
		if($policy_id>0)
		{
			$sql .= " and a.policy_id = $policy_id ";
		}
		$sql .=" and b.channel= $channel_id order by a.jiajia desc,a.sta_value desc ";
		//echo $sql;
		$data = DB::getAll($sql);
		if(!empty($data))
		{
			$goods_data['putong_data'] = $data;
		}else{
			//否则的话 就没有找到销售政策
			$goods_data['putong_data'] = array();
		}
		return $goods_data;
		//如果没有满足按款定价的话,那么需要找出活动的销售政策和默认的销售政策
	}
		
	//定义一个函数用来返回材质id
	public function getCaizhiId($caizhi,$caizhiname)
	{
		if(empty($caizhi) || $caizhiname ==''){
			return 0;	
		}
		
		foreach($caizhi as $k=>$v)
		{
			if(strpos($caizhiname,$v) !== false)
			{
				return $k;
			}
		}
	}
	//定义一个函数用来返回颜色id
	public function getYanseId($caizhi,$yanse,$caizhiname)
	{
		if(empty($caizhi) || empty($yanse) || $caizhiname ==''){
			return 0;	
		}
		foreach($caizhi as $k=>$v)
		{
			if(strpos($caizhiname,$v) !== false)
			{
				$caizhiname = str_replace($v,'',$caizhiname);
				break;
			}
		}
		foreach($yanse as $k=>$v)
		{
			if(trim($caizhiname) === trim($v))
			{
				return $k;
			}
		}
	}
	
	
	//为了下单那里现货用的是产品线的id，款式分类也是用的id
	public function getproductid($pname='')
	{
		if($pname=='')
		{
			return 0;
		}
		$sql = "select product_type_id from app_product_type where product_type_status=1 and product_type_name='{$pname}'";
		$pid = DB::getOne($sql);
		return $pid;
	}
	
	public function getcatid($cname='')
	{
		if($cname=='')
		{
			return 0;
		}
		$sql = "select cat_type_id from  app_cat_type where cat_type_status=1 and cat_type_name='{$cname}'";
		$cid = DB::getOne($sql);
		return $cid;
	}
	//拿取保险费
	public function getbaoxianfei($producttype,$xiangkou)
	{
		//定义所有需要加保险费用的产品线
		$allproducttype = array('钻石','珍珠','珍珠饰品','翡翠','翡翠饰品','宝石','宝石饰品','钻石饰品','宝石饰品','宝石');
		//定义保险费默认值
		$baoxianfei = 0;
		//判断是否需要拿取保险费 (镶嵌类的现货,拖类型)
		if(in_array($producttype,$allproducttype))
		{
			//拿取保险费
			$xiangkou = $xiangkou * 10000;
			$i = 0;
			$j = 0;
			$k = 0;
			$sql = 'SELECT `id`,`min`,`max`,`price`,`status` FROM `app_style_baoxianfee` WHERE 1';
			$data = DB::getAll($sql);
			foreach($data as $v)
			{
				$max[$i] = $v['max'] * 10000;
				$min[$j] = $v['min'] * 10000;
				$fee[$k] = $v['price'];
				$i++;$j++;$k++; 
			}
			$count = count($max);
			for($i = 0; $i <$count; $i ++) 
			{
				if ($xiangkou >= $min[$i] && $xiangkou <= $max[$i])
				{
					return $fee[$i];
				}
			}
		}
		return $baoxianfei;
	}
	public function getxilie($gsn='')
	{
	    if($gsn=='')
	    {
	        return '空白';
	    }
	    $sql = "select xilie from base_style_info where check_status=3 and style_sn='{$gsn}'";
	    $xilieid = DB::getOne($sql);
	    if(empty($xilieid))
	    {
	        return '空白';
	    }else{
	        $allid = array_filter(explode(',',$xilieid));
	        $xilieids = implode(',',$allid);
	        $sqlone = "select name from app_style_xilie where id in({$xilieids})";
	        $allxilie = DB::getAll($sqlone);
	        if(!empty($allxilie))
	        {
	            $xiliename = array_column($allxilie,'name');
	            if(count($xiliename)>1)
	            {
	                $xilie_name = implode('|',$xiliename);
	            }else{
	                $xilie_name = $xiliename[0];
	            }
	            return $xilie_name;
	        }else{
	            return '空白';
	        }
	    }
	}	  	

}
    


?>